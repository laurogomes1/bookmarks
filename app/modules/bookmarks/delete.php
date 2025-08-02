<?php
// ALTERADO: Caminhos de require_once para usar a constante BASE_PATH.
require_once dirname(__DIR__, 3) . '/app/layout/common.php';
require_once dirname(__DIR__, 3) . '/app/data.php';

// MANTIDO: Sua lógica de verificação de autenticação.
checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

$bookmark_id = (int)($_GET['id'] ?? 0);
$error = '';
$bookmark = null;

// MANTIDO: Sua lógica para buscar o bookmark.
if ($bookmark_id) {
    $stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookmark_id, $user['id']]);
    $bookmark = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ALTERADO: Caminho do header('Location: ...') para a nova rota.
if (!$bookmark) {
    header('Location: /bookmarks/dashboard');
    exit;
}

// MANTIDO: Sua lógica de exclusão ao confirmar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete'])) {
        try {
            $stmt = $pdo->prepare('DELETE FROM bookmarks WHERE id = ? AND user_id = ?');
            $stmt->execute([$bookmark_id, $user['id']]);
            // ALTERADO: Caminho do header('Location: ...') para a nova rota.
            header('Location: /bookmarks/dashboard');
            exit;
        } catch (PDOException $e) {
            $error = "Erro ao excluir o bookmark.";
        }
    }
}

// MANTIDO: Toda a sua estrutura HTML para a página de confirmação.
$pageContent = '<div class="bg-white shadow rounded-lg p-8 max-w-lg mx-auto text-center mt-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Confirmar Exclusão</h1>';

if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}

$pageContent .= '<p class="text-gray-700 mb-6">
        Você tem certeza que deseja excluir o bookmark: <br>
        <strong class="text-lg d-block mt-2">' . htmlspecialchars($bookmark['title']) . '</strong>?
        <br>
        <span class="text-sm text-gray-500">Esta ação não pode ser desfeita.</span>
    </p>
    <form method="post" action="/bookmarks/bookmarks/delete?id=' . $bookmark_id . '" class="flex justify-center gap-4">
        <input type="hidden" name="confirm_delete" value="1">
        <a href="/bookmarks/dashboard" class="px-6 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</a>
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700">Sim, Excluir</button>
    </form>
</div>';

// MANTIDO: Sua renderização da página.
echo renderHeader('Excluir Bookmark');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>