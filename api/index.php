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
        // Chave de API do Gemini direta no Frontend
        const GEMINI_API_KEY = "AQ.Ab8RN6LBjWPLZmydiSBIKwwYQ4i2GdbCopLbx8jZMR4Uj-ukXg";

        // Contexto Oficial do site etpc.pt
        const escolaContexto = `
        [INFORMACÃO GERAL E SOBRE A ESCOLA]
        - Nome: ETPC - Escola Profissional Tecnológica do Canto (Cantanhede)
        - Descrição: A ETPC é uma instituição de ensino profissional focada na preparação prática de jovens para o mercado de trabalho e prossecução de estudos superiores, oferecendo cursos com Formação em Contexto de Trabalho (estágios).
        - Página Sobre: https://etpc.pt/

        [CONTACTOS E LOCALIZAÇÃO REAL]
        - Morada: Cantanhede, Portugal
        - Email de Contacto: geral@etpc.pt
        - Página de Contactos: https://etpc.pt/contactos

        [OFERTA FORMATIVA / CURSOS PROFISSIONAIS DA ETPC]
        - Cursos Profissionais de Nível 4 (Equivalência ao 12º ano + Certificação Profissional):
          * Técnico de Gestão e Programação de Sistemas Informáticos
          * Técnico de Eletrónica, Automação e Comando
          * Técnico de Restauração (Cozinha/Pastelaria e Bar)
          * Técnico de Multimédia / Design
        - Página de Cursos: https://etpc.pt/cursos

        [ADMISSÕES E INSCRIÇÕES]
        - Candidaturas: Abertas para alunos que concluíram o 9º ano de escolaridade (ou equivalente).
        - Apoios aos Alunos: Possibilidade de subsídio de transporte, alimentação e bolsa de material escolar consoante as condições aplicáveis.
        - Página de Inscrições: https://etpc.pt/inscricoes
        `;

        const systemInstruction = `
        És o assistente virtual oficial da ETPC - Escola Profissional Tecnológica do Canto (disponível em https://etpc.pt/).
        
        REGRAS STRICTAS DE RESPOSTA:
        1. Responde APENAS utilizando a informação fornecida no CONTEXTO DA ETPC abaixo (extraído do site oficial etpc.pt).
        2. Se a resposta à pergunta não estiver presente no CONTEXTO DA ETPC, responde exatamente: 'Lamento, mas não tenho informação suficiente no site da escola para responder a essa questão.'
        3. Se o utilizador perguntar sobre assuntos alheios à escola (ex: futebol, culinária geral, política, jogos), recusa educadamente responder.
        4. Não inventes dados.
        5. Sempre que responderes a uma dúvida válida, inclui no final o link correspondente da página oficial do site https://etpc.pt/ que fundamenta essa resposta.
        
        CONTEXTO DA ETPC:
        ${escolaContexto}
        `;

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

            const loadingDiv = appendMessage('A consultar o site da escola...', 'bot');

            try {
                // Chamada direta à API da Google Gemini
                const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contents: [{
                            parts: [{ text: `${systemInstruction}\n\nPERGUNTA DO UTILIZADOR: ${question}` }]
                        }]
                    })
                });

                if (!response.ok) {
                    throw new Error(`Erro na API: ${response.status}`);
                }

                const data = await response.json();
                const replyText = data.candidates?.[0]?.content?.parts?.[0]?.text || 'Lamento, ocorreu um erro ao processar a resposta.';
                
                // Converte links de texto em hiperligações HTML clicáveis
                const formattedReply = replyText.replace(
                    /(https?:\/\/[^\s]+)/g, 
                    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
                );
                
                loadingDiv.innerHTML = formattedReply;

            } catch (error) {
                console.error('Erro de Comunicação:', error);
                loadingDiv.textContent = 'Erro ao contactar a API do Gemini. Verifica a tua ligação ou chave de API.';
            }

            chatBody.scrollTop = chatBody.scrollHeight;
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
