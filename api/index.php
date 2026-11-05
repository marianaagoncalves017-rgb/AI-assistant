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
        
        /* Top Header ao estilo do site etpc.pt */
        .top-bar {
            background: #005689;
            color: white;
            padding: 5px 2rem;
            font-size: 0.8rem;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .top-bar a { color: white; text-decoration: none; font-weight: 600; }

        /* Header Principal */
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
        
        /* Faixa Azul Principal (Banner) */
        .hero-banner {
            background: linear-gradient(135deg, #0080cb, #005689);
            color: white;
            padding: 1.2rem 2rem;
            text-align: center;
        }

        .hero-banner h2 { font-size: 1.3rem; margin-bottom: 4px; }
        .hero-banner p { font-size: 0.9rem; opacity: 0.9; }

        /* Contentor do Chat */
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
        
        /* Balões de Mensagem */
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
        
        .message.user a {
            color: #ffd194;
        }

        /* Rodapé de Envio */
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
            transition: transform 0.1s;
        }

        .chat-footer button:hover { 
            transform: scale(1.02);
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
            <img src="/Logo_ETPC.png" alt="ETPC Logo" onerror="this.style.display='none'">
            <h1>ETPC - Assistente Virtual Oficial</h1>
        </div>
    </header>

    <div class="hero-banner">
        <h2>Inscrições Abertas 2026/2027</h2>
        <p>O teu Sucesso começa Aqui! Pergunta-me sobre os cursos, a PAP ou a FCT.</p>
    </div>

    <div class="chat-container">
        <div class="chat-body" id="chatBody">
            <div class="message bot">Olá! Sou o assistente oficial da ETPC (Escola Técnico Profissional de Cantanhede). Em que posso ajudar relativamente aos nossos cursos, estágios ou funcionamento da escola?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Escreve aqui a tua pergunta..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
        // BASE DE DADOS BASEADA NO SITE REAL DA ETPC
        const baseConhecimento = {
            cursos: "A **ETPC (Escola Técnico Profissional de Cantanhede)** oferece cursos profissionais de Nível 4 (com equivalência ao 12º ano) nas seguintes áreas de formação:\n\n• **Informática** (Técnico de Gestão e Programação de Sistemas Informáticos)\n• **Automação** (Técnico de Eletrónica, Automação e Comando)\n• **Restauração** (Cozinha/Pastelaria e Bar)\n• **Administração**\n• **Marketing**\n• **Desporto**\n• **Saúde e Bem-Estar**\n• **Segurança**\n\nTodos os cursos incluem **FCT (Estágio)** e a realização da **PAP**.\nConsulte a oferta em: https://etpc.pt/cursos",

            pap: "A **PAP (Prova de Aptidão Profissional)** é o projeto prático final realizado pelos alunos do 3º ano da ETPC.\n\n• **Em que consiste:** É a criação de um projeto prático e a sua apresentação perante um júri de avaliação (professores, direção e representantes de empresas parceiras).\n• **Objetivo:** Demonstrar a autonomia e todas as competências técnicas desenvolvidas nos 3 anos de curso.\n\nMais sobre os projetos da escola em: https://etpc.pt/",

            estagios: "A **FCT (Formação em Contexto de Trabalho)** representa a componente prática de estágio na ETPC:\n\n• **Funcionamento:** Os alunos realizam o estágio em empresas da região parceiras da escola para aplicarem os conhecimentos aprendidos nas aulas.\n• **Vantagem:** Permite aos alunos ganhar experiência profissional real e ter a oportunidade de conseguir contrato de trabalho após o curso.\n\nMais informações em: https://etpc.pt/cte",

            inscricoes: "As **Inscrições para o Ano Letivo 2026/2027 estão Abertas** na ETPC para alunos com o 9º ano completo!\n\n• A escola oferece apoios sociais (subsídio de transporte, alimentação e apoio escolar de acordo com as condições aplicáveis).\n\nGarante a tua vaga em: https://etpc.pt/inscricoes",

            contactos: "Contactos da ETPC - Escola Técnico Profissional de Cantanhede:\n\n• **Localização:** Cantanhede, Portugal\n• **Email:** geral@etpc.pt\n\nVisualiza os contactos completos em: https://etpc.pt/contactos",

            sobre: "A ETPC (Escola Técnico Profissional de Cantanhede) é uma instituição de referência na formação profissional, focada na ligação prática com o tecido empresarial e na preparação para o mercado de trabalho e universidade.\n\nMais informações em: https://etpc.pt/"
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

            const loadingDiv = appendMessage('A consultar o assistente...', 'bot');

            setTimeout(() => {
                const qLower = question.toLowerCase();
                let reply = "";

                if (qLower.includes('futebol') || qLower.includes('benfica') || qLower.includes('porto') || qLower.includes('sporting') || qLower.includes('politica') || qLower.includes('receita')) {
                    reply = "Lamento, mas sou o assistente oficial da ETPC e apenas respondo a questões relacionadas com a escola, oferta formativa, estágios e procedimentos escolares.";
                } 
                else if (qLower.includes('pap') || qLower.includes('aptidao') || qLower.includes('aptidão') || qLower.includes('projeto') || qLower.includes('juri')) {
                    reply = baseConhecimento.pap;
                } 
                else if (qLower.includes('estagio') || qLower.includes('estágio') || qLower.includes('fct') || qLower.includes('empresa') || qLower.includes('trabalho')) {
                    reply = baseConhecimento.estagios;
                } 
                else if (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('informática') || qLower.includes('informatica') || qLower.includes('automação') || qLower.includes('restauração') || qLower.includes('desporto') || qLower.includes('marketing') || qLower.includes('saúde') || qLower.includes('segurança')) {
                    reply = baseConhecimento.cursos;
                } 
                else if (qLower.includes('inscrição') || qLower.includes('inscricao') || qLower.includes('vaga') || qLower.includes('ano letivo') || qLower.includes('candidatura')) {
                    reply = baseConhecimento.inscricoes;
                } 
                else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('telefone') || qLower.includes('onde fica') || qLower.includes('cantanhede')) {
                    reply = baseConhecimento.contactos;
                } 
                else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre')) {
                    reply = baseConhecimento.sobre;
                } 
                else {
                    reply = "Lamento, não encontrei essa informação na base de dados da ETPC. Podes perguntar-me sobre os **cursos**, as **inscrições**, a **PAP**, a **FCT (estágios)** ou aceder ao site em https://etpc.pt/";
                }

                const formattedReply = reply
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>')
                    .replace(/\n/g, '<br>');

                loadingDiv.innerHTML = formattedReply;
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 500);
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
