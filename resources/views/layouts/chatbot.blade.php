<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        /* --- VARIABLES & DESIGN SYSTEM --- */
        :root {
            --primary-color: #01729e;
            --primary-gradient: linear-gradient(135deg, #01729e, #005f85);
            --bg-color: #ffffff;
            --light-gray: #f4f6f8;
            --text-color: #333333;
            --shadow-soft: 0 12px 40px rgba(0, 0, 0, 0.12);
            --shadow-button: 0 4px 15px rgba(1, 114, 158, 0.4);
            --radius-box: 20px;
            --radius-msg: 18px;
        }

        * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        /* --- BOUTON FLOTTANT --- */
        .chat-button {
            position: fixed; bottom: 30px; right: 30px;
            width: 60px; height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            cursor: pointer;
            box-shadow: var(--shadow-button);
            z-index: 10001;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .chat-button:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 25px rgba(1, 114, 158, 0.5);
        }

        /* --- CORRECTION TAILLE ICONE PRINCIPALE --- */
        .chat-button svg {
            width: 32px;  /* MODIFIER ICI POUR CHANGER LA TAILLE (ex: 40px) */
            height: 32px; /* MODIFIER ICI AUSSI */
            fill: white;  /* Force la couleur blanche */
            transition: transform 0.3s ease;
        }
        
        .chat-button:hover svg {
            transform: scale(1.1);
        }

        /* --- FENÊTRE DE CHAT --- */
        .chat-box {
            position: fixed; bottom: 100px; right: 30px;
            width: 360px; height: 520px;
            background-color: var(--bg-color);
            box-shadow: var(--shadow-soft);
            border-radius: var(--radius-box);
            display: none; flex-direction: column;
            z-index: 10000; overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04);
            animation: slideIn 0.3s ease forwards;
            opacity: 0; transform: translateY(20px);
        }

        @keyframes slideIn { to { opacity: 1; transform: translateY(0); } }
        .chat-box.active { display: flex; }

        /* --- HEADER --- */
        .chat-header {
            background: var(--primary-gradient);
            color: white; padding: 18px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .chat-header-info { display: flex; align-items: center; gap: 12px; }

        .chat-avatar {
            width: 34px; height: 34px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 14px;
        }

        .chat-text-group { display: flex; flex-direction: column; }
        .chat-title { font-weight: 600; font-size: 16px; letter-spacing: 0.3px; }
        .chat-status { font-size: 11px; opacity: 0.85; display: block; margin-top: 2px; }

        .header-actions button {
            background: none; border: none; cursor: pointer;
            color: white; opacity: 0.7; transition: 0.2s;
            font-size: 16px; padding: 6px; border-radius: 6px;
        }
        .header-actions button:hover { opacity: 1; background: rgba(255, 255, 255, 0.15); }

        /* --- CONTENT --- */
        .chat-content {
            flex-grow: 1; padding: 20px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 12px;
            background-color: #fff;
        }
        .chat-content::-webkit-scrollbar { width: 5px; }
        .chat-content::-webkit-scrollbar-thumb { background-color: #ddd; border-radius: 10px; }

        .message {
            padding: 10px 16px;
            border-radius: var(--radius-msg);
            max-width: 85%;
            font-size: 14px;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .bot-message {
            background-color: var(--light-gray);
            align-self: flex-start;
            color: var(--text-color);
            border-bottom-left-radius: 4px;
        }

        .user-message {
            background: var(--primary-gradient);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .chat-content a { color: inherit; text-decoration: underline; font-weight: 500; }

        /* --- FOOTER (INPUT) --- */
        .chat-footer {
            padding: 15px;
            background: white;
            border-top: 1px solid #f0f0f0;
        }

        .input-wrapper {
            position: relative; display: flex; align-items: center;
            background: var(--light-gray);
            border-radius: 30px;
            padding: 5px 5px 5px 15px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .input-wrapper:focus-within {
            background: white;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(1, 114, 158, 0.1);
        }

        #chat-input {
            flex-grow: 1; border: none; background: transparent;
            outline: none; font-size: 14px; color: #333; padding: 8px 0;
        }

        /* --- CORRECTION BOUTON ENVOYER --- */
        .send-btn {
            background: var(--primary-color);
            border: none;
            width: 36px;  /* Un peu plus grand pour l'ergonomie */
            height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
            margin-left: 8px;
            padding: 0; /* Important pour le centrage */
        }

        .send-btn:hover { transform: scale(1.1); }

        .send-btn svg {
            width: 18px;
            height: 18px;
            fill: white; /* L'icône devient blanche */
            margin-left: 2px; /* Petit ajustement optique pour centrer l'avion */
        }
    </style>
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
                    <span class="chat-status">Toujours là pour vous</span>
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
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Sélecteurs
            const chatButton = document.getElementById("chat-button");
            const chatBox = document.getElementById("chat-box");
            const closeChatBtn = document.getElementById("close-chat");
            const chatInput = document.getElementById("chat-input");
            const sendBtn = document.getElementById("send-btn");
            const chatContent = document.getElementById("chat-content");
            const clearChat = document.getElementById("clear-chat");

            // --- FONCTIONS ---

            function toggleChat() {
                if (chatBox.style.display === "flex") {
                    chatBox.style.display = "none";
                    chatBox.classList.remove('active');
                    localStorage.setItem('cube_chat_open', 'false');
                } else {
                    chatBox.style.display = "flex";
                    chatBox.classList.add('active');
                    localStorage.setItem('cube_chat_open', 'true');
                    setTimeout(() => chatInput.focus(), 100);
                }
            }

            function addMessage(text, side, save = true) {
                const msgDiv = document.createElement("div");
                const cssClass = side === 'user' ? 'user-message' : 'bot-message';
                msgDiv.className = `message ${cssClass}`;
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
                chatContent.scrollTop = chatContent.scrollHeight;

                try {
                    const response = await axios.post("/chat/ask", {
                        message: userMsg
                    }, {
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' }
                    });

                    document.getElementById("temp-loader").remove();
                    addMessage(response.data.reply, 'bot');

                } catch (error) {
                    if (document.getElementById("temp-loader")) document.getElementById("temp-loader").remove();
                    console.error(error);
                    addMessage("Désolé, une erreur est survenue. Veuillez réessayer.", 'bot');
                }
            }

            // --- INITIALISATION ---
            const savedHistory = JSON.parse(localStorage.getItem('cube_chat_history')) || [];
            if (savedHistory.length > 0) {
                savedHistory.forEach(msg => addMessage(msg.text, msg.side, false));
            } else {
                addMessage("Bienvenue chez CUBE ! Comment puis-je vous aider ? 🚲", 'bot', true);
            }

            if (localStorage.getItem('cube_chat_open') === 'true') {
                chatBox.style.display = "flex";
                chatBox.classList.add('active');
            }

            // --- EVENT LISTENERS ---
            chatButton.onclick = toggleChat;
            closeChatBtn.onclick = toggleChat;
            sendBtn.onclick = handleSendMessage;
            chatInput.onkeypress = (e) => {
                if (e.key === "Enter") handleSendMessage();
            };

             clearChat.onclick = () => {
                 if(confirm("Voulez-vous effacer toute la discussion ?")) {
                     localStorage.removeItem('cube_chat_history');
                     chatContent.innerHTML = "";
                     addMessage("Discussion réinitialisée. Comment puis-je vous aider ? 🚲", 'bot', true);
                 }
             };
        });
    </script>

</body>
</html>