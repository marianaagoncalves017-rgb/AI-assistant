let baseConhecimento = {};

// Carrega o ficheiro JSON de conhecimento
async function carregarBD() {
    try {
        const res = await fetch('/api/conhecimento.json');
        baseConhecimento = await res.json();
    } catch (err) {
        console.error("Erro ao carregar ficheiro JSON:", err);
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
        else if (qLower.includes('estagio') || qLower.includes('estágio') || qLower.includes('fct')) {
            reply = baseConhecimento.estagios;
        } 
        else if (qLower.includes('pap') || qLower.includes('aptidao') || qLower.includes('aptidão')) {
            reply = baseConhecimento.pap;
        } 
        else if (qLower.includes('inscrição') || qLower.includes('inscricao') || qLower.includes('vaga') || qLower.includes('candidatura') || qLower.includes('bolsa') || qLower.includes('apoio')) {
            reply = baseConhecimento.inscricoes;
        } 
        else if ((qLower.includes('quanto') || qLower.includes('número') || qLower.includes('numero') || qLower.includes('total')) && (qLower.includes('curso') || qLower.includes('oferta'))) {
            reply = baseConhecimento.quantidadeCursos;
        }
        else if (qLower.includes('curso') || qLower.includes('oferta') || qLower.includes('quais') || qLower.includes('lista')) {
            reply = baseConhecimento.cursos;
        } 
        else if (qLower.includes('contacto') || qLower.includes('email') || qLower.includes('morada') || qLower.includes('cantanhede')) {
            reply = baseConhecimento.contactos;
        } 
        else if (qLower.includes('escola') || qLower.includes('etpc') || qLower.includes('sobre')) {
            reply = baseConhecimento.sobre;
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
