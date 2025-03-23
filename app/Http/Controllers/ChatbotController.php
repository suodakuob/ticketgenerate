<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\FootballMatch;
use App\Models\Ticket;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message');

        $intent = $this->detectIntentWithAI($message);

        if (!empty($intent['team'])) {
            session(['chatbot_last_team' => $intent['team']]);
        } elseif (!empty(session('chatbot_last_team'))) {
            $intent['team'] = session('chatbot_last_team');
        }

        return match ($intent['action']) {
            'upcoming_matches' => response()->json(['response' => $this->getUpcomingMatches()]),
            'user_tickets' => response()->json(['response' => $this->getUserTickets()]),
            'match_price' => response()->json(['response' => $this->getMatchPrice($intent['team'])]),
            'match_location' => response()->json(['response' => $this->getMatchLocation($intent['team'])]),
            'match_date' => response()->json(['response' => $this->getMatchDatesForTeam($intent['team'])]),
            default => $this->fallbackResponse($message),
        };
    }

    private function detectIntentWithAI($message)
    {
        try {
            $client = new Client();

            $response = $client->post('https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.openrouter.key'),
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'http://localhost',
                    'X-Title' => 'Tutore Chatbot'
                ],
                'json' => [
                    'model' => 'openchat/openchat-7b',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "Tu es un assistant pour un site de billetterie de football. Réponds uniquement au format JSON. Exemples :
                            {\"action\":\"match_price\", \"team\":\"Barca\"}
                            {\"action\":\"greeting\"}
                            Si un utilisateur dit bonjour, réponds avec {\"action\":\"greeting\"}."
                        ],
                        ['role' => 'user', 'content' => $message],
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $content = $data['choices'][0]['message']['content'] ?? '';

            // Vérifie si la réponse est bien du JSON
            $intent = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($intent['action'])) {
                return $intent;
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        // Tentative de fallback basique
        if (Str::contains(strtolower($message), ['match', 'matchs', 'venir', 'prochains'])) {
            return ['action' => 'upcoming_matches'];
        }
         /*   if (Str::contains(strtolower($message), ['bonjour', 'salut', 'coucou', 'hello', 'hi'])) {
            return ['action' => 'greeting'];
        }*/
        

        return ['action' => 'unknown'];
    }

    private function fallbackResponse($message)
    {
        return response()->json(['response' => $this->getDefaultIAResponse($message)]);
    }

    private function getUpcomingMatches()
    {
        $matches = FootballMatch::where('match_date', '>=', now())->orderBy('match_date')->take(5)->get();

        if ($matches->isEmpty()) {
            return "Il n'y a actuellement aucun match prévu.";
        }

        $response = "\ud83d\udcc5 Prochains matchs :\n";
        foreach ($matches as $match) {
            $response .= "- {$match->home_team} vs {$match->away_team}, le " . $match->match_date->format('d/m/Y H:i') . " au stade {$match->stadium}\n";
        }
        return $response;
    }

    private function getUserTickets()
    {
        if (!auth()->check()) return "\u26a0\ufe0f Veuillez vous connecter pour voir vos billets.";

        $tickets = auth()->user()->tickets()->with('match')->latest()->take(5)->get();

        if ($tickets->isEmpty()) return "\ud83c\udf9f\ufe0f Vous n'avez aucun billet pour le moment.";

        $response = "\ud83c\udf9f\ufe0f Vos billets :\n";
        foreach ($tickets as $ticket) {
            $match = $ticket->match;
            $response .= "- {$match->home_team} vs {$match->away_team} le " . $match->match_date->format('d/m/Y') . " au stade {$match->stadium} (Place : {$ticket->seat_number})\n";
        }
        return $response;
    }

    private function getMatchFromTeam($team)
    {
        if (!$team) return null;

        return FootballMatch::where(function ($query) use ($team) {
            $query->where('home_team', 'like', "%$team%")
                ->orWhere('away_team', 'like', "%$team%");
        })->orderBy('match_date')->first();
    }

    private function getAllMatchesFromTeam($team)
    {
        if (!$team) return collect();

        return FootballMatch::where(function ($query) use ($team) {
            $query->where('home_team', 'like', "%$team%")
                ->orWhere('away_team', 'like', "%$team%");
        })->orderBy('match_date')->get();
    }

    private function getMatchPrice($team)
    {
        $match = $this->getMatchFromTeam($team);
        return $match ? "\ud83c\udfab Le billet pour {$match->home_team} vs {$match->away_team} est à \${$match->ticket_price}."
            : "\u274c Aucun match avec cette équipe trouvé dans notre base.";
    }

    private function getMatchLocation($team)
    {
        $match = $this->getMatchFromTeam($team);
        return $match ? "\ud83d\udccd Le match se joue au stade {$match->stadium}."
            : "\u274c Lieu introuvable : aucun match trouvé pour cette équipe.";
    }

    private function getMatchDatesForTeam($team)
    {
        $matches = $this->getAllMatchesFromTeam($team);

        if ($matches->isEmpty()) return "\u274c Aucun match trouvé pour l'équipe $team.";

        $response = "\ud83d\uddd3\ufe0f Prochains matchs pour $team :\n";
        foreach ($matches as $match) {
            $response .= "- {$match->home_team} vs {$match->away_team}, le " . $match->match_date->format('d/m/Y H:i') . " au stade {$match->stadium}\n";
        }

        return $response;
    }

    private function getDefaultIAResponse($message)
    {
        return "Je n'ai pas compris votre demande. Vous pouvez par exemple me demander : 'Quel est le prix du match de l'Algérie ?', 'Quels sont les matchs à venir ?', ou 'Je veux voir mes billets'.";
    }

     /*   private function getGreetingResponse()
{
    $greetings = [
        "👋 Bonjour ! Comment puis-je vous aider aujourd'hui ?",
        "🙌 Salut ! Vous cherchez des infos sur les matchs ?",
        "⚽ Hello ! Dites-moi si vous voulez réserver un billet !",
    ];

    return $greetings[array_rand($greetings)];
}*/
}
