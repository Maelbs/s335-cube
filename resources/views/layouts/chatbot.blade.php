<!DOCTYPE html>
<html lang="fr">
 
<head>
 
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
 
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/chatBox.css') }}">
</head>
 
<body>
 
    <div id="chat-button" class="chat-button">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21H16.5C17.8978 21 18.5967 21 19.1481 20.7716C19.8831 20.4672 20.4672 19.8831 20.7716 19.1481C21 18.5967 21 17.8978 21 16.5V12C21 7.02944 16.9706 3 12 3ZM8 11C8 10.4477 8.44772 10 9 10H15C15.5523 10 16 10.4477 16 11C16 11.5523 15.5523 12 15 12H9C8.44772 12 8 11.5523 8 11ZM11 15C11 14.4477 11.4477 14 12 14H15C15.5523 14 16 14.4477 16 15C16 15.5523 15.5523 16 15 16H12C11.4477 16 11 15.5523 11 15Z">
            </path>
        </svg>
    </div>
 
    <div id="chat-box" class="chat-box">
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar">E</div>
                <div class="chat-text-group">
                    <span class="chat-title">Expert CUBE</span>
                    <span class="chat-status" id="chat-status">
                        @auth
                            Ravi de vous revoir, {{ Auth::user()->prenom_client }}
                        @else
                            Toujours là pour vous
                        @endauth
                    </span>
                </div>
            </div>
            <div class="header-actions">
                <button id="clear-chat" title="Vider la discussion">🗑️</button>
                <button id="close-chat" title="Fermer">✕</button>
            </div>
        </div>
 
        <div id="chat-content" class="chat-content">
        </div>
 
        <div class="chat-footer">
            <div class="input-wrapper">
                <input type="text" id="chat-input" placeholder="Écrivez votre message...">
                <button id="send-btn" class="send-btn" title="Envoyer">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
 
    <script>
        function addToCartFromBot(ref, qty) {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            const token = metaTag ? metaTag.content : '{{ csrf_token() }}';
 
            fetch('/bot/add-to-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ ref: ref, qty: qty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("" + data.message);
                    window.location.reload();
                } else {
                    alert("" + (data.error || 'Erreur lors de l\'ajout'));
                }
            })
            .catch(err => {
                console.error("Erreur Fetch:", err);
                alert("Erreur de connexion au serveur.");
            });
        }
 
        document.addEventListener("DOMContentLoaded", function () {
            const chatButton = document.getElementById("chat-button");
            const chatBox = document.getElementById("chat-box");
            const closeChatBtn = document.getElementById("close-chat");
            const chatInput = document.getElementById("chat-input");
            const sendBtn = document.getElementById("send-btn");
            const chatContent = document.getElementById("chat-content");
            const clearChat = document.getElementById("clear-chat");
 
            function toggleChat() {
                if (chatBox.style.display === "flex") {
                    chatBox.style.display = "none";
                    localStorage.setItem('cube_chat_open', 'false');
                } else {
                    chatBox.style.display = "flex";
                    localStorage.setItem('cube_chat_open', 'true');
                    setTimeout(() => chatInput.focus(), 100);
                }
            }
 
            function addMessage(text, side, save = true) {
                const msgDiv = document.createElement("div");
                msgDiv.className = `message ${side === 'user' ? 'user-message' : 'bot-message'}`;
                msgDiv.innerHTML = text;
                chatContent.appendChild(msgDiv);
                chatContent.scrollTop = chatContent.scrollHeight;
 
                if (save) {
                    let history = JSON.parse(localStorage.getItem('cube_chat_history')) || [];
                    history.push({ text, side });
                    localStorage.setItem('cube_chat_history', JSON.stringify(history));
                }
            }
 
            async function handleSendMessage() {
                const userMsg = chatInput.value.trim();
                if (userMsg === "") return;
 
                addMessage(userMsg, 'user');
                chatInput.value = "";
 
                const loadingDiv = document.createElement("div");
                loadingDiv.className = "message bot-message";
                loadingDiv.id = "temp-loader";
                loadingDiv.innerHTML = '<span style="opacity:0.6">Expert CUBE réfléchit...</span>';
                chatContent.appendChild(loadingDiv);
 
                try {
                    const response = await axios.post("/chat/ask", { message: userMsg }, {
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' }
                    });
                    document.getElementById("temp-loader").remove();
                    addMessage(response.data.reply, 'bot');
                } catch (error) {
                    if (document.getElementById("temp-loader")) document.getElementById("temp-loader").remove();
                    addMessage("Désolé, une erreur est survenue.", 'bot');
                }
            }
 
            const savedHistory = JSON.parse(localStorage.getItem('cube_chat_history')) || [];
            if (savedHistory.length > 0) {
                savedHistory.forEach(msg => addMessage(msg.text, msg.side, false));
            } else {
                @auth
                    const prenom = {!! json_encode(Auth::user()->prenom_client) !!};
                    addMessage(`Bonjour ${prenom} ! Comment puis-je vous aider aujourd'hui ?`, 'bot', true);
                @else
                    addMessage("Comment puis-je vous aider ? ", 'bot', true);
                @endauth
            }
 
            if (localStorage.getItem('cube_chat_open') === 'true') {
                chatBox.style.display = "flex";
            }
 
            chatButton.onclick = toggleChat;
            closeChatBtn.onclick = toggleChat;
            sendBtn.onclick = handleSendMessage;
            chatInput.onkeypress = (e) => { if (e.key === "Enter") handleSendMessage(); };
 
            clearChat.onclick = () => {
                if(confirm("Voulez-vous effacer la discussion ?")) {
                    localStorage.removeItem('cube_chat_history');
                    chatContent.innerHTML = "";
                    @auth
                        addMessage(`Comment puis-je vous aider, {{ Auth::user()->prenom_client }} ?`, 'bot', true);
                    @else
                        addMessage("Comment puis-je vous aider ?", 'bot', true);
                    @endauth
                }
            };
        });
    </script>
</body>
</html>