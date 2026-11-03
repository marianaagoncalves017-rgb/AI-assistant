// Alterna a visibilidade da janela de chat
function toggleChat() {
    const chatWindow = document.getElementById('chatWindow');
    if (!chatWindow) return;
    
    const isVisible = chatWindow.style.display === 'flex';
    chatWindow.style.display = isVisible ? 'none' : 'flex';
}

// Permite enviar a mensagem ao premir a tecla "Enter"
function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

// Função principal de envio e receção de mensagens
async function sendMessage() {
    const input = document.getElementById('userInput');
    const chatBody = document.getElementById('chatBody');
    
    if (!input || !chatBody) return;

    const question = input.value.trim();

    // Se o campo estiver vazio, não faz nada
    if (!question) return;

    // 1. Mostra a pergunta do utilizador no chat
    appendMessage(question, 'user');
    input.value = '';

    // 2. Cria uma mensagem temporária de "A carregar..."
    const loadingDiv = appendMessage('A pensar...', 'bot');

    try {
        // Prepara os dados para enviar via POST (para o index.php ou endpoint de backend)
        const formData = new FormData();
        formData.append('action', 'chat');
        formData.append('question', question);

        // Envia o pedido à aplicação
        const response = await fetch('index.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Erro na resposta do servidor.');
        }

        const data = await response.json();
        
        // Formata a resposta: converte URLs de texto em hiperligações clicáveis
        const replyText = data.reply || 'Lamento, ocorreu um erro ao obter a resposta.';
        const formattedReply = replyText.replace(
            /(https?:\/\/[^\s]+)/g, 
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
        );
        
        // Atualiza o texto do balão do bot com a resposta formatada
        loadingDiv.innerHTML = formattedReply;

    } catch (error) {
        console.error('Erro na comunicação:', error);
        // Mensagem adequada em caso de indisponibilidade da IA ou falha de rede
        loadingDiv.textContent = 'O assistente está temporariamente indisponível. Por favor, tenta mais tarde.';
    }

    // Faz scroll automático para a última mensagem
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Adiciona um novo balão de mensagem à janela de chat
function appendMessage(text, sender) {
    const chatBody = document.getElementById('chatBody');
    const msg = document.createElement('div');
    
    msg.className = `message ${sender}`;
    msg.textContent = text;
    
    chatBody.appendChild(msg);
    chatBody.scrollTop = chatBody.scrollHeight;
    
    return msg;
}
