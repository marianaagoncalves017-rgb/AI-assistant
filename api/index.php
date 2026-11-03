<?php
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AQ.Ab8RN6LBjWPLZmydiSBIKwwYQ4i2GdbCopLbx8jZMR4Uj-ukXg');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'chat') {
    header('Content-Type: application/json');
    
    $userQuestion = trim($_POST['question'] ?? '');

    if (empty($userQuestion)) {
        echo json_encode(['error' => 'Pergunta vazia.']);
        exit;
    }

    $escolaContexto = "
    [CONTACTOS E LOCALIZAÇÃO]
    - Nome: ETPC - Escola Profissional Tecnológica do Canto
    - Morada: Rua do Canto, Coimbra, Portugal
    - Telefone: +351 239 000 000
    - Email: geral@etpc.pt
    - Página de Contactos: https://www.etpc.pt/contactos

    [CURSOS DISPONÍVEIS]
    - Curso Técnico de Gestão e Programação de Sistemas Informáticos (3 anos, Nível 4 EQF).
    - Curso Técnico de Multimédia (3 anos, Nível 4 EQF).
    - Curso Técnico de Eletrónica, Automação e Comando (3 anos, Nível 4 EQF).
    - Página dos Cursos: https://www.etpc.pt/cursos

    [SOBRE A ESCOLA]
    - A ETPC é uma escola profissional focada na preparação para o mercado de trabalho e prossecução de estudos.
    - Oferece estágios integrados (Formação em Contexto de Trabalho) nas empresas da região.
    - Página Sobre: https://www.etpc.pt/sobre
    ";

    $systemInstruction = "
    És o assistente virtual oficial da Escola Profissional Tecnológica do Canto (ETPC).
    
    REGRAS STRICTAS:
    1. Responde APENAS com base no CONTEXTO DA ETPC fornecido abaixo.
    2. Se a resposta para a pergunta não estiver explicitamente no CONTEXTO DA ETPC, responde exatamente: 'Lamento, mas não tenho informação suficiente no site da escola para responder a essa questão.'
    3. Se a pergunta for sobre futebol, política, culinária ou qualquer assunto não relacionado com a ETPC, recusa responder.
    4. Não inventes dados e inclui o link correspondente da página da ETPC que fundamenta a resposta.
    
    CONTEXTO DA ETPC:
    " . $escolaContexto;

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;

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
    <title>Assistente Virtual ETPC</title>
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
        .message a { color: #38bdf8; font-weight: bold; }
        
        .chat-footer { display: flex; gap: 0.5rem; padding: 1rem 0; }
        .chat-footer input { flex: 1; background: #1e293b; border: 1px solid #334155; color: white; padding: 0.8rem 1.2rem; border-radius: 25px; outline: none; }
        .chat-footer input:focus { border-color: #38bdf8; }
        .chat-footer button { background: #0284c7; color: white; border: none; border-radius: 25px; padding: 0.8rem 1.5rem; font-weight: bold; cursor: pointer; }
        .chat-footer button:hover { background: #0369a1; }
    </style>
</head>
<body>

    <header>
        <h1>ETPC - Assistente Virtual Inteligente</h1>
    </header>

    <div class="chat-container">
        <div class="chat-body" id="chatBody">
            <div class="message bot">Olá! Sou o assistente oficial da ETPC. Como posso ajudar com informações sobre a escola ou cursos?</div>
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

                // Aponta para a rota correta do PHP na Vercel
                const response = await fetch('/api/index.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Erro no servidor.');

                const data = await response.json();
                const replyText = data.reply || 'Lamento, ocorreu um erro ao processar a resposta.';
                
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
