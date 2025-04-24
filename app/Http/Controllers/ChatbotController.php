<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FootballMatch;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon; // Importer Carbon
use Illuminate\Support\Facades\Config; // Importer Config pour la clé API

class ChatbotController extends Controller
{
    /**
     * Handles the incoming chatbot message.
     */
    public function handle(Request $request)
    {
        // Get the original message from the request
        $originalMessage = $request->input('message');
        if (empty($originalMessage)) {
             // Handle empty message case
             return response()->json(['reply' => "Bonjour ! Comment puis-je vous aider aujourd'hui ?"]);
        }

        // Prepare messages for different purposes
        // Use a slightly capitalized version for Gemini as it might work better with prompts
        $messageForGemini = ucfirst(strtolower($originalMessage));
        // Use lowercase for internal processing (keyword extraction, fallback)
        $lowerMessage = strtolower($originalMessage);

        // Get the authenticated user (can be null if not logged in)
        $user = Auth::user();

        try {
            // 1. Detect the user's intent using Gemini or fallback
            $intent = $this->detectIntentWithGemini($messageForGemini, $lowerMessage);
            Log::info("Intent detected: {$intent} for message: '{$originalMessage}'");

            // 2. Route the request based on detected intent
            switch (trim($intent)) { // Use trim in switch for safety
                case 'réservations':
                case 'reservation':
                case 'tickets':
                case 'mes tickets':
                    // Reservations doesn't need keyword extraction for teams here
                    return $this->handleUserReservations($user);

                case 'prix':
                case 'price':
                    return $this->handleMatchPrice($lowerMessage); // Pass lowercase message

                case 'date':
                case 'datetime': // Keep datetime as a potential intent from Gemini, map in fallback
                    return $this->handleMatchDate($lowerMessage); // Pass lowercase message

                case 'lieu':
                case 'stadium': // Keep stadium as a potential intent from Gemini
                    return $this->handleMatchLocation($lowerMessage); // Pass lowercase message

                case 'statut_ticket':
                case 'statut':
                case 'ticket_status': // Keep ticket_status as a potential intent from Gemini
                    return $this->handleTicketStatus($user, $lowerMessage);

                default:
                    // If intent is 'inconnu' or unhandled by the specific cases above
                    // Check if there are keywords to give slightly different "unknown" replies
                    $keywords = $this->extractKeywords($lowerMessage);
                    if (empty($keywords)) {
                         return response()->json(['reply' => "Désolé, je n'ai pas bien compris votre demande. Pourriez-vous reformuler ?"]);
                    } else {
                         // If keywords are found but intent is unknown, suggest examples
                         return response()->json(['reply' => "Je ne suis pas sûr de l'intention de votre demande concernant " . implode(' ', $keywords) . ". Essaie par exemple de demander le 'prix Barca' ou 'quand Real Madrid'."]);
                    }
            }

        } catch (\Exception $e) {
             // Catch any unexpected errors during processing
            Log::error("Chatbot error handling message '{$originalMessage}': " . $e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);
            return response()->json(['reply' => "Une erreur interne est survenue. Veuillez réessayer plus tard."]);
        }
    }

    /**
     * Detects the user's intent using the Gemini API or a keyword-based fallback.
     */
    private function detectIntentWithGemini(string $messageForGemini, string $lowerMessage): string
    {
        $apiKey = config('services.gemini.api_key'); // Get API key from config

        // Check if API key is configured
        if (empty($apiKey)) {
            Log::warning('GEMINI_API_KEY is not set in services config. Using fallback intent detection.');
            return $this->fallbackIntentDetection($lowerMessage);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;

        try {
            // Make the API call with a timeout
            $response = Http::timeout(10) // Set a reasonable timeout
                           // The Authorization header is typically NOT needed for API Key in URL
                           ->withHeaders(['Content-Type' => 'application/json'])
                           ->post($url, [
                                'contents' => [
                                    ['parts' => [
                                        // Refined prompt: Be very strict about the output format and list
                                        // Using English in the prompt can sometimes yield more consistent results from the model
                                        // Added mapping examples in prompt for clarity
                                        ['text' => "You are a football website assistant. Analyze the user query: \"$messageForGemini\". What is the primary user intent? Respond ONLY with a single lowercase word from this exact list: reservations, price, date, location, ticket_status, unknown. If the intent is not clearly one of these from the list, respond with unknown. Do not add any other text, punctuation, or formatting.
                                        Examples:
                                        'Mes tickets' -> 'reservations'
                                        'Prix du match Barca' -> 'price'
                                        'Quand joue Real Madrid' -> 'date'
                                        'Où est le stade' -> 'location'
                                        'Statut de mon ticket' -> 'ticket_status'
                                        'Bonjour' -> 'unknown'
                                        'Raconte une blague' -> 'unknown'
                                        "]
                                    ]]
                                ],
                                 // Keep safety settings for production if applicable
                                 'safetySettings' => [
                                     ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                                     ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                                     ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                                     ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                                 ],
                                 // Generation settings for more deterministic classification
                                 'generationConfig' => [
                                     'temperature' => 0, // Lower temperature makes output more deterministic
                                     'maxOutputTokens' => 20, // We only expect a single word
                                     // 'topK' => 1, // Can also help with deterministic output
                                     // 'topP' => 0, // Can also help
                                 ]
                            ]);

            $responseData = $response->json();

            // Check for common API failure scenarios (HTTP failure or error payload)
            if ($response->failed() || isset($responseData['error'])) {
                Log::error('Gemini API call failed or returned error payload: ' . $response->body());
                return $this->fallbackIntentDetection($lowerMessage);
            }

             // Safely access the predicted intent from the response structure
             // Check if candidates and parts exist as expected
            $predictedIntent = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'unknown';
            $intent = strtolower(trim($predictedIntent));

            // Map Gemini's output (expected English based on prompt, but could be French) to our internal intents
            $intentMap = [
                'reservations' => 'réservations',
                'price' => 'prix',
                'date' => 'date',
                'location' => 'lieu',
                'ticket_status' => 'statut_ticket',
                'unknown' => 'inconnu',
                 // Fallback mappings for French responses if prompt isn't strictly followed
                 'réservations' => 'réservations',
                 'prix' => 'prix',
                 'lieu' => 'lieu',
                 'statut_ticket' => 'statut_ticket',
                 'inconnu' => 'inconnu',
                 // Map common synonyms/variations Gemini might return despite prompt
                 'datetime' => 'date',
                 'stadium' => 'lieu',
                 'statut' => 'statut_ticket',
            ];

             // Return the mapped intent, defaulting to 'inconnu' if Gemini returns something completely unexpected
             $mappedIntent = $intentMap[$intent] ?? 'inconnu';
             if ($mappedIntent === 'inconnu' && $intent !== 'unknown' && $intent !== 'inconnu') {
                 Log::warning("Gemini returned unexpected intent '$intent'. Mapped to 'inconnu'.");
             }
             return $mappedIntent;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Handle HTTP specific errors (e.g., connection failed, timeout)
            Log::error('Gemini HTTP Request Exception: ' . $e->getMessage(), ['exception' => $e]);
            return $this->fallbackIntentDetection($lowerMessage);
        } catch (\Exception $e) {
            // Catch any other unexpected errors during the API call or response processing
            Log::error('Unexpected error during Gemini API call or processing: ' . $e->getMessage(), ['exception' => $e]);
            return $this->fallbackIntentDetection($lowerMessage);
        }
    }

    /**
     * Provides a keyword-based fallback intent detection if the LLM fails or is not used.
     * Improved with more synonyms.
     */
    private function fallbackIntentDetection(string $lowerMessage): string
    {
        // This logic is simpler and faster but less flexible than the LLM
        if (str_contains($lowerMessage, 'réservation') || str_contains($lowerMessage, 'mes tickets') || str_contains($lowerMessage, 'tickets')) {
            return 'réservations';
        } elseif (str_contains($lowerMessage, 'prix') || str_contains($lowerMessage, 'coût') || str_contains($lowerMessage, 'combien') || str_contains($lowerMessage, 'tarif')) { // Added 'tarif'
            return 'prix';
        } elseif (str_contains($lowerMessage, 'date') || str_contains($lowerMessage, 'quand') || str_contains($lowerMessage, 'jour')) { // Added 'jour'
            return 'date';
        } elseif (str_contains($lowerMessage, 'lieu') || str_contains($lowerMessage, 'stade') || str_contains($lowerMessage, 'où')) {
            return 'lieu';
        } elseif (str_contains($lowerMessage, 'statut') || str_contains($lowerMessage, 'valide') || str_contains($lowerMessage, 'confirmation')) {
            return 'statut_ticket';
        }

        // Could add a check for simple team names if needed, but returning 'inconnu' is safer
        // if no specific intent keyword is found.

        return 'inconnu';
    }


    /**
     * Extracts potential keywords from the message after removing stop words and noise.
     */
    private function extractKeywords(string $message): array
    {
         // Default stop words list (can be moved to config)
         $stopWords = [
             'quand', 'prix', 'lieu', 'statut', 'date', 'ticket', 'tickets', 'match', 'vs',
             'pour', 'le', 'du', 'de', 'mon', 'ma', 'mes', 'la', 'à', 'a', 'est', 'il', 'se',
             'où', 'combien', 'coût', 'valide', 'confirmation', 'stade', 'au', 'quel', 'quelle',
             'quels', 'quelles', 'un', 'une', 'des', 'les', 'et', 'ou', 'par', 'sur', 'dans',
             'avec', 'sans', 'pourquoi', 'comment', 'qui', 'que', 'quoi', 'fait', 'fais',
             'faire', 'avoir', 'être', 'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles',
             'mon', 'ton', 'son', 'notre', 'votre', 'leur', 'mes', 'tes', 'ses', 'nos', 'vos',
             'leurs', 'ce', 'cet', 'cette', 'ces', 'celui', 'celle', 'ceux', 'celles', 'ici', 'là',
             'plus', 'moins', 'très', 'beaucoup', 'peu', 'toujours', 'jamais', 'souvent',
             'parfois', 'vite', 'lentement', 'bien', 'mal', 'si', 'très', 'juste', 'déjà', 'encore',
             'maintenant', 'hier', 'aujourd\'hui', 'demain', 'matin', 'soir', 'nuit', 'midi',
             'minuit', 'semaine', 'mois', 'année', 'jour', 'bonjour', 'salut', 'merci',
             'svp', 'stp', 's\'il', 'vous', 'plait', 'moi', 'toi', 'lui', 'elle', 'nous', 'vous', 'eux', 'elles',
             'm', 't', 's', 'n', 'j', 'c', 'd', 'l', // Common single letters/contractions
             'equipe', 'équipes', 'football', 'soccer', 'game', 'partie', 'tarif', 'chercher', 'trouver' // Potentially useful additions
         ];

        $lowerMessage = strtolower($message);

        // Replace punctuation and symbols with spaces, keeping letters and numbers and space
        // Handle apostrophes specifically (replace with space)
        $cleaned = str_replace("'", ' ', $lowerMessage);
        // Use Unicode property \p{L} for letters and \p{N} for numbers to support various languages
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleaned);

        // Remove standalone numbers (e.g., 'match 3', prices that weren't removed by intent)
         $cleaned = preg_replace('/\b\d+(\,\d+)?\b/u', '', $cleaned); // Added 'u' flag


        // Build regex pattern for stop words with word boundaries (\b) and case-insensitivity (handled by lowercasing already)
        // Use 'u' flag for Unicode support with \p{L} in stop words themselves
        $stopWordPattern = '/\b(' . implode('|', array_map(fn($word) => preg_quote($word, '/'), $stopWords)) . ')\b/u';
        $cleaned = preg_replace($stopWordPattern, '', $cleaned);

        // Replace multiple spaces with a single space and trim whitespace from ends
        $cleaned = trim(preg_replace('/\s+/', ' ', $cleaned));

        // Split the cleaned string into an array of words
        $keywords = explode(' ', $cleaned);

        // Filter out any empty strings that might result from splitting/cleaning
        return array_values(array_filter($keywords, 'strlen')); // Re-index the array
    }

    /**
     * Finds a FootballMatch based on a list of keywords.
     * Currently finds the latest match related to the keywords.
     * Can be upgraded to prioritize upcoming matches.
     */
    private function findMatchByKeywords(array $keywords): ?FootballMatch
    {
        if (empty($keywords)) {
            return null;
        }

        $query = FootballMatch::query();

        $query->where(function($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                // Use LOWER() function in SQL for case-insensitive comparison
                $q->orWhereRaw('LOWER(home_team) LIKE ?', ['%' . strtolower($keyword) . '%']);
                $q->orWhereRaw('LOWER(away_team) LIKE ?', ['%' . strtolower($keyword) . '%']);
            }
        });

        // Get the most recent match matching the keywords
        // For 'upcoming' prioritization, this method would need modification (as in the 'perfected' version)
        $match = $query->latest('match_date')->first();

        return $match;
    }


    /**
     * Handles user reservations query.
     */
    private function handleUserReservations(?\App\Models\User $user): \Illuminate\Http\JsonResponse
    {
        // Check if user is authenticated
        if (!$user) {
            return response()->json(['reply' => "Vous devez être connecté(e) pour voir vos réservations."]);
        }

        // Fetch tickets for the user, loading related match data
        // Order by match_date for better readability
        $tickets = $user->tickets()->with('match')->join('matches', 'tickets.match_id', '=', 'matches.id')
                        ->orderBy('matches.match_date', 'asc')
                        ->orderBy('matches.match_time', 'asc')
                        ->select('tickets.*') // Select tickets columns to avoid overwriting
                        ->get();


        if ($tickets->isEmpty()) {
            return response()->json(['reply' => "Tu n'as pas encore de réservations."]);
        }

        // Build the response string, ensuring each match is listed only once
        $processedMatches = []; // Using match ID to track processed matches
        $reply = "🎟 Voici tes réservations :\n";

        foreach ($tickets as $ticket) {
            $match = $ticket->match;

             // Validate that match data is available and dates/times are valid Carbon instances
             if (
                 !$match ||
                 !($match->match_date instanceof Carbon) ||
                 !($match->match_time instanceof Carbon) ||
                 !$match->match_date->isValid() ||
                 !$match->match_time->isValid() // Check validity as well
             ) {
                 Log::warning("Skipping ticket ID " . $ticket->id . " due to missing or invalid match data.");
                 continue; // Skip tickets linked to missing or invalid match data
             }

            $matchIdentifier = $match->id; // Use match ID as a reliable unique key

            if (!isset($processedMatches[$matchIdentifier])) {
                 // Format date and time using Carbon
                 $formattedDate = $match->match_date->format('d/m/Y');
                 $formattedTime = $match->match_time->format('H:i');

                 // Add match details to the reply
                $reply .= "- {$match->home_team} vs {$match->away_team} le {$formattedDate} à {$formattedTime} au {$match->stadium}\n";

                $processedMatches[$matchIdentifier] = true; // Mark this match ID as processed
            }
        }

         // If no valid matches were found after filtering, provide feedback
        if (empty($processedMatches) && !$tickets->isEmpty()) {
             $reply = "Désolé, je n'ai pas pu afficher vos réservations en raison d'informations de match manquantes ou incorrectes.";
        } elseif (empty($processedMatches) && $tickets->isEmpty()) {
             // This case is already handled by the initial empty() check, but as a safeguard
             $reply = "Tu n'as pas encore de réservations.";
        }


        return response()->json(['reply' => $reply]);
    }


    /**
     * Handles match price query.
     */
    private function handleMatchPrice(string $message): \Illuminate\Http\JsonResponse
    {
        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return response()->json(['reply' => "Je n'ai pas compris de quelle équipe ou match vous parlez. Pourriez-vous préciser l'équipe ? Essaie par exemple : 'prix Barca'."]);
        }

        // Find the relevant match using keywords (currently the latest)
        $match = $this->findMatchByKeywords($keywords);

        if (!$match) {
             $keywordList = implode(' ou ', $keywords);
             return response()->json(['reply' => "Désolé, je n'ai trouvé aucun match récent pour l'équipe/les mots clés : '$keywordList'."]);
        }

        // Ensure ticket_price is formatted correctly
        $price = number_format($match->ticket_price, 2, ',', ''); // Format price with 2 decimals and comma

        return response()->json(['reply' => "Le prix du match {$match->home_team} vs {$match->away_team} est de {$price} MAD."]);
    }

    /**
     * Handles match date query.
     */
    private function handleMatchDate(string $message): \Illuminate\Http\JsonResponse
    {
        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return response()->json(['reply' => "Je n'ai pas compris de quelle équipe ou match vous parlez. Pourriez-vous préciser l'équipe ? Essaie par exemple : 'date Real Madrid'."]);
        }

        // Find the relevant match using keywords (currently the latest)
        $match = $this->findMatchByKeywords($keywords);

        if (!$match) {
            $keywordList = implode(' ou ', $keywords);
            return response()->json([
                'reply' => "Désolé, je n'ai trouvé aucun match récent pour l'équipe/les mots clés : '$keywordList'."
            ]);
        }

         // Ensure dates and times are Carbon instances and valid
         $matchDate = $match->match_date instanceof Carbon && $match->match_date->isValid() ? $match->match_date : Carbon::parse($match->match_date);
         $matchTime = $match->match_time instanceof Carbon && $match->match_time->isValid() ? $match->match_time : Carbon::parse($match->match_time);


        return response()->json([
            'reply' => "Le match {$match->home_team} vs {$match->away_team} aura lieu le {$matchDate->format('d/m/Y')} à {$matchTime->format('H:i')}."
        ]);
    }

    /**
     * Handles match location query.
     */
    private function handleMatchLocation(string $message): \Illuminate\Http\JsonResponse
    {
        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return response()->json(['reply' => "Je n'ai pas compris de quelle équipe ou match vous parlez. Pourriez-vous préciser l'équipe ? Essaie par exemple : 'où joue leganes'."]);
        }

        // Find the relevant match using keywords (currently the latest)
        $match = $this->findMatchByKeywords($keywords);

        if (!$match) {
            $keywordList = implode(' ou ', $keywords);
            return response()->json([
                'reply' => "Désolé, je n'ai trouvé aucun match récent pour l'équipe/les mots clés : '$keywordList'."
            ]);
        }

        return response()->json([
            'reply' => "Le match {$match->home_team} vs {$match->away_team} se jouera au stade {$match->stadium}."
        ]);
    }

    /**
     * Handles ticket status query for a specific match for the authenticated user.
     */
    private function handleTicketStatus(?\App\Models\User $user, string $message): \Illuminate\Http\JsonResponse
    {
        // User must be authenticated to check ticket status
        if (!$user) {
            return response()->json(['reply' => "Vous devez être connecté(e) pour vérifier le statut de votre ticket."]);
        }

        // Extract keywords from the user's message to identify the match
        $keywords = $this->extractKeywords($message);

        if (empty($keywords)) {
            return response()->json(['reply' => "Je n'ai pas compris de quel ticket vous parlez. Pourriez-vous préciser l'équipe ? Essaie par exemple : 'statut ticket Barca'."]);
        }

        // Find the relevant match based on keywords (currently the latest)
        $match = $this->findMatchByKeywords($keywords);

        if (!$match) {
             $keywordList = implode(' ou ', $keywords);
             return response()->json(['reply' => "Match introuvable pour l'équipe/les mots clés : '$keywordList'."]);
        }

        // Now, check if the authenticated user has a ticket for this specific match
        $ticket = Ticket::where('user_id', $user->id)
            ->where('match_id', $match->id)
            ->first();

        if (!$ticket) {
             // Inform the user if they don't have a ticket for the found match
             return response()->json([
                 'reply' => "Tu n'as pas de ticket pour le match {$match->home_team} vs {$match->away_team}."
            ]);
        }

        // Return the status of the user's ticket for this match
        return response()->json(['reply' => "Ton ticket pour le match {$match->home_team} vs {$match->away_team} est de statut : {$ticket->status}."]);
    }
}