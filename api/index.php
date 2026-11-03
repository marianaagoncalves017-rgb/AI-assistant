<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETPC - Assistente Virtual Inteligente</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #0f172a; color: #f8fafc; height: 100vh; display: flex; flex-direction: column; }
        
        header { background: #1e293b; padding: 1rem 2rem; border-bottom: 1px solid #334155; display: flex; align-items: center; justify-content: space-between; }
        header h1 { font-size: 1.25rem; color: #38bdf8; }
        
        .chat-container { flex: 1; max-width: 800px; width: 100%; margin: 0 auto; display: flex; flex-direction: column; height: calc(100vh - 65px); padding: 1rem; }
        .chat-body { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding: 1rem 0; }
        
        .message { max-width: 80%; padding: 0.8rem 1.2rem; border-radius: 12px; font-size: 0.95rem; line-height: 1.5; }
        .message.bot { background: #1e293b; color: #f8fafc; align-self: flex-start; border: 1px solid #334155; }
        .message.user { background: #0284c7; color: white; align-self: flex-end; }
        .message a { color: #38bdf8; font-weight: bold; word-break: break-all; }
        
        .chat-footer { display: flex; gap: 0.5rem; padding: 1rem 0; }
        .chat-footer input { flex: 1; background: #1e293b; border: 1px solid #334155; color: white; padding: 0.8rem 1.2rem; border-radius: 25px; outline: none; }
        .chat-footer input:focus { border-color: #38bdf8; }
        .chat-footer button { background: #0284c7; color: white; border: none; border-radius: 25px; padding: 0.8rem 1.5rem; font-weight: bold; cursor: pointer; }
        .chat-footer button:hover { background: #0369a1; }
    </style>
</head>
<body>

    <header>
        <h1>ETPC - Assistente Virtual Inteligente (etpc.pt)</h1>
    </header>

    <div class="chat-container">
        <div class="chat-body" id="chatBody">
            <div class="message bot">Olá! Sou o assistente oficial da ETPC. Como posso ajudar com informações sobre a escola ou os nossos cursos?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Escreve a tua pergunta sobre a ETPC..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
        // BASE DE DADOS LOCAL E INTEGRADA DA ETPC
        const baseConhecimento = {
            cursos: "Na ETPC (Escola Profissional Tecnológica do Canto) temos os seguintes Cursos Profissionais de Nível 4 (com equivalência ao 12º ano e estágio incluído):\n\n• Técnico de Gestão e Programação de Sistemas Informáticos\n• Técnico de Eletrónica, Automação e Comando\n• Técnico de Restauração (Cozinha/Pastelaria e Bar)\n• Técnico de Multimédia / Design\n\nPodes saber mais em: https://etpc.pt",
            contactos: "Podes contactar a ETPC através dos seguintes canais:\n\n• Email: geral@etpc.pt\n• Localização: Cantanhede, Portugal\n\nMais detalhes em: https://etpc.pt/contactos",
            sobre: "A ETPC (Escola Profissional Tecnológica do Canto) é uma instituição de ensino profissional em Cantanhede focada na preparação prática de jovens para o mercado de trabalho e prossecução de estudos superiores.\n\nMais detalhes em: https://etpc.pt/",
            inscricoes: "As candidaturas estão abertas para alunos com o 9º ano concluído (ou equivalente). A escola disponibiliza apoios como subsídio de transporte, alimentação e bolsa de material escolar.\n\nInscrições em: https://etpc.pt/inscricoes"
        };

        function handleKeyPress(event) {
            if (event.key === 'Enter') sendMessage();
        }

        async function sendMessage() {
            const input = document.getElementById('userInput');
            const chatBody = document.getElementById('chatBody');
            
            if (!input || !chatBody) return;
            const question = input.value.trim();
            if (!question) return;

            appendMessage(question, 'user');
            input.value = '';

            const loadingDiv = appendMessage('A consultar a base de dados da ETPC...', 'bot');

            // Pequeno atraso para simular o tempo de resposta da IA
            setTimeout(() => {
                const qLower = question.toLowerCase();
                let reply = "";

                // Verificação de perguntas fora do âmbito
                if (qLower.includes('futebol') || qLower.includes('benfica') || qLower.includes('porto') || qLower.includes('sporting') || qLower.includes('politica') || qLower.includes('receita')) {
                    reply = "Lamento, mas sou um assistente exclusivo da ETPC e apenas posso responder a questões sobre a escola, cursos e inscrições.";
                } 
                else if (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('estudar') || qLower.includes('programação') || qLower.includes('multimédia')) {
                    reply = baseConhecimento.cursos;
                } 
                else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('telefone') || qLower.includes('morada') || qLower.includes('onde fica')) {
                    reply = baseConhecimento.contactos;
                } 
                else if (qLower.includes('inscrição') || qLower.includes('inscricao') || qLower.includes('candidatura') || qLower.includes('apoio') || qLower.includes('bolsa')) {
                    reply = baseConhecimento.inscricoes;
                } 
                else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre') || qLower.includes('quem somos')) {
                    reply = baseConhecimento.sobre;
                } 
                else {
                    reply = "Lamento, mas não tenho informação suficiente na base de dados da escola para responder a essa questão. Podes consultar o site oficial em https://etpc.pt/";
                }

                // Converter URLs em links clicáveis
                const formattedReply = reply.replace(
                    /(https?:\/\/[^\s]+)/g, 
                    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
                ).replace(/\n/g, '<br>');

                loadingDiv.innerHTML = formattedReply;
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 600);
        }

        function appendMessage(text, sender) {
            const chatBody = document.getElementById('chatBody');
            const msg = document.createElement('div');
            msg.className = `message ${sender}`;
            msg.textContent = text;
            chatBody.appendChild(msg);
            chatBody.scrollTop = chatBody.scrollHeight;
            return msg;
        }
    </script>
</body>
</html>
