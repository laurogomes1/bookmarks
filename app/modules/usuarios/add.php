<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

// Verifica se o usuário está logado
checkAuth();

$user = $_SESSION['user'];
$pdo = db_connect();

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $pin = $_POST['pin'] ?? '';
    $role = $_POST['role'] ?? 'user';
    if ($name && $email && $password && $pin && in_array($role, ['admin','user'])) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pinHash = password_hash($pin, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password, pin, name, role) VALUES (?, ?, ?, ?, ?)');
        try {
            $stmt->execute([$email, $hash, $pinHash, $name, $role]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'Erro ao cadastrar usuário: ' . $e->getMessage();
        }
    } else {
        $error = 'Preencha todos os campos corretamente.';
    }
}

$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6 max-w-lg mx-auto">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900 mb-6">Adicionar Usuário</h1>';
if ($success) {
    $pageContent .= '<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">Usuário cadastrado com sucesso! <a href="index.php" class="underline text-green-700">Voltar à lista</a></div>';
} else {
    if ($error) {
        $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
    }
    $pageContent .= '<form method="post" class="space-y-4">';
    $pageContent .= '<div><label class="block text-gray-700">Nome</label><input name="name" class="w-full border px-3 py-2 rounded" required></div>';
    $pageContent .= '<div><label class="block text-gray-700">Email</label><input type="email" name="email" class="w-full border px-3 py-2 rounded" required></div>';
    $pageContent .= '<div><label class="block text-gray-700">Senha</label><input type="password" name="password" class="w-full border px-3 py-2 rounded" required></div>';
    $pageContent .= '<div><label class="block text-gray-700">PIN</label><input name="pin" maxlength="6" class="w-full border px-3 py-2 rounded" required></div>';
    $pageContent .= '<div><label class="block text-gray-700">Perfil</label><select name="role" class="w-full border px-3 py-2 rounded"><option value="user">Usuário</option><option value="admin">Administrador</option></select></div>';
    $pageContent .= '<div class="flex gap-2"><button type="submit" class="bg-green-700 text-white px-4 py-2 rounded">Salvar</button><a href="index.php" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Cancelar</a></div>';
    $pageContent .= '</form>';
}
$pageContent .= '</div>';

echo renderHeader('Adicionar Usuário');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
