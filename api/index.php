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
            cursos: "A **ETPC (Escola Técnico Profissional de Cantanhede)** disponibiliza Cursos Profissionais de Nível 4 (com equivalência ao 12º ano e certificação profissional). As áreas de formação incluem:\n\n" +
                    "1. **Técnico de Gestão e Programação de Sistemas Informáticos:** Foco no desenvolvimento de software, aplicações web, bases de dados e redes de computadores.\n" +
                    "2. **Técnico de Eletrónica, Automação e Comando:** Aprendizagem em sistemas automatizados, robótica industrial, circuitos eletrónicos e domótica.\n" +
                    "3. **Técnico de Restauração (Cozinha/Pastelaria e Bar):** Preparação prática na área da restauração, confeção culinária e serviço de mesa/bar.\n" +
                    "4. **Técnico de Multimédia / Design:** Design gráfico, edição de vídeo/áudio, modelação 3D e desenvolvimento para web.\n" +
                    "5. **Outras Áreas Formativas:** A escola disponibiliza também oferta nas áreas de Administração, Marketing, Desporto, Saúde e Segurança.\n\n" +
                    "Todos os cursos têm a duração de 3 anos e incluem componente prática em contexto de trabalho (estágio FCT) e a Prova de Aptidão Profissional (PAP).",

            pap: "A **PAP (Prova de Aptidão Profissional)** é um projeto prático individual (ou em pequena equipa) desenvolvido ao longo do 3º ano do curso:\n\n" +
                 "• **Objetivo:** Aplicar de forma autónoma as competências técnicas adquiridas durante a formação.\n" +
                 "• **Componentes:** Elaboração do projeto, redação de um relatório detalhado e a apresentação pública.\n" +
                 "• **Avaliação:** Defesa do projeto perante um júri constituído pela direção da escola, professores e elementos externos/empresários do setor.",

            estagios: "A **FCT (Formação em Contexto de Trabalho)** é o estágio curricular obrigatório em empresas parceiras:\n\n" +
                      "• **Duração:** Realizada ao longo do curso (habitualmente no 2º e 3º anos).\n" +
                      "• **Objetivo:** Vivenciar o ambiente real de trabalho, adquirir hábitos profissionais e criar contactos com o tecido empresarial.\n" +
                      "• **Acompanhamento:** Cada aluno tem a orientação de um Tutor na empresa e de um Orientador de Estágio na ETPC.",

            inscricoes: "As **Inscrições para o Ano Letivo 2026/2027 estão Abertas** para alunos com o 9º ano completo:\n\n" +
                        "• **Apoios Disponíveis:** Subsídio de transporte / passe escolar, subsídio de alimentação (ou refeição gratuita na cantina), bolsa de material de estudo (consoante o escalão) e bolsa de estágio na FCT.\n" +
                        "• **Processo:** Podes realizar a pré-inscrição junto dos serviços administrativos da escola ou submeter a candidatura diretamente.",

            contactos: "Contactos Oficiais da **ETPC (Escola Técnico Profissional de Cantanhede)**:\n\n" +
                       "• **Morada:** Cantanhede, Portugal\n" +
                       "• **Email:** geral@etpc.pt\n" +
                       "• **Atendimento:** De Segunda a Sexta-feira, no horário de funcionamento dos serviços administrativos.",

            sobre: "A **ETPC (Escola Técnico Profissional de Cantanhede)** é uma instituição focada no ensino profissional de excelência, aproximando os alunos do mercado de trabalho e preparando-os também para a prossecução de estudos no Ensino Superior."
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
                reply = "Lamento, mas sou o assistente virtual da ETPC e apenas respondo a questões relacionadas com a escola, os cursos, os estágios e procedimentos escolares.";
            } 
            else if (qLower.includes('curso') || qLower.includes('exist') || qLower.includes('ha') || qLower.includes('há') || qLower.includes('oferta') || qLower.includes('quais')) {
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
            else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('morada') || qLower.includes('onde fica') || qLower.includes('cantanhede')) {
                reply = baseConhecimento.contactos;
            } 
            else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre')) {
                reply = baseConhecimento.sobre;
            } 
            else {
                reply = "Não encontrei essa informação específica. Podes perguntar-me detalhadamente sobre a lista de **cursos**, a realização da **PAP**, os estágios em empresa (**FCT**), os **apoios sociais** ou os **contactos** da ETPC.";
            }

            const formattedReply = reply
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
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
