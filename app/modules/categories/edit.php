<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$category_id = (int)($_GET['id'] ?? 0);
$error = '';
$category = null;

if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $user['id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$category) {
    header('Location: ../dashboard/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-folder');

    if ($name) {
        try {
            $stmt = $pdo->prepare('UPDATE categories SET name = ?, icon = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$name, $icon, $category_id, $user['id']]);
            header('Location: ../dashboard/');
            exit;
        } catch (PDOException $e) {
            $error = 'Erro ao atualizar a categoria.';
        }
    } else {
        $error = 'O nome da categoria é obrigatório.';
    }
}

$icons = ['fa-folder', 'fa-star', 'fa-book', 'fa-code', 'fa-globe', 'fa-heart', 'fa-briefcase', 'fa-film', 'fa-music', 'fa-gamepad'];

$pageContent = '<div class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Editar Categoria</h1>';
if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}
$pageContent .= '<form method="post" class="space-y-4">
        <div>
            <label class="block text-gray-700">Nome da Categoria</label>
            <input name="name" class="w-full border px-3 py-2 rounded-md" required value="' . htmlspecialchars($category['name']) . '">
        </div>
        <div>
            <label class="block text-gray-700">Ícone</label>
            <select name="icon" class="w-full border px-3 py-2 rounded-md">';
foreach ($icons as $icon) {
    $selected = ($icon == $category['icon']) ? 'selected' : '';
    $pageContent .= '<option value="' . $icon . '" ' . $selected . '>' . ucfirst(str_replace('fa-', '', $icon)) . '</option>';
}
$pageContent .= '</select>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Salvar Alterações</button>
            <a href="../dashboard/" class="px-4 py-2 rounded-md bg-gray-200 text-gray-800">Cancelar</a>
        </div>
    </form>
</div>';

echo renderHeader('Editar Categoria');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>