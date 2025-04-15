<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\FootballMatch;
use App\Models\Section;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{

    public function create(Request $request, FootballMatch $match)
    {
        $validated = $request->validate([
            'selected_section_id' => 'required|string',
        ]);

        $sectionId = $validated['selected_section_id'];
        $section = $match->sections()->where('section_id', $sectionId)->firstOrFail();

        // Check if there are available seats in this section
        if ($section->available_seats <= 0) {
            return redirect()->back()->with('error', 'No available seats in this section.');
        }

        // Generate unique ticket number
        $ticketNumber = strtoupper(Str::random(10));

        // Create the ticket
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'match_id' => $match->id,
            'section_id' => $section->id,
            'seat_number' => $sectionId . '-' . ($section->capacity - $section->available_seats + 1),
            'price' => $section->price,
            'status' => 'pending',
            'ticket_number' => $ticketNumber,
            'qr_code' => 'QR-' . $ticketNumber,
        ]);

        // Decrease available seats
        $section->decrement('available_seats');

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket reserved successfully! Please complete payment.');
    }

    public function show(Ticket $ticket)
    {
        // Ensure current user can only view their own tickets
        if ($ticket->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }

    public function index()
    {
        $tickets = Auth::user()->tickets()
            ->with(['match', 'payment', 'section'])
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'quantity' => 'required|integer|min:1|max:10',
            'section_id' => 'required|string',  // Add validation for section_id
        ]);

        $match = FootballMatch::findOrFail($request->match_id);

        // Find the section
        $section = $match->sections()
            ->where('section_id', $request->section_id)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if there are enough seats available in the section
        if ($section->available_seats < $request->quantity) {
            return back()->with('error', 'Not enough seats available in this section.');
        }

        // Assurer que le dossier public/qrcodes existe
        $qrDir = public_path('qrcodes');
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0775, true);
        }

        $tickets = [];
        for ($i = 0; $i < $request->quantity; $i++) {
            // Create seat number based on section ID and position
            $seatNumber = $section->section_id . '-' . ($section->capacity - $section->available_seats + $i + 1);

            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'match_id' => $match->id,
                'section_id' => $section->id,
                'seat_number' => $seatNumber,
                'price' => $section->price,
                'status' => 'confirmed',
                'ticket_number' => 'TIX-' . Str::random(10),
            ]);

            // Génération des données du QR Code (SANS ÉMOJIS, EN UTF-8)
            $qrData = utf8_encode(
                "Ticket Information\n"
                    . "Match: " . $match->home_team . " vs " . $match->away_team . "\n"
                    . "Date: " . $match->match_date->format('F j, Y g:i A') . "\n"
                    . "Stadium: " . $match->stadium . "\n"
                    . "Section: " . $section->name . " (ID: " . $section->section_id . ")\n"
                    . "Seat: " . $seatNumber . "\n"
                    . "Ticket Number: " . $ticket->ticket_number . "\n"
                    . "Status: " . ucfirst($ticket->status)
            );

            // Générer et enregistrer le QR Code
            $qrPath = 'qrcodes/' . $ticket->ticket_number . '.svg';
            QrCode::format('svg')->size(300)->encoding('UTF-8')->generate($qrData, public_path($qrPath));


            // Vérifier si le fichier QR Code a bien été généré
            if (!file_exists(public_path($qrPath))) {
                dd("Erreur : Impossible de générer le QR Code !");
            }

            // Mettre à jour le ticket avec le chemin du QR Code
            $ticket->update(['qr_code' => $qrPath]);
            $tickets[] = $ticket;
        }

        // Update section availability
        $section->decrement('available_seats', $request->quantity);

        // Also update the match's overall available tickets
        $match->decrement('available_tickets', $request->quantity);

        return redirect()->route('my-tickets')->with('success', 'Booking successful! Check your tickets below.');
    }


    public function download(Ticket $ticket)
    {
        if (!auth()->check()) {
            return abort(403, "Utilisateur non authentifié.");
        }

        if ($ticket->user_id !== auth()->id()) {
            return abort(403, "Ce ticket ne vous appartient pas !");
        }

        if ($ticket->status !== 'confirmed') {
            return abort(403, "Seuls les tickets confirmés peuvent être téléchargés !");
        }

        // Vérifier que le fichier QR Code existe
        $qrPath = public_path($ticket->qr_code);
        if (!file_exists($qrPath)) {
            return abort(404, "Le fichier QR Code n'existe pas !");
        }

        // Encoder l'image en base64 pour l'inclure dans le PDF
        $qrBase64 = "data:image/png;base64," . base64_encode(file_get_contents($qrPath));

        // Générer le PDF
        $pdf = Pdf::loadView('tickets.pdf', compact('ticket', 'qrBase64'))->setPaper('a4', 'portrait');

        return $pdf->download('Ticket-' . $ticket->ticket_number . '.pdf');
    }

    /**
     * Purchase tickets for a specific section
     */
    public function purchase(Request $request, FootballMatch $match)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the section
        $section = $match->sections()->where('section_id', $request->section_id)->firstOrFail();

        // Check if there are enough seats available
        if ($section->available_seats < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough seats available in this section.');
        }

        // Create tickets
        $tickets = [];
        $totalPrice = $section->price * $request->quantity;

        for ($i = 0; $i < $request->quantity; $i++) {
            // Generate a unique ticket number
            $ticketNumber = 'TIX-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

            // Generate a QR code for the ticket
            $qrCode = 'QR-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));

            // Create ticket
            $ticket = new Ticket([
                'user_id' => auth()->id(),
                'match_id' => $match->id,
                'section_id' => $section->id,
                'seat_number' => $section->section_id . '-' . ($section->capacity - $section->available_seats + $i + 1),
                'price' => $section->price,
                'status' => 'confirmed',
                'ticket_number' => $ticketNumber,
                'qr_code' => $qrCode,
            ]);

            $ticket->save();
            $tickets[] = $ticket;
        }

        // Update section available seats
        $section->update([
            'available_seats' => $section->available_seats - $request->quantity
        ]);

        // Create payment record
        $payment = new Payment([
            'user_id' => auth()->id(),
            'ticket_id' => $tickets[0]->id, // Link to the first ticket
            'amount' => $totalPrice,
            'payment_method' => 'credit_card',
            'transaction_id' => 'TRX-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
            'status' => 'completed',
        ]);

        $payment->save();

        return redirect()->route('my-tickets')->with('success', 'Tickets purchased successfully!');
    }
}
