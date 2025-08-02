<?php
// Caminhos de inclusão corrigidos
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$edit_user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($edit_user_id === 0) {
    header('Location: /bookmarks/usuarios');
    exit;
}

// Lógica para ATUALIZAR o usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($role)) {
        if (!empty($password)) {
            // Atualiza com nova senha
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?');
            $stmt->execute([$name, $email, $hashed_password, $role, $edit_user_id]);
        } else {
            // Atualiza sem alterar a senha
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
            $stmt->execute([$name, $email, $role, $edit_user_id]);
        }
        
        // Redireciona para a lista de usuários com a ROTA CORRETA
        header('Location: /bookmarks/usuarios');
        exit;
    }
}

// Busca os dados do usuário a ser editado
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$edit_user_id]);
$edit_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$edit_user) {
    header('Location: /bookmarks/usuarios');
    exit;
}

// Conteúdo HTML do formulário de edição
$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Usuário: ' . htmlspecialchars($edit_user['name']) . '</h1>';

// Formulário com a ACTION CORRETA
$pageContent .= '<form action="/bookmarks/usuarios/edit?id=' . $edit_user_id . '" method="post" class="space-y-6">';
$pageContent .= '<div><label for="name" class="block text-sm font-medium text-gray-700">Nome:</label><input type="text" id="name" name="name" value="' . htmlspecialchars($edit_user['name']) . '" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="email" class="block text-sm font-medium text-gray-700">Email:</label><input type="email" id="email" name="email" value="' . htmlspecialchars($edit_user['email']) . '" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="password" class="block text-sm font-medium text-gray-700">Nova Senha (deixe em branco para não alterar):</label><input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="role" class="block text-sm font-medium text-gray-700">Perfil:</label><select id="role" name="role" required class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">';
$pageContent .= '<option value="user"' . ($edit_user['role'] === 'user' ? ' selected' : '') . '>Usuário</option>';
$pageContent .= '<option value="admin"' . ($edit_user['role'] === 'admin' ? ' selected' : '') . '>Administrador</option>';
$pageContent .= '</select></div>';
$pageContent .= '<div class="flex items-center space-x-4">';
$pageContent .= '<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">Atualizar Usuário</button>';

// Botão "Cancelar" com a ROTA CORRETA
$pageContent .= '<a href="/bookmarks/usuarios" class="text-gray-600 hover:text-gray-900">Cancelar</a>';
$pageContent .= '</div></form></div>';

echo renderHeader('Editar Usuário');
echo renderPageStructure($user, $pageContent);
echo renderFooter();

?>