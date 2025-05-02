<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\FootballMatch; // Corrected model name
use App\Models\Section;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage; // Useful if storing QR codes in storage
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Added for logging errors

class TicketController extends Controller
{

    // Cette méthode 'create' semble être une alternative ou une ancienne version.
    // La logique de création via le plan interactif utilise la méthode 'store'.
    // Vous pouvez la garder si elle est utilisée ailleurs, sinon, envisagez de la supprimer ou la renommer.
    public function create(Request $request, FootballMatch $match)
    {
        // Assuming this method is used for a different flow, like a simple "buy now" button without section selection
        $validated = $request->validate([
             // Maybe validate a default section ID or the first available one
            'quantity' => 'required|integer|min:1|max:10',
            // You might need a section_id parameter here too
            // 'section_id' => 'required|string|exists:sections,section_id',
        ]);

         // This method's logic needs to be adapted based on how it's actually used.
         // The logic below is very similar to 'store' and might be redundant.
         // For now, keeping the original logic but with corrected model/pivot references.

         // Find the default/first section for this match (example)
         $matchSection = $match->sections()->orderBy('id')->first(); // Get the first associated section pivot
         if (!$matchSection) {
             return redirect()->back()->with('error', 'No sections available for this match.');
         }

         $section = Section::findOrFail($matchSection->section_id); // Get actual section details

         // Check if there are enough seats available in this section
         if ($matchSection->available_seats < $validated['quantity']) {
             return redirect()->back()->with('error', 'No available seats in this section.');
         }

         // Generate QR Code directory
         $qrDir = public_path('qrcodes');
         if (!file_exists($qrDir)) {
             mkdir($qrDir, 0775, true);
         }

         $tickets = [];
         for ($i = 0; $i < $validated['quantity']; $i++) {
             $ticketNumber = 'TIX-' . strtoupper(Str::random(10));
             while (Ticket::where('ticket_number', $ticketNumber)->exists()) {
                 $ticketNumber = 'TIX-' . strtoupper(Str::random(10));
             }

             $seatNumber = $section->section_id . '-' . ($matchSection->capacity - $matchSection->available_seats + $i + 1);

             $ticket = Ticket::create([
                 'user_id' => Auth::id(),
                 'match_id' => $match->id,
                 'section_id' => $section->id, // Use actual section ID
                 'seat_number' => $seatNumber,
                 'price' => $matchSection->price, // Use price from pivot
                 'status' => 'confirmed', // Assuming purchase means confirmed
                 'ticket_number' => $ticketNumber,
                 'qr_code' => null, // Path will be updated
             ]);

             $qrData = json_encode(['type' => 'ticket', 'ticket_number' => $ticket->ticket_number, 'user_id' => $ticket->user_id]);
             $qrPathRelative = 'qrcodes/' . $ticket->ticket_number . '.svg';
             $qrPathFull = public_path($qrPathRelative);

             try {
                 QrCode::format('svg')->size(300)->errorCorrection('H')->generate($qrData, $qrPathFull);
                 if (file_exists($qrPathFull)) {
                     $ticket->update(['qr_code' => $qrPathRelative]);
                 } else {
                     Log::error("QR Code file not created: " . $qrPathFull);
                     $ticket->update(['status' => 'qr_error']);
                 }
             } catch (\Exception $e) {
                 Log::error("QR Code Generation Failed for ticket " . $ticket->ticket_number . ": " . $e->getMessage());
                 $ticket->update(['status' => 'qr_error']);
             }

             $tickets[] = $ticket;
         }

         // Decrease available seats on the pivot table
         $matchSection->decrement('available_seats', $validated['quantity']);
         // Also update the match's overall available tickets
         if ($match->available_tickets >= $validated['quantity']) {
             $match->decrement('available_tickets', $validated['quantity']);
         }

         // Create payment record
         if (!empty($tickets)) {
             Payment::create([
                 'user_id' => Auth::id(),
                 'ticket_id' => $tickets[0]->id,
                 'amount' => $matchSection->price * $validated['quantity'],
                 'payment_method' => 'create_flow', // Example method
                 'transaction_id' => 'CRT-' . Str::random(10),
                 'status' => 'completed',
             ]);
         }


         // Redirect to the show page of the first created ticket
         if (!empty($tickets)) {
             return redirect()->route('tickets.show', $tickets[0])
                 ->with('success', 'Tickets purchased successfully! See your E-Ticket.');
         } else {
              return back()->with('error', 'Failed to create tickets.');
         }
    }

    // This method displays a single ticket - MODIFIED TO RENDER PDF VIEW
    public function show(Ticket $ticket)
    {
        // Ensure current user can only view their own tickets
        if ($ticket->user_id !== Auth::id() && (!Auth::user() || !Auth::user()->isAdmin())) {
            abort(403, 'Unauthorized action.');
        }

        // Make sure the ticket has the related models loaded
        $ticket->load(['match', 'section']);

        // Return the view that will embed the PDF (tickets.show.blade.php)
        // Pass the ticket object to the view
        return view('tickets.show', compact('ticket'));
    }

    // This method displays the list of all tickets for the user - NOW RENDERED by tickets.index.blade.php
    public function index()
    {
        // Load all tickets for the authenticated user
        // Added Auth::check() guard
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your tickets.');
        }

        $tickets = Auth::user()->tickets()
            ->with(['match', 'payment', 'section']) // Eager load relationships
            ->latest() // Order by latest tickets
            ->get(); // Get all tickets

        // Return the view that lists tickets - assuming this is tickets.index now
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Handles ticket purchase after payment (simulation) - This method is called by your JS form
     */
    public function store(Request $request)
    {
        // Validate incoming request data from the form
        $request->validate([
            'match_id' => 'required|exists:football_matches,id', // Use 'football_matches' table name
            'quantity' => 'required|integer|min:1|max:10',
            'section_id' => 'required|string',  // Validate the section_id string identifier
        ]);

        $match = FootballMatch::findOrFail($request->match_id);

        // Find the section details from the 'sections' table using the string identifier
        $section = Section::where('section_id', $request->section_id)->firstOrFail();

        // Find the specific relationship record between the match and the section (the pivot table entry)
        $matchSection = $match->sections()
            ->where('section_id', $request->section_id) // Filter by the string section_id on the pivot
            ->where('is_active', true) // Ensure the section is active for this match
            ->firstOrFail(); // Get the pivot record (which has available_seats and capacity for *this match*)


        // Check if there are enough seats available in the section for THIS match
        if ($matchSection->available_seats < $request->quantity) {
             Log::warning("Purchase attempt failed: Not enough seats for match " . $match->id . ", section " . $request->section_id . ". Requested: " . $request->quantity . ", Available: " . $matchSection->available_seats);
            return back()->with('error', 'Not enough seats available in this section for this match.');
        }

        // Prepare directory for QR codes
        $qrDir = public_path('qrcodes');
        if (!file_exists($qrDir)) {
            // 0775 permissions, true for recursive creation
            if (!mkdir($qrDir, 0775, true) && !is_dir($qrDir)) {
                 Log::error("Failed to create QR codes directory: " . $qrDir);
                 return back()->with('error', 'Failed to create QR codes directory.');
            }
        } else if (!is_writable($qrDir)) {
             Log::error("QR codes directory is not writable: " . $qrDir);
             return back()->with('error', 'QR codes directory is not writable.');
        }


        $tickets = [];
        // Loop to create multiple tickets if quantity is > 1
        for ($i = 0; $i < $request->quantity; $i++) {

            // Calculate the seat number based on the state BEFORE decrementing available_seats
            // If available_seats for this match+section is 100 and capacity is 200, the next seat is 200 - 100 + 1 = 101
            // For the i-th ticket (starting at i=0), the seat number offset is $i
            $seatNumber = $section->section_id . '-' . ($matchSection->capacity - $matchSection->available_seats + $i + 1);


            // Generate a unique ticket number
            $ticketNumber = 'TIX-' . strtoupper(Str::random(10));
            // Optional: Add a loop to regenerate if it exists, though unlikely for random(10)
             while (Ticket::where('ticket_number', $ticketNumber)->exists()) {
                 $ticketNumber = 'TIX-' . strtoupper(Str::random(10));
             }


            // Create the Ticket record in the database
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'match_id' => $match->id,
                'section_id' => $section->id, // Use the actual section ID from the 'sections' table
                'seat_number' => $seatNumber,
                'price' => $matchSection->price, // Use the price from the pivot table
                'status' => 'confirmed', // Mark as confirmed directly after payment sim
                'ticket_number' => $ticketNumber,
                // qr_code path will be updated after generation
                'qr_code' => null, // Set initially to null
            ]);

             // Génération des données du QR Code (SANS ÉMOJIS, EN UTF-8)
             // Include ticket ID or number in data for verification
             $qrData = json_encode([
                 'type' => 'ticket',
                 'ticket_number' => $ticket->ticket_number,
                 'user_id' => $ticket->user_id,
                 'match' => $match->home_team . ' vs ' . $match->away_team,
                 'section' => $section->name . ' (' . $section->section_id . ')', // Include both name and string ID
                 'seat' => $ticket->seat_number,
                 'date' => $match->match_date->format('Y-m-d H:i'),
                 'price' => $ticket->price,
             ]);


            // Générer et enregistrer le QR Code au format SVG (vectoriel et scalable)
            $qrFileName = $ticket->ticket_number . '.svg'; // Use ticket number for filename
            $qrPathRelative = 'qrcodes/' . $qrFileName; // Path relative to public directory
            $qrPathFull = public_path($qrPathRelative); // Full server path

            try {
                // Use encoding('UTF-8') explicitly
                QrCode::format('svg')
                      ->size(300) // Size in pixels for SVG
                      ->errorCorrection('H') // Error correction level (High)
                      ->encoding('UTF-8') // Explicitly set encoding
                      ->generate($qrData, $qrPathFull); // Generate and save

                 // Verify file exists after generation
                 if (file_exists($qrPathFull)) {
                      // Mettre à jour le ticket avec le chemin RELATIF du QR Code
                      $ticket->update(['qr_code' => $qrPathRelative]);
                 } else {
                      // This should not happen if generate didn't throw, but as a double check
                      throw new \Exception("QR Code file not found after generation attempt: " . $qrPathFull);
                 }


            } catch (\Exception $e) {
                \Log::error("QR Code Generation Failed for ticket " . $ticket->ticket_number . ": " . $e->getMessage());
                // Mark ticket as having QR error or delete it
                // You might want to handle this more gracefully, e.g., mark as pending review
                $ticket->update(['status' => 'qr_error']);
                // Stop creating further tickets for this batch or rollback the transaction
                 // For simplicity here, we just log and continue, but in production, handle the error properly
            }


            $tickets[] = $ticket; // Add the created ticket to the array
        }

        // Update section availability on the pivot table
        // Only decrement if tickets were actually created
        if (!empty($tickets)) {
             $matchSection->decrement('available_seats', count($tickets));

             // Also update the match's overall available tickets (if you track this)
             // Assuming match->available_tickets is on the football_matches table
             if ($match->available_tickets >= count($tickets)) {
                 $match->decrement('available_tickets', count($tickets));
             }
        }


        // === MODIFICATION MAJEURE ICI ===
        // Redirect to the SHOW page of the first created ticket
        // Pass the first ticket object to the route helper
        if (!empty($tickets)) {
             // Create a simple payment record linked to the first ticket (if needed)
             // Adjust payment logic based on your actual payment flow
             if (Auth::check()) { // Ensure user is logged in
                 try {
                     Payment::create([
                         'user_id' => Auth::id(),
                         'ticket_id' => $tickets[0]->id, // Link payment to the first ticket of the batch
                         'amount' => $matchSection->price * count($tickets), // Calculate total price based on actual tickets created
                         'payment_method' => 'arduino_simulated', // Example method
                         'transaction_id' => 'SIM-' . Str::random(10), // Simulated transaction ID
                         'status' => 'completed',
                     ]);
                 } catch (\Exception $e) {
                     Log::error("Failed to create Payment record after ticket purchase: " . $e->getMessage());
                     // Decide how to handle this: maybe mark tickets as needing manual payment verification
                 }
             }

             // Redirect to the 'show' route for the first ticket
             return redirect()->route('tickets.show', $tickets[0])->with('success', 'Réservation réussie ! Voici votre E-Ticket.');
        } else {
            // Should not happen if quantity is >= 1 and validation/stock check passes and QR codes don't fail catastrophically
             Log::error("Ticket creation loop finished but \$tickets array is empty. Match: " . $match->id . ", Section: " . $request->section_id . ", Qty: " . $request->quantity);
            return back()->with('error', 'Erreur: Aucun ticket n\'a pu être créé.');
        }
        // === FIN DE MODIFICATION ===
    }


    /**
     * Generates and downloads or streams the ticket PDF.
     * Modified to accept 'inline' query parameter.
     */
    public function download(Ticket $ticket, Request $request) // Add Request parameter
    {
        if (!auth()->check()) {
            return abort(403, "Utilisateur non authentifié.");
        }

        if ($ticket->user_id !== auth()->id()) {
            return abort(403, "Ce ticket ne vous appartient pas !");
        }

        if ($ticket->status !== 'confirmed') {
            return abort(403, "Seuls les tickets confirmés peuvent être téléchargés ou visualisés !");
        }

        // Ensure ticket relationships are loaded for the PDF view
         $ticket->load(['match', 'section']);

        // Vérifier que le fichier QR Code existe
        // Use Storage facade if you are storing QR codes in storage/app/public
        // If storing in public/, just use public_path
        $qrPathFull = public_path($ticket->qr_code);

        // Check if the qr_code path is valid and the file exists
        if (empty($ticket->qr_code) || !file_exists($qrPathFull)) {
             Log::error("QR Code file not found or path is empty for ticket ID: " . $ticket->id . ", Path: " . $ticket->qr_code);
             // You might want to regenerate or return a specific error view
             return response("QR Code not found for this ticket. Please contact support.", 404);
        }

        // Encoder l'image en base64 pour l'inclure dans le PDF
        // Check if file reading is successful
        $qrCodeContent = file_get_contents($qrPathFull);
         if ($qrCodeContent === false) {
             Log::error("Failed to read QR Code file: " . $qrPathFull);
             return response("Failed to read QR Code content.", 500);
         }
        // Use svg+xml type for SVG QR codes
        $qrBase64 = "data:image/svg+xml;base64," . base64_encode($qrCodeContent);

        // Générer le PDF
        // Make sure 'tickets.pdf' view exists and is structured to use $ticket and $qrBase64
        // Example: resources/views/tickets/pdf.blade.php
        $pdf = Pdf::loadView('tickets.pdf', compact('ticket', 'qrBase64'))->setPaper('a4', 'portrait');

        // Check if the 'inline' query parameter is present in the request
        if ($request->query('inline')) {
            // Serve the PDF directly in the browser
            // Use ->stream() method
            return $pdf->stream('Ticket-' . $ticket->ticket_number . '.pdf');
        } else {
            // Force download (default behavior)
            // Use ->download() method
            return $pdf->download('Ticket-' . $ticket->ticket_number . '.pdf');
        }
    }


    // This method is named 'purchase' but your JS form submits to 'store'.
    // This method might be unused, or used by a different purchase flow.
    // Review your routes (web.php) to confirm which method is used.
    // If 'purchase' is used for the JS form, you need to apply the redirection logic from 'store' here.
    // Keeping this method here as it was in your provided code, assuming it might be used elsewhere.
    public function purchase(Request $request, FootballMatch $match)
    {
        // ... (Existing purchase logic as you provided) ...
        // WARNING: If your JS form posts to this route, you need to change the redirection logic here
        // to redirect to tickets.show as done in the 'store' method above.
         Log::warning("TicketController@purchase method called. Check if this is intended.");

        $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the section - Corrected to use the relationship and get pivot data
         $matchSection = $match->sections()->where('section_id', $request->section_id)->firstOrFail();
         $section = Section::findOrFail($matchSection->section_id); // Get actual section details

        // Check if there are enough seats available on the pivot
        if ($matchSection->available_seats < $request->quantity) {
             Log::warning("Purchase attempt failed (purchase method): Not enough seats for match " . $match->id . ", section " . $request->section_id . ". Requested: " . $request->quantity . ", Available: " . $matchSection->available_seats);
            return redirect()->back()->with('error', 'Not enough seats available in this section.');
        }

        $tickets = [];
        $totalPrice = $matchSection->price * $request->quantity; // Use price from pivot

        // Prepare QR directory
         $qrDir = public_path('qrcodes');
         if (!file_exists($qrDir)) {
             mkdir($qrDir, 0775, true);
         }


        for ($i = 0; $i < $request->quantity; $i++) {
            $ticketNumber = 'TIX-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            while (Ticket::where('ticket_number', $ticketNumber)->exists()) {
                 $ticketNumber = 'TIX-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            }

            $seatNumber = $section->section_id . '-' . ($matchSection->capacity - $matchSection->available_seats + $i + 1);

            $ticket = new Ticket([
                'user_id' => auth()->id(),
                'match_id' => $match->id,
                'section_id' => $section->id, // Link to the sections table ID
                'seat_number' => $seatNumber,
                'price' => $matchSection->price, // Use price from pivot
                'status' => 'confirmed',
                'ticket_number' => $ticketNumber,
                'qr_code' => null, // Will update after saving
            ]);

            $ticket->save(); // Save first to get an ID

            // Generate and save QR Code
             $qrData = json_encode([
                 'type' => 'ticket',
                 'ticket_number' => $ticket->ticket_number,
                 // Add other relevant details...
             ]);
             $qrPathRelative = 'qrcodes/' . $ticket->ticket_number . '.svg';
             $qrPathFull = public_path($qrPathRelative);

             try {
                  QrCode::format('svg')->size(300)->errorCorrection('H')->encoding('UTF-8')->generate($qrData, $qrPathFull);
                   if (file_exists($qrPathFull)) {
                       $ticket->update(['qr_code' => $qrPathRelative]);
                   } else {
                       throw new \Exception("QR Code file not found after generation attempt: " . $qrPathFull);
                   }
             } catch (\Exception $e) {
                 Log::error("QR Code Generation Failed (purchase method) for ticket " . $ticket->ticket_number . ": " . $e->getMessage());
                 $ticket->update(['status' => 'qr_error']);
             }


            $tickets[] = $ticket;
        }

        // Update section available seats on the pivot
        $matchSection->decrement('available_seats', count($tickets));
         // Also update match available tickets if tracked
         if ($match->available_tickets >= count($tickets)) {
             $match->decrement('available_tickets', count($tickets));
         }


        // Create payment record (link to first ticket if creating multiple)
        if (!empty($tickets)) {
             try {
                 Payment::create([
                     'user_id' => auth()->id(),
                     'ticket_id' => $tickets[0]->id, // Link to the first ticket
                     'amount' => $totalPrice, // Use total price from pivot
                     'payment_method' => 'credit_card', // Method used by this flow?
                     'transaction_id' => 'TRX-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
                     'status' => 'completed',
                 ]);
             } catch (\Exception $e) {
                 Log::error("Failed to create Payment record (purchase method) after ticket purchase: " . $e->getMessage());
             }
        }


        // === REDIRECTION LOGIC FOR THIS METHOD (if used) ===
        // If this method is the one the JS form posts to,
        // CHANGE this redirection line:
        // return redirect()->route('my-tickets')->with('success', 'Tickets purchased successfully!');

        // TO this:
         if (!empty($tickets)) {
              return redirect()->route('tickets.show', $tickets[0])->with('success', 'Tickets purchased successfully!');
         } else {
              return back()->with('error', 'Failed to create tickets.');
         }
         // === END REDIRECTION LOGIC ===
    }
}