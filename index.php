<?php
// Inicia a sessão para podermos verificar se o usuário está logado.
session_start();

// Define o caminho base da aplicação. Como este arquivo está na raiz de /bookmarks/,
// __DIR__ é o caminho absoluto correto no servidor.
define('BASE_PATH', __DIR__ . '/');

// Obtém a rota da URL.
$route = $_GET['route'] ?? '';

// Remove a barra final da rota, se houver.
$route = rtrim($route, '/');

// Se a rota estiver vazia, significa que o usuário acessou a raiz "/bookmarks/".
// Vamos decidir para onde enviá-lo.
if ($route === '') {
    // Se o usuário já estiver logado, redireciona para o dashboard.
    if (isset($_SESSION['user_id'])) {
        // A rota do dashboard é 'dashboard'.
        $route = 'dashboard';
    } else {
        // Se não estiver logado, redireciona para a página de login.
        $route = 'login';
    }
}

// Define as rotas e os arquivos correspondentes (sem o caminho gigante).
$routes = [
    // Rotas da Aplicação Principal
    'dashboard'       => 'app/modules/dashboard/index.php',
    'configuracoes'   => 'app/modules/configuracoes/index.php',
    
    // Rotas de Usuários
    'usuarios'        => 'app/modules/usuarios/index.php',
    'usuarios/add'    => 'app/modules/usuarios/add.php',
    'usuarios/edit'   => 'app/modules/usuarios/edit.php', // Adicionei para o futuro

    // Rotas de Autenticação
    'login'                   => 'login.php', 
    'logout'                  => 'logout.php',
    'auth'                    => 'auth.php',
    'verify-credentials'      => 'verify_credentials.php',

    // Rotas para assets
    'assets/css/styles.css'   => 'assets/css/styles.css',
    'assets/js/main.js'       => 'assets/js/main.js',
];

// Lógica do Roteador
if (array_key_exists($route, $routes)) {
    // Se a rota existir, inclui o arquivo correspondente.
    require_once BASE_PATH . $routes[$route];
} else {
    // Tratamento para rotas de imagem
    if (preg_match('/^app\/uploads\/(.*)$/', $route, $matches)) {
        $imagePath = BASE_PATH . 'app/uploads/' . $matches[1];
        if (file_exists($imagePath)) {
            $mimeType = mime_content_type($imagePath);
            header("Content-Type: $mimeType");
            readfile($imagePath);
            exit;
        }
    }

    // Se a rota não for encontrada, exibe um erro 404 claro.
    http_response_code(404);
    echo "<h1>Erro 404 - Página Não Encontrada</h1>";
    echo "<p>A rota solicitada '<strong>" . htmlspecialchars($route) . "</strong>' não foi encontrada no sistema.</p>";
}