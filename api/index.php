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
            <div class="message bot">Olá! Sou o assistente oficial da ETPC. Como posso ajudar com informações sobre a escola, cursos, estágios (FCT) ou a PAP?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Pergunta sobre PAP, estágios, cursos ou inscrições..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
        // BASE DE DADOS EXPANDIDA DA ETPC
        const baseConhecimento = {
            pap: "A **PAP (Prova de Aptidão Profissional)** é um projeto prático individual (ou em equipa) desenvolvido ao longo do 3º ano do curso profissional.\n\n• **Objetivo:** Demonstrar os conhecimentos e competências adquiridos ao longo dos 3 anos.\n• **Avaliação:** Apresentação e defesa do projeto perante um júri (professores, direção e elementos externos/empresas).\n• **Exemplo:** Criação de uma aplicação web, instalação de redes, organização de evento multimédia, etc.\n\nPodes saber mais sobre a estrutura curricular em: https://etpc.pt/cursos",

            estagios: "A **FCT (Formação em Contexto de Trabalho)** é o estágio curricular obrigatório e prático integrado nos cursos da ETPC:\n\n• **Duração:** Realizado habitualmente no 2º e/ou 3º ano do curso em empresas parceiras da região.\n• **Objetivo:** Contacto direto com o mercado de trabalho real, aplicar competências técnicas e facilitar a futura contratação.\n• **Acompanhamento:** Tens a orientação de um Tutor na empresa e de um Orientador de Estágio na ETPC.\n\nConsulta mais detalhes em: https://etpc.pt/",

            cursos: "Na ETPC (Escola Profissional Tecnológica do Canto, em Cantanhede) temos os seguintes Cursos Profissionais de Nível 4 (com equivalência ao 12º ano + certificação profissional):\n\n• **Técnico de Gestão e Programação de Sistemas Informáticos**\n• **Técnico de Eletrónica, Automação e Comando**\n• **Técnico de Restauração** (Cozinha/Pastelaria e Bar)\n• **Técnico de Multimédia / Design**\n\nTodos os cursos incluem estágio (FCT) e realização de PAP.\nSaiba mais em: https://etpc.pt/cursos",

            apoios: "A ETPC disponibiliza diversos **Apoios Sociais e Financeiros** para os alunos dos cursos profissionais:\n\n• Subsídio de Transporte / Passe escolar\n• Subsídio de Alimentação (ou refeição na cantina)\n• Bolsa de Material de Estudo (consoante o escalão do Ação Social Escolar)\n• Bolsa de Estágio (durante a FCT, quando aplicável)\n\nInscrições e condições em: https://etpc.pt/inscricoes",

            universidade: "Sim! Os alunos que concluem o ensino profissional na ETPC ficam com o 12º ano completo e **podem prosseguir estudos para o Ensino Superior** (Faculdade ou CPTs/TESP):\n\n• Podem realizar os exames nacionais normais OU aderir às **Vias Especializadas de Acesso** criadas para alunos do Ensino Profissional (concursos especiais sem depender exclusivamente dos exames nacionais gerais).\n\nMais sobre a escola em: https://etpc.pt/",

            contactos: "Podes contactar a ETPC através de:\n\n• **Email:** geral@etpc.pt\n• **Localização:** Cantanhede, Portugal\n\nFormulário e detalhes em: https://etpc.pt/contactos",

            sobre: "A ETPC (Escola Profissional Tecnológica do Canto) é uma escola profissional em Cantanhede focada na formação prática de jovens, preparando-os para entrar diretamente no mercado de trabalho ou seguir para a universidade.\n\nMais informações em: https://etpc.pt/"
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

                // Filtro de perguntas fora do âmbito
                if (qLower.includes('futebol') || qLower.includes('benfica') || qLower.includes('porto') || qLower.includes('sporting') || qLower.includes('politica') || qLower.includes('receita') || qLower.includes('jogo')) {
                    reply = "Lamento, mas sou um assistente exclusivo da ETPC e apenas posso responder a questões sobre a escola, cursos, estágios e funcionamento escolar.";
                } 
                // Perguntas sobre PAP
                else if (qLower.includes('pap') || qLower.includes('aptidao') || qLower.includes('aptidão') || qLower.includes('projeto final') || qLower.includes('juri') || qLower.includes('júri')) {
                    reply = baseConhecimento.pap;
                } 
                // Perguntas sobre Estágios / FCT
                else if (qLower.includes('estagio') || qLower.includes('estágio') || qLower.includes('fct') || qLower.includes('empresa') || qLower.includes('horas de estágio')) {
                    reply = baseConhecimento.estagios;
                } 
                // Perguntas sobre Cursos
                else if (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('programação') || qLower.includes('multimédia') || qLower.includes('eletrónica') || qLower.includes('restauração')) {
                    reply = baseConhecimento.cursos;
                } 
                // Perguntas sobre Acesso à Faculdade / Prossecução de Estudos
                else if (qLower.includes('faculdade') || qLower.includes('universidade') || qLower.includes('ensino superior') || qLower.includes('exames') || qLower.includes('continuar a estudar')) {
                    reply = baseConhecimento.universidade;
                } 
                // Perguntas sobre Apoios e Bolsas
                else if (qLower.includes('bolsa') || qLower.includes('subsídio') || qLower.includes('subsidio') || qLower.includes('transporte') || qLower.includes('cantina') || qLower.includes('alimentação') || qLower.includes('pagam')) {
                    reply = baseConhecimento.apoios;
                } 
                // Contactos
                else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('telefone') || qLower.includes('morada') || qLower.includes('onde fica') || qLower.includes('localização')) {
                    reply = baseConhecimento.contactos;
                } 
                // Sobre a escola / Candidaturas
                else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre') || qLower.includes('inscricao') || qLower.includes('inscrição') || qLower.includes('candidatura')) {
                    reply = baseConhecimento.sobre;
                } 
                // Resposta padrão caso não reconheça
                else {
                    reply = "Lamento, não encontrei uma resposta exata para essa questão na base de dados da ETPC. Podes perguntar sobre os **cursos**, a **PAP**, os **estágios (FCT)**, **apoios/bolsas** ou consultar o site oficial em https://etpc.pt/";
                }

                // Formatar URLs e quebras de linha para HTML
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
