<?php
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lógica para ATUALIZAR um favorito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $title = trim($_POST['title']);
    $url = trim($_POST['url']);
    $id = (int)$_POST['edit_id'];

    if (!empty($title) && !empty($url) && $id > 0) {
        $stmt = $pdo->prepare('UPDATE bookmarks SET title = ?, url = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$title, $url, $id, $user['id']]);
    }
    header('Location: /bookmarks/dashboard');
    exit;
}

// Busca os dados do favorito para preencher o formulário
$stmt = $pdo->prepare('SELECT * FROM bookmarks WHERE id = ? AND user_id = ?');
$stmt->execute([$edit_id, $user['id']]);
$bookmark = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bookmark) {
    // Se não encontrou o favorito ou não pertence ao usuário, volta para o dashboard
    header('Location: /bookmarks/dashboard');
    exit;
}

// Conteúdo HTML da página de edição
$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Favorito</h1>';
$pageContent .= '<form action="/bookmarks/dashboard/edit?id=' . $bookmark['id'] . '" method="post" class="space-y-6">';
$pageContent .= '<input type="hidden" name="edit_id" value="' . $bookmark['id'] . '">';
$pageContent .= '<div><label for="title" class="block text-sm font-medium text-gray-700">Título:</label><input type="text" id="title" name="title" value="' . htmlspecialchars($bookmark['title']) . '" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div><label for="url" class="block text-sm font-medium text-gray-700">URL:</label><input type="url" id="url" name="url" value="' . htmlspecialchars($bookmark['url']) . '" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></div>';
$pageContent .= '<div class="flex items-center space-x-4">';
$pageContent .= '<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 transition">Salvar Alterações</button>';
$pageContent .= '<a href="/bookmarks/dashboard" class="text-gray-600 hover:text-gray-900">Cancelar</a>';
$pageContent .= '</div>';
$pageContent .= '</form>';
$pageContent .= '</div>';

echo renderHeader('Editar Favorito');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>