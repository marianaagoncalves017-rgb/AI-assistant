<?php
// Configuração da API do Gemini (Substitui pela tua chave do Google AI Studio)
define('GEMINI_API_KEY', 'A_TUA_CHAVE_API_AQUI');

// Processamento da pergunta via AJAX / POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'chat') {
    header('Content-Type: application/json');
    
    $userQuestion = trim($_POST['question'] ?? '');

    if (empty($userQuestion)) {
        echo json_encode(['error' => 'Pergunta vazia.']);
        exit;
    }

    // 1. BASE DE DADOS DE CONHECIMENTO DA ETPC (Extraída do site oficial)
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

    // 2. SYSTEM PROMPT (Regras rigorosas para a IA)
    $systemInstruction = "
    És o assistente virtual oficial da Escola Profissional Tecnológica do Canto (ETPC).
    
    REGRAS STRICTAS:
    1. Responde APENAS com base no CONTEXTO DA ETPC fornecido abaixo.
    2. Se a resposta para a pergunta não estiver explicitamente no CONTEXTO DA ETPC, responde exatamente: 'Lamento, mas não tenho informação suficiente no site da escola para responder a essa questão.'
    3. Se a pergunta for sobre futebol, política, culinária ou qualquer assunto não relacionado com a ETPC, recusa responder e lembra que só respondes sobre a escola.
    4. Não inventes dados, não uses conhecimentos externos e não faças suposições.
    5. Sempre que responderes com sucesso, inclui o link correspondente da página da ETPC que fundamenta a resposta.
    
    CONTEXTO DA ETPC:
    " . $escolaContexto;

    // 3. PEDIDO À API DO GEMINI
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
    <title>ETPC - Escola Profissional Tecnológica do Canto</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f6f9; color: #333; line-height: 1.6; }
        
        /* Header / Navbar */
        header { background: #004085; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5rem; }
        nav a { color: white; text-decoration: none; margin-left: 15px; font-weight: 500; }

        /* Hero Section */
        .hero { background: linear-gradient(135deg, #004085, #007bff); color: white; padding: 4rem 2rem; text-align: center; }
        .hero h2 { font-size: 2.5rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto 2rem; }
        .btn-primary { background: #ffc107; color: #212529; padding: 0.8rem 1.5rem; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; text-decoration: none; }

        /* Content Sections */
        .container { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card h3 { color: #004085; margin-bottom: 0.5rem; }

        /* Chatbot Widget */
        .chat-widget-btn { position: fixed; bottom: 20px; right: 20px; background: #007bff; color: white; border: none; border-radius: 50px; padding: 15px 25px; font-size: 1rem; font-weight: bold; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.2); z-index: 1000; }
        
        .chat-window { display: none; position: fixed; bottom: 80px; right: 20px; width: 360px; height: 480px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); flex-direction: column; z-index: 1000; overflow: hidden; }
        .chat-header { background: #004085; color: white; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; }
        .chat-header h4 { margin: 0; font-size: 1rem; }
        .close-btn { background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; }
        
        .chat-body { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #f9f9f9; }
        .message { max-width: 80%; padding: 10px 14px; border-radius: 15px; font-size: 0.9rem; line-height: 1.4; word-wrap: break-word; }
        .message.bot { background: #e9ecef; color: #212529; align-self: flex-start; border-bottom-left-radius: 2px; }
        .message.user { background: #007bff; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
        .message a { color: #004085; font-weight: bold; }

        .chat-footer { padding: 10px; background: white; border-top: 1px solid #ddd; display: flex; gap: 5px; }
        .chat-footer input { flex: 1; padding: 8px 12px; border: 1px solid #ccc; border-radius: 20px; outline: none; }
        .chat-footer button { background: #007bff; color: white; border: none; border-radius: 50px; padding: 8px 15px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

    <header>
        <h1>ETPC</h1>
        <nav>
            <a href="#cursos">Cursos</a>
            <a href="#sobre">Sobre</a>
            <a href="#contactos">Contactos</a>
        </nav>
    </header>

    <section class="hero">
        <h2>Construi o teu futuro profissional na ETPC</h2>
        <p>Cursos de dupla certificação com forte ligação às empresas e elevado nível de empregabilidade.</p>
        <button class="btn-primary" onclick="toggleChat()">Falar com o Assistente Virtual</button>
    </section>

    <div class="container">
        <section id="cursos">
            <h2>Os Nossos Cursos</h2>
            <div class="cards">
                <div class="card">
                    <h3>Gestão e Programação de Sistemas Informáticos</h3>
                    <p>Aprende a desenvolver software, aplicações web e a gerir bases de dados.</p>
                </div>
                <div class="card">
                    <h3>Multimédia</h3>
                    <p>Design gráfico, edição de vídeo, fotografia e desenvolvimento web interativo.</p>
                </div>
                <div class="card">
                    <h3>Eletrónica, Automação e Comando</h3>
                    <p>Sistemas eletrónicos, robótica industrial e automação de processos.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Chatbot Floating Widget -->
    <button class="chat-widget-btn" onclick="toggleChat()">💬 Dúvidas? Pergunta aqui</button>

    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <h4>Assistente Virtual ETPC</h4>
            <button class="close-btn" onclick="toggleChat()">✕</button>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="message bot">Olá! Sou o assistente da ETPC. Em que posso ajudar relativamente à escola e aos nossos cursos?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Escreve a tua pergunta..." onkeypress="handleKeyPress(event)">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            chatWindow.style.display = (chatWindow.style.display === 'flex') ? 'none' : 'flex';
        }

        function handleKeyPress(e) {
            if (e.key === 'Enter') sendMessage();
        }

        async function sendMessage() {
            const input = document.getElementById('userInput');
            const chatBody = document.getElementById('chatBody');
            const question = input.value.trim();

            if (!question) return;

            // Mostra a pergunta do utilizador
            appendMessage(question, 'user');
            input.value = '';

            // Mensagem de carregamento
            const loadingDiv = appendMessage('A pensar...', 'bot');

            try {
                const formData = new FormData();
                formData.append('action', 'chat');
                formData.append('question', question);

                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                // Converte URLs no texto em links clicáveis
                let formattedReply = data.reply.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
                loadingDiv.innerHTML = formattedReply;

            } catch (error) {
                loadingDiv.textContent = 'Serviço temporariamente indisponível. Tenta mais tarde.';
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
