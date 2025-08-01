<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$bookmark_id = (int)($_GET['id'] ?? 0);
$error = '';
$bookmark = null;

// Busca o bookmark para garantir que ele pertence ao usuário logado
if ($bookmark_id) {
    $stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookmark_id, $user['id']]);
    $bookmark = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$bookmark) {
    // Se o bookmark não for encontrado ou não pertencer ao usuário, redireciona.
    header('Location: ../dashboard/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);

    if ($title && $url && $category_id) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $stmt = $pdo->prepare('UPDATE bookmarks SET title = ?, url = ?, description = ?, category_id = ? WHERE id = ? AND user_id = ?');
                $stmt->execute([$title, $url, $description, $category_id, $bookmark_id, $user['id']]);
                header('Location: ../dashboard/');
                exit;
            } catch (PDOException $e) {
                $error = 'Erro ao atualizar o bookmark.';
            }
        } else {
            $error = 'Por favor, insira uma URL válida.';
        }
    } else {
        $error = 'Título, URL e Categoria são obrigatórios.';
    }
}

// Busca categorias para o select
$stmt_categories = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name ASC");
$stmt_categories->execute([$user['id']]);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

$pageContent = '<div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Bookmark</h1>';

if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}

$pageContent .= '<form method="post" class="space-y-4">
        <div>
            <label class="block text-gray-700">Título</label>
            <input name="title" class="w-full border px-3 py-2 rounded-md" required value="' . htmlspecialchars($bookmark['title']) . '">
        </div>
        <div>
            <label class="block text-gray-700">URL</label>
            <input type="url" name="url" class="w-full border px-3 py-2 rounded-md" placeholder="https://exemplo.com" required value="' . htmlspecialchars($bookmark['url']) . '">
        </div>
        <div>
            <label class="block text-gray-700">Descrição</label>
            <textarea name="description" rows="3" class="w-full border px-3 py-2 rounded-md">' . htmlspecialchars($bookmark['description']) . '</textarea>
        </div>
        <div>
            <label class="block text-gray-700">Categoria</label>
            <select name="category_id" class="w-full border px-3 py-2 rounded-md" required>';
if (empty($categories)) {
    $pageContent .= '<option disabled>Crie uma categoria primeiro</option>';
} else {
    foreach ($categories as $category) {
        $selected = ($category['id'] == $bookmark['category_id']) ? 'selected' : '';
        $pageContent .= '<option value="' . $category['id'] . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
    }
}
$pageContent .= '</select>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Salvar Alterações</button>
            <a href="../dashboard/" class="px-4 py-2 rounded-md bg-gray-200 text-gray-800">Cancelar</a>
        </div>
    </form>
</div>';

echo renderHeader('Editar Bookmark');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>