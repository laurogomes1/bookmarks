<?php
// Caminhos de inclusão corrigidos
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

checkAuth();
$user = $_SESSION['user'];

// Lógica para ADICIONAR um novo usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!empty($name) && !empty($email) && !empty($password) && !empty($role)) {
        $pdo = db_connect();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hashed_password, $role]);
        
        // Redireciona para a lista de usuários com a ROTA CORRETA
        header('Location: /bookmarks/usuarios');
        exit;
    }
}

// Conteúdo HTML do formulário
$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900 mb-6">Adicionar Novo Usuário</h1>';

// Formulário com a ACTION CORRETA
$pageContent .= '<form action="/bookmarks/usuarios/add" method="post" class="space-y-6">';
$pageContent .= '<div><label for="name" class="block text-sm font-medium text-gray-700">Nome:</label><input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="email" class="block text-sm font-medium text-gray-700">Email:</label><input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="password" class="block text-sm font-medium text-gray-700">Senha:</label><input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="role" class="block text-sm font-medium text-gray-700">Perfil:</label><select id="role" name="role" required class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><option value="user">Usuário</option><option value="admin">Administrador</option></select></div>';
$pageContent .= '<div class="flex items-center space-x-4">';
$pageContent .= '<button type="submit" class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition">Salvar Usuário</button>';

// Botão "Cancelar" com a ROTA CORRETA
$pageContent .= '<a href="/bookmarks/usuarios" class="text-gray-600 hover:text-gray-900">Cancelar</a>';
$pageContent .= '</div></form></div>';

echo renderHeader('Adicionar Usuário');
echo renderPageStructure($user, $pageContent);
echo renderFooter();

?>