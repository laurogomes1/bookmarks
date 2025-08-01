<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

// Verifica se o usuário está logado
checkAuth();

$user = $_SESSION['user'];
$pdo = db_connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, email, name, role FROM users WHERE id = ?');
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$usuario) {
    header('Location: index.php');
    exit;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pin = $_POST['pin'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    if ($name && $email && $pin && in_array($role, ['admin','user'])) {
        $pinHash = password_hash($pin, PASSWORD_DEFAULT);
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET email=?, name=?, pin=?, role=?, password=? WHERE id=?');
            $stmt->execute([$email, $name, $pinHash, $role, $hash, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET email=?, name=?, pin=?, role=? WHERE id=?');
            $stmt->execute([$email, $name, $pinHash, $role, $id]);
        }
        $success = true;
        // Atualiza dados do usuário editado
        $stmt = $pdo->prepare('SELECT id, email, name, role FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = 'Preencha todos os campos obrigatórios.';
    }
}

$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6 max-w-lg mx-auto">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Usuário</h1>';
if ($success) {
    $pageContent .= '<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">Usuário atualizado com sucesso! <a href="index.php" class="underline text-green-700">Voltar à lista</a></div>';
}
if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}
$pageContent .= '<form method="post" class="space-y-4">';
$pageContent .= '<div><label class="block text-gray-700">Nome</label><input name="name" value="' . htmlspecialchars($usuario['name']) . '" class="w-full border px-3 py-2 rounded" required></div>';
$pageContent .= '<div><label class="block text-gray-700">Email</label><input type="email" name="email" value="' . htmlspecialchars($usuario['email']) . '" class="w-full border px-3 py-2 rounded" required></div>';
$pageContent .= '<div><label class="block text-gray-700">PIN</label><input name="pin" maxlength="6" placeholder="Digite o novo PIN" class="w-full border px-3 py-2 rounded" required></div>';
$pageContent .= '<div><label class="block text-gray-700">Perfil</label><select name="role" class="w-full border px-3 py-2 rounded"><option value="user"' . ($usuario['role']=='user'?' selected':'') . '>Usuário</option><option value="admin"' . ($usuario['role']=='admin'?' selected':'') . '>Administrador</option></select></div>';
$pageContent .= '<div><label class="block text-gray-700">Nova senha <span class="text-xs text-gray-400">(deixe em branco para não alterar)</span></label><input type="password" name="password" class="w-full border px-3 py-2 rounded"></div>';
$pageContent .= '<div class="flex gap-2"><button type="submit" class="bg-green-700 text-white px-4 py-2 rounded">Salvar</button><a href="index.php" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Cancelar</a></div>';
$pageContent .= '</form>';
$pageContent .= '</div>';

echo renderHeader('Editar Usuário');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
