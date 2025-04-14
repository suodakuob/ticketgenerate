<div id="chatbot-toggle" class="fixed bottom-4 right-4 z-50">
    <button onclick="toggleChatbot()" class="bg-green-600 text-white rounded-full p-3 shadow-lg hover:bg-green-700 transition">
        💬
    </button>
</div>

<div id="chatbot" class="fixed bottom-24 right-4 w-80 h-96 bg-white border border-gray-300 shadow-xl rounded-lg hidden z-50 flex flex-col">
    <div class="p-2 bg-green-600 text-white font-semibold">Assistant Tutore</div>
    <div id="chat-messages" class="p-2 flex-1 overflow-y-auto text-sm space-y-2 bg-gray-50"></div>
    <form onsubmit="sendMessage(event)" class="p-2 border-t flex gap-2">
        <input type="text" id="chat-input" placeholder="Posez votre question..." class="flex-1 border rounded px-2 py-1 text-sm" />
        <button type="submit" class="bg-green-500 text-white px-3 rounded hover:bg-green-600">Envoyer</button>
    </form>
</div>

<script>
    function toggleChatbot() {
        document.getElementById('chatbot').classList.toggle('hidden');
    }

    async function sendMessage(event) {
        event.preventDefault();
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;

        const messages = document.getElementById('chat-messages');
        messages.innerHTML += `<div class="text-right text-green-600">${message}</div>`;
        input.value = '';

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
            messages.innerHTML += `<div class="text-left text-gray-800 whitespace-pre-line">${data.response}</div>`;
            messages.scrollTop = messages.scrollHeight;
        } catch (err) {
            messages.innerHTML += `<div class="text-left text-red-600">Erreur lors de la réponse 🤖</div>`;
        }
    }
</script>
