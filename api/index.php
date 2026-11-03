<?php
// Configuração da API do Gemini (Lê variável de ambiente da Vercel ou usa a chave direta como fallback)
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AQ.Ab8RN6LBjWPLZmydiSBIKwwYQ4i2GdbCopLbx8jZMR4Uj-ukXg');

// Processamento da pergunta via AJAX / POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'chat') {
    header('Content-Type: application/json');
    
    $userQuestion = trim($_POST['question'] ?? '');

    if (empty($userQuestion)) {
        echo json_encode(['error' => 'Pergunta vazia.']);
        exit;
    }

    // BASE DE DADOS DE CONHECIMENTO REAL DO SITE DA ETPC (https://etpc.pt/)
    $escolaContexto = "
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
    ";

    // SYSTEM PROMPT - Ancoragem rigorosa no contexto do site etpc.pt
    $systemInstruction = "
    És o assistente virtual oficial da ETPC - Escola Profissional Tecnológica do Canto (disponível em https://etpc.pt/).
    
    REGRAS STRICTAS DE RESPOSTA:
    1. Responde APENAS utilizando a informação fornecida no CONTEXTO DA ETPC abaixo (extraído do site oficial etpc.pt).
    2. Se a resposta à pergunta não estiver presente ou não for possível confirmá-la no CONTEXTO DA ETPC, responde exatamente: 'Lamento, mas não tenho informação suficiente no site da escola para responder a essa questão.'
    3. Se o utilizador perguntar sobre assuntos alheios à escola (ex: futebol, culinária geral, política, receitas, jogos), recusa educadamente responder, indicando que apenas respondes a questões sobre a ETPC.
    4. Não inventes dados nem faças suposições.
    5. Sempre que responderes a uma dúvida válida, inclui no final o link correspondente da página oficial do site https://etpc.pt/ que fundamenta essa resposta.
    
    CONTEXTO DA ETPC:
    " . $escolaContexto;

    // Chamada à API do Gemini (Uso do modelo gemini-1.5-flash)
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $systemInstruction . "\n\nPERGUNTA DO UTILIZADOR: " . $userQuestion]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Erro ao processar resposta.';
        echo json_encode(['reply' => $reply]);
    } else {
        echo json_encode(['reply' => 'O assistente está temporariamente indisponível. Por favor, tenta mais tarde.']);
    }
    exit;
}
?>
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
                const formData = new FormData();
                formData.append('action', 'chat');
                formData.append('question', question);

                const response = await fetch('/api/index.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Erro no servidor.');

                const data = await response.json();
                const replyText = data.reply || 'Lamento, ocorreu um erro ao processar a resposta.';
                
                // Formatar URLs para hiperligações clicáveis
                const formattedReply = replyText.replace(
                    /(https?:\/\/[^\s]+)/g, 
                    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
                );
                
                loadingDiv.innerHTML = formattedReply;

            } catch (error) {
                loadingDiv.textContent = 'O assistente está temporariamente indisponível. Por favor, tenta mais tarde.';
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
