<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\FootballMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()
            ->with(['match', 'payment'])
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $match = FootballMatch::findOrFail($request->match_id);

        if ($match->available_tickets < $request->quantity) {
            return back()->with('error', 'Not enough tickets available.');
        }

        $tickets = [];
        for ($i = 0; $i < $request->quantity; $i++) {
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'match_id' => $match->id,
                'price' => $match->ticket_price,
                'status' => 'confirmed', // Correction ici, les tickets doivent être "confirmed" pour être téléchargés
                'ticket_number' => 'TIX-' . Str::random(10),
            ]);

            // Génération du QR Code avec une URL de validation
            $qrData = route('tickets.download', ['ticket' => $ticket->id]);

            $qrPath = 'qrcodes/' . $ticket->ticket_number . '.png';
            QrCode::format('png')->size(300)->generate($qrData, storage_path("app/public/{$qrPath}"));

            // Met à jour le ticket avec le chemin du QR Code
            $ticket->update(['qr_code' => $qrPath]);
            $tickets[] = $ticket;
        }

        // Mise à jour du nombre de tickets disponibles après achat
        $match->decrement('available_tickets', $request->quantity);

        return redirect()->route('my-tickets')->with('success', 'Booking successful! You have booked ' . $request->quantity . ' ticket(s) for ' . $match->home_team . ' vs ' . $match->away_team . '. Check your tickets below.');
    }

    public function download(Ticket $ticket)
    {
        // Vérifier si l'utilisateur est bien connecté
        if (!auth()->check()) {
            dd("Utilisateur non authentifié");
        }
    
        // Vérifier si l'utilisateur possède bien ce ticket
        if ($ticket->user_id !== auth()->id()) {
            dd("Ce ticket ne vous appartient pas !");
        }
    
        // Vérifier que le statut est bien "confirmed"
        if ($ticket->status !== 'confirmed') {
            dd("Seuls les tickets confirmés peuvent être téléchargés !");
        }
    
        // Vérifier que le fichier QR Code existe
        
        // Générer le PDF
        $pdf = Pdf::loadView('tickets.pdf', compact('ticket'))->setPaper('a4', 'portrait');

    
        return $pdf->download('Ticket-' . $ticket->ticket_number . '.pdf');
    }
    
    
    
}



