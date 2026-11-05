<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETPC - Assistente Virtual Inteligente</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        body { 
            background-color: #f4f7f9; 
            color: #2c3e50; 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
        }
        
        .top-bar {
            background: #005689;
            color: white;
            padding: 5px 2rem;
            font-size: 0.8rem;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        header { 
            background: #ffffff; 
            padding: 0.8rem 2rem; 
            border-bottom: 3px solid #e67e22; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-brand img {
            height: 50px;
            width: auto;
        }
        
        header h1 { 
            font-size: 1.15rem; 
            color: #0080cb; 
            font-weight: 700;
        }
        
        .hero-banner {
            background: linear-gradient(135deg, #0080cb, #005689);
            color: white;
            padding: 1.2rem 2rem;
            text-align: center;
        }

        .hero-banner h2 { font-size: 1.3rem; margin-bottom: 4px; }
        .hero-banner p { font-size: 0.9rem; opacity: 0.9; }

        .chat-container { 
            flex: 1; 
            max-width: 850px; 
            width: 100%; 
            margin: 0 auto; 
            display: flex; 
            flex-direction: column; 
            height: calc(100vh - 150px); 
            padding: 1rem; 
        }

        .chat-body { 
            flex: 1; 
            overflow-y: auto; 
            display: flex; 
            flex-direction: column; 
            gap: 1rem; 
            padding: 1rem 0; 
        }
        
        .message { 
            max-width: 80%; 
            padding: 0.9rem 1.2rem; 
            border-radius: 12px; 
            font-size: 0.95rem; 
            line-height: 1.5; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .message.bot { 
            background: #ffffff; 
            color: #2c3e50; 
            align-self: flex-start; 
            border-left: 5px solid #0080cb;
        }

        .message.user { 
            background: #0080cb; 
            color: white; 
            align-self: flex-end; 
            border-bottom-right-radius: 2px;
        }

        .message a { 
            color: #0080cb; 
            font-weight: bold; 
            word-break: break-all; 
        }

        .chat-footer { 
            display: flex; 
            gap: 0.5rem; 
            padding: 1rem 0; 
        }

        .chat-footer input { 
            flex: 1; 
            background: #ffffff; 
            border: 2px solid #0080cb; 
            color: #2c3e50; 
            padding: 0.8rem 1.2rem; 
            border-radius: 25px; 
            outline: none; 
            font-size: 0.95rem;
        }

        .chat-footer input:focus { 
            border-color: #e67e22; 
        }

        .chat-footer button { 
            background: linear-gradient(135deg, #e67e22, #f39c12); 
            color: white; 
            border: none; 
            border-radius: 25px; 
            padding: 0.8rem 1.8rem; 
            font-weight: bold; 
            cursor: pointer; 
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <span>E-COMMUNITY</span>
        <span>RECRUTAMENTO</span>
    </div>

    <header>
        <div class="header-brand">
            <img src="/Logo_ETPC.png" alt="Logótipo ETPC" id="etpcLogo">
            <h1>ETPC - Assistente Virtual Oficial</h1>
        </div>
    </header>

    <div class="hero-banner">
        <h2>Inscrições Abertas 2026/2027</h2>
        <p>O teu Sucesso começa Aqui! Respostas rápidas com dados oficiais da escola.</p>
    </div>

    <div class="chat-container">
        <div class="chat-body" id="chatBody">
            <div class="message bot">Olá! Sou o assistente oficial da ETPC. Em que posso ajudar relativamente aos nossos cursos, estágios ou funcionamento da escola?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Escreve aqui a tua pergunta..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
        const baseConhecimento = {
            cursos: "A **ETPC (Escola Técnico Profissional de Cantanhede)** dispõe dos seguintes cursos profissionais de Nível 4 (com equivalência ao 12º ano):\n\n• **Técnico de Gestão e Programação de Sistemas Informáticos**\n• **Técnico de Eletrónica, Automação e Comando**\n• **Técnico de Restauração** (Cozinha/Pastelaria e Bar)\n• **Áreas de Administração, Marketing, Desporto, Saúde e Segurança**\n\nTodos os cursos integram estágio (FCT) e a elaboração da PAP.\nMais informações: https://etpc.pt/cursos",
            pap: "A **PAP (Prova de Aptidão Profissional)** é o projeto prático final elaborado no 3º ano do curso profissional:\n\n• **Em que consiste:** Apresentação e defesa de um projeto prático desenvolvido pelo aluno perante um júri de avaliação.\n• **Objetivo:** Demonstrar a aplicação das competências técnicas adquiridas ao longo do curso.\n\nMais detalhes em: https://etpc.pt/",
            estagios: "A **FCT (Formação em Contexto de Trabalho)** é o estágio curricular obrigatório da ETPC:\n\n• Realizado em empresas parceiras da região para proporcionar contacto real com o mercado de trabalho.\n\nSaiba mais em: https://etpc.pt/cte",
            inscricoes: "As **Inscrições para 2026/2027 estão abertas** na ETPC para alunos com o 9º ano concluído. A escola faculta apoios como transporte, cantina e bolsas escolar.\n\nInscrições em: https://etpc.pt/inscricoes",
            contactos: "Contactos da ETPC:\n• Email: geral@etpc.pt\n• Localização: Cantanhede, Portugal\n\nDetalhes completos em: https://etpc.pt/contactos",
            sobre: "A ETPC é uma instituição de ensino profissional em Cantanhede focada na preparação prática de alunos para o mercado de trabalho e universidade.\n\nWebsite: https://etpc.pt/"
        };

        function handleKeyPress(event) {
            if (event.key === 'Enter') sendMessage();
        }

        function sendMessage() {
            const input = document.getElementById('userInput');
            const chatBody = document.getElementById('chatBody');
            
            if (!input || !chatBody) return;
            const question = input.value.trim();
            if (!question) return;

            appendMessage(question, 'user');
            input.value = '';

            const qLower = question.toLowerCase();
            let reply = "";

            if (qLower.includes('futebol') || qLower.includes('benfica') || qLower.includes('porto') || qLower.includes('sporting') || qLower.includes('politica') || qLower.includes('receita')) {
                reply = "Lamento, mas apenas respondo a assuntos relacionados com a ETPC, cursos e funcionamento escolar.";
            } 
            else if (qLower.includes('curso') || qLower.includes('exist') || qLower.includes('ha') || qLower.includes('há') || qLower.includes('oferta') || qLower.includes('quais')) {
                reply = baseConhecimento.cursos;
            } 
            else if (qLower.includes('pap') || qLower.includes('aptidao') || qLower.includes('aptidão') || qLower.includes('juri')) {
                reply = baseConhecimento.pap;
            } 
            else if (qLower.includes('estagio') || qLower.includes('estágio') || qLower.includes('fct') || qLower.includes('empresa')) {
                reply = baseConhecimento.estagios;
            } 
            else if (qLower.includes('inscrição') || qLower.includes('inscricao') || qLower.includes('vaga') || qLower.includes('candidatura')) {
                reply = baseConhecimento.inscricoes;
            } 
            else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('morada') || qLower.includes('cantanhede')) {
                reply = baseConhecimento.contactos;
            } 
            else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre')) {
                reply = baseConhecimento.sobre;
            } 
            else {
                reply = "Podes consultar toda a informação detalhada sobre esse tema diretamente no site oficial da escola: https://etpc.pt/";
            }

            const formattedReply = reply
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>')
                .replace(/\n/g, '<br>');

            appendMessage(formattedReply, 'bot', true);
        }

        function appendMessage(text, sender, isHTML = false) {
            const chatBody = document.getElementById('chatBody');
            const msg = document.createElement('div');
            msg.className = `message ${sender}`;
            if (isHTML) {
                msg.innerHTML = text;
            } else {
                msg.textContent = text;
            }
            chatBody.appendChild(msg);
            chatBody.scrollTop = chatBody.scrollHeight;
            return msg;
        }
    </script>
</body>
</html>
