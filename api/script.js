let baseConhecimento = {};

// Carrega o ficheiro JSON localizado na raiz do projeto
async function carregarBD() {
    try {
        const res = await fetch('/conhecimento.json');
        baseConhecimento = await res.json();
    } catch (err) {
        console.error("Erro ao carregar conhecimento.json:", err);
    }
}
carregarBD();

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
        // 1. ESTÁGIOS / FCT
        else if (qLower.includes('estagio') || qLower.includes('estágio') || qLower.includes('fct')) {
            if (baseConhecimento.estagios) {
                reply = `A **FCT (Formação em Contexto de Trabalho)** é o estágio curricular obrigatório:\n\n• **Duração:** ${baseConhecimento.estagios.horas} (distribuídas pelo ${baseConhecimento.estagios.anos}).\n• ${baseConhecimento.estagios.descricao}`;
            } else {
                reply = "A FCT é o estágio curricular obrigatório em empresas parceiras com duração habitualmente entre 400 a 420 horas.";
            }
        } 
        // 2. PAP
        else if (qLower.includes('pap') || qLower.includes('aptidao') || qLower.includes('aptidão')) {
            reply = baseConhecimento.pap ? baseConhecimento.pap.descricao : "A PAP é o projeto prático final realizado no 3º ano.";
        } 
        // 3. INSCRIÇÕES / BOLSA
        else if (qLower.includes('inscrição') || qLower.includes('inscricao') || qLower.includes('vaga') || qLower.includes('candidatura') || qLower.includes('bolsa') || qLower.includes('apoio')) {
            reply = "As **Inscrições para 2026/2027 estão Abertas** na ETPC com vários apoios disponíveis (transporte, alimentação e bolsas de estudo).";
        } 
        // 4. QUANTIDADE DE CURSOS
        else if ((qLower.includes('quanto') || qLower.includes('número') || qLower.includes('numero') || qLower.includes('total')) && (qLower.includes('curso') || qLower.includes('oferta'))) {
            if (baseConhecimento.cursos && baseConhecimento.cursos.lista_profissionais) {
                const lista = baseConhecimento.cursos.lista_profissionais.map((c, i) => `${i + 1}. ${c}`).join('\n');
                reply = `A ETPC disponibiliza **${baseConhecimento.cursos.quantidade} Cursos Profissionais de Nível 4**:\n\n${lista}`;
            } else {
                reply = "A ETPC disponibiliza 11 Cursos Profissionais de Nível 4 com equivalência ao 12º ano.";
            }
        }
        // 5. LISTA DE CURSOS
        else if (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('quais') || qLower.includes('lista')) {
            if (baseConhecimento.cursos) {
                const prof = baseConhecimento.cursos.lista_profissionais.map(c => `• ${c}`).join('\n');
                const ctesp = baseConhecimento.cursos.ctesp.map(c => `• ${c}`).join('\n');
                const adultos = baseConhecimento.cursos.adultos.map(c => `• ${c}`).join('\n');
                
                reply = `A **oferta formativa completa da ETPC** inclui:\n\n**Cursos Profissionais (Nível 4):**\n${prof}\n\n**CTeSP:**\n${ctesp}\n\n**Adultos:**\n${adultos}`;
            } else {
                reply = "A ETPC disponibiliza cursos em várias áreas profissionais, CTeSP e Formação para Adultos.";
            }
        } 
        // 6. CONTACTOS
        else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('morada') || qLower.includes('cantanhede')) {
            if (baseConhecimento.contactos) {
                reply = `Contactos da **ETPC**:\n• Email: ${baseConhecimento.contactos.email}\n• Localização: ${baseConhecimento.contactos.localizacao}`;
            } else {
                reply = "Contactos da ETPC: Email geral@etpc.pt | Cantanhede, Portugal.";
            }
        } 
        // 7. SOBRE A ESCOLA
        else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre')) {
            reply = "A **ETPC** é uma escola de ensino profissional de referência em Cantanhede, focada na ligação ao mercado de trabalho e preparação para a universidade.";
        } 
        else {
            reply = "Não encontrei essa informação específica. Podes perguntar-me sobre os cursos, a **PAP**, os estágios (**FCT**), as **horas de estágio**, os **apoios sociais** ou os **contactos** da ETPC.";
        }

        const formattedReply = reply
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
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
