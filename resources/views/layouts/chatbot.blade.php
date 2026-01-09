<div id="chat-button" class="chat-button">
    <div id="chat-icon" class="chat-icon">💬</div>
</div>

<div id="chat-box" class="chat-box">
    <div class="chat-header">
        <span class="chat-title">Expert CUBE</span>
        <button id="clear-chat" class="clear-chat" title="Vider la discussion">🗑️</button>
    </div>
    <div id="chat-content" class="chat-content">
        </div>
    <input type="text" id="chat-input" placeholder="Écrivez un message...">
</div>

<style>
    .chat-button {
        position: fixed; bottom: 20px; right: 20px;
        width: 60px; height: 60px;
        background-color:rgb(1, 114, 158); border-radius: 50%;
        display: flex; justify-content: center; align-items: center;
        cursor: pointer; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        z-index: 10001; transition: transform 0.3s ease;
    }
    .chat-button:hover { transform: scale(1.1); }
    .chat-icon { font-size: 28px; color: white; }

    .chat-box {
        position: fixed; bottom: 90px; right: 20px;
        width: 350px; height: 450px;
        background-color: #fff; border: 1px solid #ddd;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        border-radius: 12px; display: none;
        flex-direction: column; z-index: 10000;
        overflow: hidden;
    }

    .chat-header {
        background-color: rgb(1, 114, 158); color: white; padding: 15px;
        display: flex; justify-content: space-between; align-items: center;
        font-weight: bold;
    }

    /* Style du bouton poubelle */
    .clear-chat {
        background: none; border: none; cursor: pointer;
        font-size: 18px; filter: grayscale(1) brightness(2); /* Rend l'émoji blanc/clair */
        transition: transform 0.2s;
    }
    .clear-chat:hover { transform: scale(1.2); }

    .chat-content {
        flex-grow: 1; padding: 10px; overflow-y: auto;
        display: flex; flex-direction: column; background-color: #f9f9f9;
    }

    .message { margin-bottom: 12px; padding: 10px; border-radius: 10px; max-width: 85%; font-size: 14px; line-height: 1.4; }
    .bot-message { background-color: #eee; align-self: flex-start; color: #333; }
    .user-message { background-color: #4CAF50; color: white; align-self: flex-end; }

    #chat-input { border: none; padding: 15px; width: 100%; box-sizing: border-box; border-top: 1px solid #ddd; outline: none; }

    .chat-product-link { color: #0000EE !important; text-decoration: underline !important; font-weight: normal; }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatInput = document.getElementById("chat-input");
        const chatContent = document.getElementById("chat-content");
        const chatButton = document.getElementById("chat-button");
        const chatBox = document.getElementById("chat-box");
        const clearChat = document.getElementById("clear-chat");

        function addMessage(text, side, save = true) {
            const msgDiv = document.createElement("div");
            msgDiv.className = `message ${side}-message`;
            msgDiv.innerHTML = text; 
            chatContent.appendChild(msgDiv);
            chatContent.scrollTop = chatContent.scrollHeight;

            if (save) {
                let history = JSON.parse(localStorage.getItem('cube_chat_history')) || [];
                history.push({ text, side });
                localStorage.setItem('cube_chat_history', JSON.stringify(history));
            }
        }

        // Chargement initial
        const savedHistory = JSON.parse(localStorage.getItem('cube_chat_history')) || [];
        if (savedHistory.length > 0) {
            savedHistory.forEach(msg => addMessage(msg.text, msg.side, false));
        } else {
            addMessage("Bienvenue chez CUBE ! Comment puis-je vous aider ? 🚲", 'bot', true);
        }

        if (localStorage.getItem('cube_chat_open') === 'true') {
            chatBox.style.display = "flex";
        }

        // TOGGLE : La boule ouvre ET ferme
        chatButton.onclick = () => {
            if (chatBox.style.display === "flex") {
                chatBox.style.display = "none";
                localStorage.setItem('cube_chat_open', 'false');
            } else {
                chatBox.style.display = "flex";
                localStorage.setItem('cube_chat_open', 'true');
            }
        };

        // BOUTON POUBELLE : Vide tout
        clearChat.onclick = () => {
            if(confirm("Voulez-vous effacer la discussion ?")) {
                localStorage.removeItem('cube_chat_history');
                chatContent.innerHTML = "";
                addMessage("Discussion réinitialisée. Comment puis-je vous aider ? 🚲", 'bot', true);
            }
        };

        chatInput.onkeypress = async (e) => {
            if (e.key === "Enter" && chatInput.value.trim() !== "") {
                const userMsg = chatInput.value;
                addMessage(userMsg, 'user');
                chatInput.value = "";

                const loadingDiv = document.createElement("div");
                loadingDiv.className = "message bot-message";
                loadingDiv.id = "temp-loader";
                loadingDiv.textContent = "...";
                chatContent.appendChild(loadingDiv);
                chatContent.scrollTop = chatContent.scrollHeight;

                try {
                    const response = await axios.post("/chat/ask", {
                        message: userMsg
                    }, {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });

                    document.getElementById("temp-loader").remove();
                    addMessage(response.data.reply, 'bot');
                } catch (error) {
                    if(document.getElementById("temp-loader")) document.getElementById("temp-loader").remove();
                    addMessage("Erreur technique. Réessayez.", 'bot');
                }
            }
        };
    });
</script>