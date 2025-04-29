<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Nécessaire pour enregistrer les erreurs

class GeneralChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message');
        $apiKey = env('GEMINI_API_KEY' , 'AIzaSyAZAGyL1NguhOxnzItVsQAPU-LZogL3t7gw'); // Récupère la clé API

        // --- Validation et vérification de la clé API ---
        if (empty($message)) {
            return response()->json(['reply' => "Veuillez saisir un message."], 400);
        }

        if (!$apiKey) {
            Log::error("GEMINI_API_KEY n'est pas définie dans l'environnement (.env).");
            return response()->json(['reply' => "Erreur de configuration : La clé API GEMINI n'est pas définie sur le serveur."], 500);
        }

        // --- Préparation de l'appel API Gemini ---
        // L'URL correcte avec la clé API comme paramètre de requête
        // >>> C'EST CETTE LIGNE QUI DOIT ÊTRE MODIFIÉE <<<
        // Remplacez 'gemini-pro' par 'gemini-1.5-flash' ou 'gemini-1.5-pro'
        // selon ce que votre appel ListModels a montré être disponible.
        // D'après votre ListModels, 'gemini-1.5-flash' ou 'gemini-1.5-pro' fonctionnent.
        // J'utilise 'gemini-1.5-flash' ci-dessous :
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        // Si vous préférez utiliser 'gemini-1.5-pro', décommentez la ligne ci-dessous et commentez celle au-dessus :
        // $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . $apiKey;


        // Le corps de la requête API, format spécifique à Gemini
        $requestBody = [
            'contents' => [
                ['parts' => [
                    ['text' => $message]
                ]]
            ],
            // Options de génération (ajustez si nécessaire, ces valeurs sont standard)
            'generationConfig' => [
                 'temperature' => 0.8,
                 'maxOutputTokens' => 800,
            ],
            // Paramètres de sécurité (la configuration par défaut est généralement BLOCK_SOME ou BLOCK_MOST,
            // ajustez prudemment si vous rencontrez des blocages excessifs, mais c'est à vos risques)
            // 'safetySettings' => [
            //     ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
            //     ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
            //     ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
            //     ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
            // ],
        ];

        // --- Appel à l'API Gemini ---
        try {
            // Envoie la requête POST. Laravel's Http ajoute Content-Type.
            // Nous n'ajoutons PAS l'en-tête Authorization: Bearer pour l'API Key.
            $response = Http::post($apiUrl, $requestBody);

            // --- Gestion de la réponse de l'API ---
            if ($response->successful()) {
                $data = $response->json();

                // Extrait la réponse en texte, avec des fallbacks robustes
                // Vérifie si 'candidates' existe et n'est pas vide
                $reply = "Désolé, je n'ai pas pu générer de réponse pour votre message."; // Default fallback
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'];
                } elseif (isset($data['candidates'][0]['finishReason'])) {
                    // Fournit le motif si pas de texte (ex: "SAFETY")
                    $reply = "La génération de réponse s'est terminée avec la raison : " . $data['candidates'][0]['finishReason'];
                } elseif (isset($data['promptFeedback']['blockReason'])) {
                     // Fournit le motif si le prompt a été bloqué avant la génération
                    $reply = "Votre message a été bloqué en raison de : " . $data['promptFeedback']['blockReason'];
                }


                // Optionnel : Loguer le corps complet de la réponse pour débogage si besoin
                // Log::info("Réponse Gemini successful: " . json_encode($data));

                return response()->json(['reply' => $reply]);

            } else {
                // --- Gestion des erreurs de l'API (4xx ou 5xx) ---
                $statusCode = $response->status();
                $errorBody = $response->body();

                Log::error("Erreur lors de l'appel à l'API Gemini. Statut: {$statusCode}. Corps de la réponse: {$errorBody}");

                // Tente d'extraire le message d'erreur de Google si le corps est JSON
                $errorData = json_decode($errorBody, true);
                $errorMessage = $errorData['error']['message'] ?? "Erreur inconnue";
                 // Ajoute plus de détails si l'erreur API est un JSON valide avec des détails structurés
                if (isset($errorData['error']['details'][0]['@type'])) {
                     // Affiche les détails structurés de l'erreur API
                     $errorMessage .= " (Détails: " . json_encode($errorData['error']['details']) . ")";
                }


                // Renvoie un message d'erreur détaillé au frontend
                return response()->json([
                    // Ceci est le message qui s'affiche dans l'interface du chatbot
                    'reply' => "Une erreur s'est produite lors de la communication avec l'assistant généraliste (Code API: {$statusCode}). Détail : {$errorMessage}."
                ], $statusCode >= 400 && $statusCode < 600 ? $statusCode : 500); // Utilise le code HTTP réel ou 500
            }

        } catch (\Exception $e) {
            // --- Gestion des exceptions générales (réseau, etc.) ---
            Log::error("Exception lors de l'appel à l'API Gemini: " . $e->getMessage());
            return response()->json(['reply' => "Une erreur interne inattendue s'est produite."], 500);
        }
    }
}