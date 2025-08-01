<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-folder');

    if ($name) {
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (user_id, name, icon) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $name, $icon]);
            header('Location: ../dashboard/');
            exit;
        } catch (PDOException $e) {
            $error = 'Erro ao criar a categoria.';
        }
    } else {
        $error = 'O nome da categoria é obrigatório.';
    }
}

// Lista de ícones pré-definidos (exemplo, pode ser expandida)
$icons = ['fa-folder', 'fa-star', 'fa-book', 'fa-code', 'fa-globe', 'fa-heart'];

$pageContent = '<div class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Criar Nova Categoria</h1>';
if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}
$pageContent .= '<form method="post" class="space-y-4">
        <div>
            <label class="block text-gray-700">Nome da Categoria</label>
            <input name="name" class="w-full border px-3 py-2 rounded-md" required>
        </div>
        <div>
            <label class="block text-gray-700">Ícone</label>
            <select name="icon" class="w-full border px-3 py-2 rounded-md">';
foreach ($icons as $icon) {
    $pageContent .= '<option value="' . $icon . '">' . ucfirst(str_replace('fa-', '', $icon)) . '</option>';
}
$pageContent .= '</select>
        </div>
        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Salvar Categoria</button>
            <a href="../dashboard/" class="px-4 py-2 rounded-md bg-gray-200 text-gray-800">Cancelar</a>
        </div>
    </form>
</div>';

echo renderHeader('Adicionar Categoria');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>