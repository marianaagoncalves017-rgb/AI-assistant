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

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0.4rem 0.2rem;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background-color: #0080cb;
            border-radius: 50%;
            display: inline-block;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
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
        <p>O teu Sucesso começa Aqui! Pergunta-me sobre a oferta formativa, PAP ou FCT.</p>
    </div>

    <div class="chat-container">
        <div class="chat-body" id="chatBody">
            <div class="message bot">Olá! Sou o assistente virtual da ETPC. Como posso ajudar com informações sobre a escola, os cursos profissionais, o estágio (FCT) ou a PAP?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Escreve aqui a tua pergunta..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
        const baseConhecimento = {
            quantidadeCursos: "A ETPC disponibiliza **8 áreas de formação profissional principais**:\n\n1. Informática\n2. Eletrónica e Automação\n3. Restauração (Cozinha/Pastelaria e Bar)\n4. Desporto\n5. Marketing\n6. Administração\n7. Saúde e Bem-Estar\n8. Segurança\n\nSe quiseres saber detalhes de algum curso em específico, basta perguntar!",

            cursos: "A **ETPC (Escola Técnico Profissional de Cantanhede)** disponibiliza Cursos Profissionais de Nível 4 (equivalente ao 12º ano) nas seguintes áreas:\n\n" +
                    "1. **Técnico de Gestão e Programação de Sistemas Informáticos:** Desenvolvimento de software, web e redes.\n" +
                    "2. **Técnico de Eletrónica, Automação e Comando:** Sistemas automatizados, robótica e circuitos.\n" +
                    "3. **Técnico de Restauração (Cozinha/Pastelaria e Bar):** Confeção culinária e serviço de mesa/bar.\n" +
                    "4. **Desporto:** Organização e acompanhamento de atividades físicas e desportivas.\n" +
                    "5. **Marketing:** Comunicação, estratégia digital e vendas.\n" +
                    "6. **Administração:** Gestão documental, financeira e administrativa.\n" +
                    "7. **Saúde e Bem-Estar:** Cuidados auxiliares e apoio de saúde.\n" +
                    "8. **Segurança:** Proteção de bens e pessoas e sistemas de segurança.\n\n" +
                    "Todos os cursos duram 3 anos e incluem estágio (FCT) e a Prova de Aptidão Profissional (PAP).",

            pap: "A **PAP (Prova de Aptidão Profissional)** é um projeto prático individual (ou em pequena equipa) desenvolvido no 3º ano:\n\n" +
                 "• **Objetivo:** Aplicar as competências técnicas adquiridas durante o curso.\n" +
                 "• **Componentes:** Projeto prático, relatório e apresentação perante um júri de avaliação.",

            estagios: "A **FCT (Formação em Contexto de Trabalho)** é o estágio curricular obrigatório em empresas parceiras:\n\n" +
                      "• Permite vivenciar o ambiente real de trabalho e criar contactos com o mercado empresarial.\n" +
                      "• Conta com orientação de um Tutor na empresa e de um Orientador na ETPC.",

            inscricoes: "As **Inscrições para 2026/2027 estão Abertas** para alunos com o 9º ano completo:\n\n" +
                        "• **Apoios Disponíveis:** Subsídio de transporte, alimentação, bolsa de material de estudo e bolsa de estágio.",

            contactos: "Contactos da **ETPC**:\n• Email: geral@etpc.pt\n• Localização: Cantanhede, Portugal",

            sobre: "A **ETPC** é uma escola de ensino profissional de referência em Cantanhede, focada no mercado de trabalho e no acesso ao Ensino Superior."
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

            const typingDiv = appendMessage(
                '<div class="typing-indicator"><span></span><span></span><span></span></div>', 
                'bot', 
                true
            );

            setTimeout(() => {
                const qLower = question.toLowerCase();
                let reply = "";

                if (qLower.includes('futebol') || qLower.includes('benfica') || qLower.includes('porto') || qLower.includes('sporting') || qLower.includes('politica') || qLower.includes('receita')) {
                    reply = "Lamento, mas sou o assistente virtual da ETPC e apenas respondo a questões relacionadas com a escola, cursos e estágios.";
                } 
                // PERGUNTAS SOBRE QUANTIDADE / NÚMERO DE CURSOS
                else if ((qLower.includes('quanto') || qLower.includes('numero') || qLower.includes('número') || qLower.includes('total')) && (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('opcao') || qLower.includes('opção') || qLower.includes('opcoes') || qLower.includes('opções'))) {
                    reply = baseConhecimento.quantidadeCursos;
                }
                // PERGUNTAS SOBRE A LISTA DE CURSOS
                else if (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('quais') || qLower.includes('lista')) {
                    reply = baseConhecimento.cursos;
                } 
                else if (qLower.includes('pap') || qLower.includes('aptidao') || qLower.includes('aptidão') || qLower.includes('juri') || qLower.includes('projeto')) {
                    reply = baseConhecimento.pap;
                } 
                else if (qLower.includes('estagio') || qLower.includes('estágio') || qLower.includes('fct') || qLower.includes('empresa') || qLower.includes('trabalho')) {
                    reply = baseConhecimento.estagios;
                } 
                else if (qLower.includes('inscrição') || qLower.includes('inscricao') || qLower.includes('vaga') || qLower.includes('candidatura') || qLower.includes('bolsa') || qLower.includes('apoio')) {
                    reply = baseConhecimento.inscricoes;
                } 
                else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('morada') || qLower.includes('cantanhede')) {
                    reply = baseConhecimento.contactos;
                } 
                else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre')) {
                    reply = baseConhecimento.sobre;
                } 
                else {
                    reply = "Não encontrei essa informação específica. Podes perguntar-me sobre a quantidade ou lista de **cursos**, a **PAP**, os estágios (**FCT**), os **apoios sociais** ou os **contactos** da ETPC.";
                }

                const formattedReply = reply
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1$2</strong>')
                    .replace(/\n/g, '<br>');

                typingDiv.innerHTML = formattedReply;
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 1000);
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
