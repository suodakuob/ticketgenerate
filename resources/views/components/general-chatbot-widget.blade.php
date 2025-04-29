<!-- Bouton pour ouvrir/fermer le chatbot -->
<div id="generalbot-toggle" class="fixed bottom-20 right-4 z-50">
    <button onclick="toggleGeneralbot()" class="bg-blue-600 text-white rounded-full p-4 shadow-lg hover:bg-blue-700 transition duration-300 text-lg">
        🌐 <!-- Ou une autre icône si vous préférez -->
    </button>
</div>

<!-- Fenêtre du chatbot (cachée par défaut) -->
<div id="generalbot" class="fixed bottom-44 right-4 w-80 max-w-[90vw] h-96 bg-white border border-gray-300 shadow-2xl rounded-xl hidden z-50 flex flex-col overflow-hidden">
    <!-- Header du chatbot -->
    <div class="p-3 bg-blue-600 text-white font-bold text-sm flex justify-between items-center">
        Assistant Général 🌐
        <button onclick="toggleGeneralbot()" class="text-white text-lg leading-none">×</button> <!-- Bouton de fermeture (×) -->
    </div>
    <!-- Zone d'affichage des messages -->
    <div id="general-messages" class="flex-1 overflow-y-auto p-3 space-y-2 bg-gray-50 text-sm scroll-smooth relative"></div>
    <!-- Formulaire d'input pour l'utilisateur -->
    <form onsubmit="sendGeneralMessage(event)" class="p-2 border-t flex gap-2 bg-white">
        <input type="text" id="general-input" placeholder="Posez votre question..." autocomplete="off"
               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <button type="submit"
                class="bg-blue-500 text-white px-4 rounded hover:bg-blue-600 transition duration-200 text-sm">Envoyer</button>
    </form>
</div>

<!-- Script JavaScript pour la logique du chatbot -->
<script>
    function toggleGeneralbot() {
        const bot = document.getElementById('generalbot');
        bot.classList.toggle('hidden');
        if (!bot.classList.contains('hidden')) {
            setTimeout(() => {
                document.getElementById('general-input').focus();
            }, 100);
        }
    }

    async function sendGeneralMessage(event) {
        event.preventDefault();
        const input = document.getElementById('general-input');
        const message = input.value.trim();
        if (!message) return;

        const messages = document.getElementById('general-messages');

        // Afficher le message de l'utilisateur
        messages.innerHTML += `
            <div class="text-right">
                <div class="inline-block bg-blue-100 text-blue-800 px-3 py-2 rounded-xl max-w-xs break-words">${message}</div>
            </div>
        `;

        // Afficher l'indicateur de frappe
        const loadingId = 'typing-general-' + Date.now();
        messages.innerHTML += `
            <div class="text-left text-gray-500 text-xs" id="${loadingId}">🌐 Assistant écrit...</div>
        `;

        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        try {
            // Envoyer la requête au backend Laravel
            const res = await fetch('{{ route("chatbot.general") }}', { // Utilise le nom de la route pour plus de robustesse
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Inclure le token CSRF
                },
                body: JSON.stringify({ message: message })
            });

            // Gestion améliorée des erreurs HTTP
            if (!res.ok) {
                 let errorDetails = `Erreur HTTP: ${res.status}`;
                 try {
                     const errorData = await res.json();
                     // Préfère le message 'reply' du contrôleur si présent, sinon les détails de l'erreur API ou statut
                     errorDetails = errorData.reply || `API Error ${res.status}: ${JSON.stringify(errorData)}`;
                 } catch (jsonError) {
                     // Si la réponse d'erreur n'est pas JSON, utilise juste le statut et le texte brut si possible
                     console.error("Failed to parse error response JSON:", jsonError);
                     errorDetails += ` - ${await res.text().catch(() => 'No response body')}`;
                 }
                 throw new Error(errorDetails);
            }

            // Traiter la réponse succès
            const data = await res.json();

            // Supprimer l'indicateur de frappe
            document.getElementById(loadingId)?.remove();

            // Afficher la réponse de l'assistant
            messages.innerHTML += `
                <div class="text-left">
                    <div class="inline-block bg-gray-200 text-gray-800 px-3 py-2 rounded-xl max-w-xs break-words whitespace-pre-line">${data.reply}</div>
                </div>
            `;
            messages.scrollTop = messages.scrollHeight;

        } catch (err) {
            console.error("Erreur lors de l'envoi ou de la réception du message:", err);

            // Supprimer l'indicateur de frappe
            document.getElementById(loadingId)?.remove();

            // Afficher l'erreur dans l'interface utilisateur
            messages.innerHTML += `
                <div class="text-left text-red-600 text-xs">❌ ${err.message || 'Une erreur inconnue est survenue.'}</div>
            `;
            messages.scrollTop = messages.scrollHeight;
        }
    }
</script>