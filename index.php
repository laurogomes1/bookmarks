<?php
session_start();
define('BASE_PATH', __DIR__ . '/');

$route = $_GET['route'] ?? '';
$route = rtrim($route, '/');

if ($route === '') {
    if (isset($_SESSION['user_id'])) {
        $route = 'dashboard';
    } else {
        $route = 'login';
    }
}

$routes = [
    // Dashboard
    'dashboard'               => 'app/modules/dashboard/index.php',

    // Bookmarks
    'bookmarks/add'           => 'app/modules/bookmarks/add.php',
    'bookmarks/edit'          => 'app/modules/bookmarks/edit.php',
    'bookmarks/delete'        => 'app/modules/bookmarks/delete.php',

    // Categories
    'categories/add'          => 'app/modules/categories/add.php',
    'categories/edit'         => 'app/modules/categories/edit.php',
    'categories/delete'       => 'app/modules/categories/delete.php',

    // AJAX - ROTA CORRIGIDA PARA O CAMINHO EXISTENTE
    'ajax/save-order'         => 'app/ajax/save_order.php',

    // Usuários
    'usuarios'                => 'app/modules/usuarios/index.php',
    'usuarios/add'            => 'app/modules/usuarios/add.php',
    'usuarios/edit'           => 'app/modules/usuarios/edit.php',

    // Configurações
    'configuracoes'           => 'app/modules/configuracoes/index.php',

    // Autenticação
    'login'                   => 'login.php', 
    'logout'                  => 'logout.php',
    'auth'                    => 'auth.php',
    'verify-credentials'      => 'verify_credentials.php',
];

if (array_key_exists($route, $routes)) {
    require_once BASE_PATH . $routes[$route];
} else {
    // Tratamento para imagens
    if (preg_match('/^images\/(.*)$/', $route, $matches)) {
        $imagePath = BASE_PATH . 'images/' . $matches[1];
        if (file_exists($imagePath)) {
            $mimeType = mime_content_type($imagePath);
            header("Content-Type: $mimeType");
            readfile($imagePath);
            exit;
        }
    }
    http_response_code(404);
    echo "<h1>Erro 404 - Página Não Encontrada</h1>";
    echo "<p>A rota solicitada '<strong>" . htmlspecialchars($route) . "</strong>' não foi encontrada no sistema.</p>";
}