<?php
// ALTERADO: Caminhos de require_once para usar a constante BASE_PATH.
require_once dirname(__DIR__, 3) . '/app/layout/common.php';
require_once dirname(__DIR__, 3) . '/app/data.php';

// MANTIDO: Sua lógica de verificação de autenticação.
checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$category_id = (int)($_GET['id'] ?? 0);
$error = '';
$category = null;

// MANTIDO: Sua lógica para buscar a categoria.
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$category_id, $user['id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
}

// MANTIDO: Sua lógica de redirecionamento se a categoria não existir.
// ALTERADO: Caminho do header('Location: ...') para a nova rota.
if (!$category) {
    header('Location: /bookmarks/dashboard');
    exit;
}

// MANTIDO: Sua lógica de exclusão ao confirmar.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        // Exclui primeiro os bookmarks da categoria
        $stmt_bookmarks = $pdo->prepare('DELETE FROM bookmarks WHERE category_id = ? AND user_id = ?');
        $stmt_bookmarks->execute([$category_id, $user['id']]);
        
        // Depois exclui a categoria
        $stmt_category = $pdo->prepare('DELETE FROM categories WHERE id = ? AND user_id = ?');
        $stmt_category->execute([$category_id, $user['id']]);

        // ALTERADO: Caminho do header('Location: ...') para a nova rota.
        header('Location: /bookmarks/dashboard');
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao excluir a categoria.";
    }
}

// MANTIDO: Toda a sua estrutura HTML para a página de confirmação.
$pageContent = '<div class="bg-white shadow rounded-lg p-8 max-w-lg mx-auto text-center mt-8">
    <h1 class="text-2xl font-bold text-red-700 mb-4">Confirmar Exclusão de Categoria</h1>';

if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}

$pageContent .= '<p class="text-gray-700 mb-3">
        Você tem certeza que deseja excluir permanentemente a categoria: <br>
        <strong class="text-lg d-block mt-2">' . htmlspecialchars($category['name']) . '</strong>?
    </p>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Atenção!</p>
        <p>Todos os bookmarks dentro desta categoria também serão excluídos. Esta ação não pode ser desfeita.</p>
    </div>
    <form method="post" action="/bookmarks/categories/delete?id=' . $category_id . '" class="flex justify-center gap-4">
        <input type="hidden" name="confirm_delete" value="1">
        <a href="/bookmarks/dashboard" class="px-6 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</a>
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700">Sim, Excluir Tudo</button>
    </form>
</div>';

// MANTIDO: Sua renderização da página.
echo renderHeader('Excluir Categoria');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>