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

        // Assurer que le dossier public/qrcodes existe
        $qrDir = public_path('qrcodes');
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0775, true);
        }

        $tickets = [];
        for ($i = 0; $i < $request->quantity; $i++) {
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'match_id' => $match->id,
                'price' => $match->ticket_price,
                'status' => 'confirmed',
                'ticket_number' => 'TIX-' . Str::random(10),
            ]);

            // Génération des données du QR Code (SANS ÉMOJIS, EN UTF-8)
            $qrData = utf8_encode(
                "Ticket Information\n"
                    . "Match: " . $match->home_team . " vs " . $match->away_team . "\n"
                    . "Date: " . $match->match_date->format('F j, Y g:i A') . "\n"
                    . "Stadium: " . $match->stadium . "\n"
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

        // Mise à jour du nombre de tickets disponibles après achat
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

        // Encoder l’image en base64 pour l’inclure dans le PDF
        $qrBase64 = "data:image/png;base64," . base64_encode(file_get_contents($qrPath));

        // Générer le PDF
        $pdf = Pdf::loadView('tickets.pdf', compact('ticket', 'qrBase64'))->setPaper('a4', 'portrait');

        return $pdf->download('Ticket-' . $ticket->ticket_number . '.pdf');
    }
}