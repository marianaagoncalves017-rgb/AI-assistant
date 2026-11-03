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

    // BASE DE DADOS DE CONHECIMENTO DA ETPC (Extraída do site oficial)
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

    // SYSTEM PROMPT (Regras rigorosas para a IA)
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

    // PEDIDO À API DO GEMINI
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
    <link rel="stylesheet" href="style.css">
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
        <h2>Constrói o teu futuro profissional na ETPC</h2>
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

    <!-- Botão Flutuante e Janela do Chatbot -->
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

    <script src="script.js"></script>
</body>
</html>
