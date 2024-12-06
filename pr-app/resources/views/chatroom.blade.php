<!-- resources/views/chat.blade.php -->
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Chat Room</h2>
                
                <!-- Chat Messages Container -->
                <div id="chat-messages" class="h-96 overflow-y-auto mb-4 p-4 bg-base-200 rounded-lg space-y-2">
                    <!-- Messages will be inserted here -->
                </div>

                <!-- Chat Input Form -->
                <form id="chat-form" class="flex gap-2">
                    @csrf
                    <input type="text" 
                           id="chat-input" 
                           name="text-value"
                           class="input input-bordered flex-1" 
                           placeholder="Type your message..."
                           required>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Message Template -->
    <template id="message-template">
        <div class="chat chat-message">
            <div class="chat-header font-bold">
                <span class="username"></span>
                <time class="text-xs opacity-50 ml-2"></time>
            </div>
            <div class="chat-bubble"></div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const messagesContainer = document.getElementById('chat-messages');
            const messageTemplate = document.getElementById('message-template');

            // Listen for private chat messages
            window.Echo.private('chat-channel')
                .listen('ChatMessage', (event) => {
                    appendMessage(event.chat);
                });

            // Handle form submission
            chatForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const message = chatInput.value.trim();
                if (!message) return;

                try {
                    const response = await fetch('/send-chat-message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            'text-value': message
                        })
                    });

                    if (response.ok) {
                        chatInput.value = '';
                    } else {
                        console.error('Failed to send message');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                }
            });

            // Function to append a new message to the chat
            function appendMessage(chat) {
                const messageNode = messageTemplate.content.cloneNode(true);
                const messageDiv = messageNode.querySelector('.chat-message');
                const username = messageNode.querySelector('.username');
                const time = messageNode.querySelector('time');
                const bubble = messageNode.querySelector('.chat-bubble');

                // Set message content
                username.textContent = chat.username;
                bubble.textContent = chat.text;
                time.textContent = new Date().toLocaleTimeString();

                // Determine message alignment
                if (chat.username === '{{ auth()->user()->name }}') {
                    messageDiv.classList.add('chat-end');
                    bubble.classList.add('chat-bubble-primary');
                } else {
                    messageDiv.classList.add('chat-start');
                }

                // Add message to container and scroll to bottom
                messagesContainer.appendChild(messageNode);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            // Auto-scroll to bottom on page load
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    </script>
</body>
</html>