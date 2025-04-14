<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $message = $this->normalizeMessage($request->input('message'));

        if (Str::contains($message, ['bonjour', 'salut', 'hello'])) {
            $name = auth()->check() ? auth()->user()->name : '👋';
            return response()->json(['response' => "Bonjour $name ! Je suis ton assistant Tutore 🤖. Comment puis-je vous aider aujourd'hui ?"]);
        }

        if (Str::contains($message, ['merci', 'thanks', 'mirci', 'mercie'])) {
            return response()->json(['response' => "Avec plaisir ! Si tu as d'autres questions, je suis là pour t'aider 💚"]);
        }

        $intent = $this->detectIntentWithGemini($message);

        if (!isset($intent['action']) || $intent['action'] === 'unknown') {
            $intent = $this->fallbackIntentDetection($message);
        }

        if (!empty($intent['team'])) {
            session(['chatbot_last_team' => $intent['team']]);
        } elseif (session()->has('chatbot_last_team')) {
            $intent['team'] = session('chatbot_last_team');
        }

        return match ($intent['action'] ?? 'unknown') {
            'upcoming_matches' => response()->json(['response' => $this->getUpcomingMatches()]),
            'user_tickets' => response()->json(['response' => $this->getUserTickets()]),
            'match_price' => response()->json(['response' => $this->getMatchPrice($intent['team'] ?? null)]),
            'match_location' => response()->json(['response' => $this->getMatchLocation($intent['team'] ?? null)]),
            'match_date' => response()->json(['response' => $this->getMatchDates($intent['team'] ?? null)]),
            'match_status' => response()->json(['response' => $this->getMatchStatus($intent['team'] ?? null)]),
            'match_by_stadium' => response()->json(['response' => $this->getMatchByStadium($intent['stadium'] ?? null)]),
            'user_total_payments' => response()->json(['response' => $this->getUserTotalPayments()]),
            default => response()->json(['response' => $this->getFallbackMessage($message)])
        };
    }

    private function normalizeMessage($message)
    {
        return (string) Str::of($message)
            ->lower()
            ->ascii()
            ->replaceMatches("/[^a-z0-9\s]/", '')
            ->replaceMatches("/\s+/"," ").trim();
    }

    private function detectIntentWithGemini($message)
    {
        try {
            $client = new Client();
            $response = $client->post('https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent', [
                'query' => ['key' => config('services.gemini.key')],
                'json' => [
                    'contents' => [[
                        'parts' => [[
                            'text' => "Tu es un assistant pour un site de billetterie de football. Analyse ce message utilisateur et retourne une intention au format JSON avec un champ 'action', optionnellement 'team' ou 'stadium'. Exemples valides : {\"action\":\"match_price\",\"team\":\"Barca\"}. Corrige automatiquement les fautes d'orthographe légères. Réponds uniquement avec du JSON. Question: $message"
                        ]]
                    ]]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            \Log::info('Gemini raw response:', ['text' => $text]);

            $text = trim($text);
            $text = preg_replace('/^```json|```$/i', '', $text);
            $intent = json_decode($text, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($intent['action'])) {
                return $intent;
            }
        } catch (\Exception $e) {
            \Log::error('Gemini API error', ['error' => $e->getMessage()]);
        }

        return ['action' => 'unknown'];
    }

    private function fallbackIntentDetection($message)
    {
        $map = [
            'upcoming_matches' => ['match', 'matchs', 'venir', 'joue'],
            'match_price' => ['prix', 'combien', 'coût'],
            'match_location' => ['lieu', 'où', 'localisation'],
            'match_date' => ['date', 'quand'],
            'user_tickets' => ['billet', 'réservation'],
        ];

        foreach ($map as $action => $keywords) {
            if (Str::contains($message, $keywords)) {
                return ['action' => $action];
            }
        }

        return ['action' => 'unknown'];
    }

    private function getFallbackMessage($message)
    {
        return "Je n'ai pas compris. 🧐 Tu peux essayer avec : 'Quels sont les matchs ?', 'Quel est le prix de Barca ?', ou 'Je veux voir mes billets'.";
    }

    private function getUpcomingMatches()
    {
        return FootballMatch::where('match_date', '>=', now())
            ->orderBy('match_date')
            ->take(5)
            ->get()
            ->map(fn($match) => "- {$match->home_team} vs {$match->away_team}, le " . $match->match_date->format('d/m/Y H:i') . " au stade {$match->stadium}")
            ->prepend("📅 Prochains matchs :")
            ->implode("\n") ?: "Aucun match trouvé.";
    }

    private function getUserTickets()
    {
        if (!auth()->check()) return "Veuillez vous connecter pour voir vos billets.";

        $tickets = auth()->user()->tickets()->with('match')->latest()->take(5)->get();

        if ($tickets->isEmpty()) return "🎟️ Vous n'avez aucun billet pour le moment.";

        $response = "🎟️ Vos billets :\n";
        foreach ($tickets as $ticket) {
            $match = $ticket->match;
            $response .= "- {$match->home_team} vs {$match->away_team} le " . $match->match_date->format('d/m/Y') . " au stade {$match->stadium} (Place : {$ticket->seat_number})\n";
        }
        return $response;
    }

    private function getMatchPrice($team)
    {
        $match = $this->findMatchByTeam($team);
        return $match ? "🎫 Le billet pour {$match->home_team} vs {$match->away_team} est à \${$match->ticket_price}."
                      : "❌ Aucun match trouvé pour cette équipe.";
    }

    private function getMatchLocation($team)
    {
        $match = $this->findMatchByTeam($team);
        return $match ? "📍 Le match se joue au stade {$match->stadium}."
                      : "❌ Aucun lieu trouvé pour cette équipe.";
    }

    private function getMatchDates($team)
    {
        $matches = $this->findAllMatchesByTeam($team);
        if ($matches->isEmpty()) return "❌ Aucun match trouvé pour $team.";

        return "📅 Prochains matchs pour $team :\n" .
            $matches->map(fn($m) => "- {$m->home_team} vs {$m->away_team}, le " . $m->match_date->format('d/m/Y H:i') . " au stade {$m->stadium}")->implode("\n");
    }

    private function getMatchStatus($team)
    {
        $match = $this->findMatchByTeam($team);
        return $match ? "📊 Le match {$match->home_team} vs {$match->away_team} est actuellement marqué comme '{$match->match_status}'."
                      : "❌ Statut introuvable pour cette équipe.";
    }

    private function getMatchByStadium($stadium)
    {
        $matches = FootballMatch::where('stadium', 'like', "%$stadium%")
            ->orderBy('match_date', 'asc')->get();

        if ($matches->isEmpty()) return "Aucun match prévu dans ce stade.";

        return "🏟️ Matchs au stade $stadium :\n" .
            $matches->map(fn($m) => "- {$m->home_team} vs {$m->away_team}, le " . $m->match_date->format('d/m/Y H:i') )->implode("\n");
    }

    private function getUserTotalPayments()
    {
        if (!auth()->check()) return "Connectez-vous pour voir vos paiements.";

        $total = Payment::where('user_id', auth()->id())->where('status', 'completed')->sum('amount');
        return "💰 Vous avez payé un total de \$$total pour vos billets.";
    }

    private function findMatchByTeam($team)
    {
        return $team ? FootballMatch::where('home_team', 'like', "%$team%")
                            ->orWhere('away_team', 'like', "%$team%")
                            ->orderBy('match_date')
                            ->first() : null;
    }

    private function findAllMatchesByTeam($team)
    {
        return $team ? FootballMatch::where('home_team', 'like', "%$team%")
                            ->orWhere('away_team', 'like', "%$team%")
                            ->orderBy('match_date')
                            ->get() : collect();
    }
}
