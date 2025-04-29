<div id="chatbot-toggle" class="fixed bottom-4 right-4 z-50">
    <button onclick="toggleChatbot()" class="bg-green-600 text-white rounded-full p-4 shadow-lg hover:bg-green-700 transition duration-300 text-lg">
        💬
    </button>
</div>

<div id="chatbot" class="fixed bottom-24 right-4 w-80 max-w-[90vw] h-96 bg-white border border-gray-300 shadow-2xl rounded-xl hidden z-50 flex flex-col overflow-hidden">
    <div class="p-3 bg-green-600 text-white font-bold text-sm flex justify-between items-center">
        Assistant Ticket 360 🤖
        <button onclick="toggleChatbot()" class="text-white text-lg">×</button>
    </div>
    <div id="chat-messages" class="flex-1 overflow-y-auto p-3 space-y-2 bg-gray-50 text-sm scroll-smooth"></div>
    <form onsubmit="sendMessage(event)" class="p-2 border-t flex gap-2 bg-white">
        <input type="text" id="chat-input" placeholder="Posez votre question..." autocomplete="off"
               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
        <button type="submit"
                class="bg-green-500 text-white px-4 rounded hover:bg-green-600 transition duration-200 text-sm">Envoyer</button>
    </form>
</div>

<script>
    function toggleChatbot() {
        const bot = document.getElementById('chatbot');
        bot.classList.toggle('hidden');
        if (!bot.classList.contains('hidden')) {
            setTimeout(() => {
                document.getElementById('chat-input').focus();
            }, 100);
        }
    }

    async function sendMessage(event) {
        event.preventDefault();
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;

        const messages = document.getElementById('chat-messages');

        // Affichage du message utilisateur
        messages.innerHTML += `
            <div class="text-right">
                <div class="inline-block bg-green-100 text-green-800 px-3 py-2 rounded-xl max-w-xs">${message}</div>
            </div>
        `;

        // Affichage du "Assistant écrit..."
        const loadingId = 'typing-indicator-' + Date.now();
        messages.innerHTML += `
            <div class="text-left text-gray-500" id="${loadingId}">🤖 Assistant écrit...</div>
        `;

        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        try {
            const res = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await res.json();
            document.getElementById(loadingId).remove();

            const replyClass = data.reply.toLowerCase().includes('aucun match') || data.reply.toLowerCase().includes('désolé')
                ? 'bg-red-100 text-red-800'
                : 'bg-gray-200 text-gray-800';

            messages.innerHTML += `
                <div class="text-left">
                    <div class="inline-block ${replyClass} px-3 py-2 rounded-xl max-w-xs whitespace-pre-line">${data.reply}</div>
                </div>
            `;
            messages.scrollTop = messages.scrollHeight;
        } catch (err) {
            document.getElementById(loadingId).remove();
            messages.innerHTML += `
               ${err}
            `;
        }
    }
</script>
