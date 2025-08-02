<?php
// Define o ponto de entrada e carrega os arquivos necessários
require_once dirname(__DIR__, 3) . '/app/layout/common.php';
require_once dirname(__DIR__, 3) . '/app/data.php';

// Verifica a autenticação do usuário
checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

// Pega o ID do bookmark da URL. Se não houver, redireciona.
$bookmark_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($bookmark_id === 0) {
    header('Location: /bookmarks/dashboard');
    exit;
}

// Processa o formulário quando ele é enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

    // Validação simples
    if (!empty($title) && !empty($url) && $category_id > 0) {
        // Atualiza o bookmark no banco de dados
        $stmt = $pdo->prepare("UPDATE bookmarks SET title = ?, url = ?, description = ?, category_id = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $url, $description, $category_id, $bookmark_id, $user['id']]);

        // Redireciona de volta para o dashboard
        header('Location: /bookmarks/dashboard');
        exit;
    }
}

// Busca os dados do bookmark a ser editado
$stmt_bookmark = $pdo->prepare("SELECT * FROM bookmarks WHERE id = ? AND user_id = ?");
$stmt_bookmark->execute([$bookmark_id, $user['id']]);
$bookmark = $stmt_bookmark->fetch(PDO::FETCH_ASSOC);

// Se o bookmark não for encontrado ou não pertencer ao usuário, redireciona
if (!$bookmark) {
    header('Location: /bookmarks/dashboard');
    exit;
}

// Busca todas as categorias do usuário para o menu <select>
$stmt_categories = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name ASC");
$stmt_categories->execute([$user['id']]);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Constrói o conteúdo HTML da página
$pageContent = '
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Editar Bookmark</h1>
    
    <form action="/bookmarks/bookmarks/edit?id=' . $bookmark_id . '" method="post">
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="category_id" id="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white">';
    
    foreach ($categories as $category) {
        // Marca a categoria atual do bookmark como selecionada
        $selected = ($category['id'] == $bookmark['category_id']) ? ' selected' : '';
        $pageContent .= '<option value="' . $category['id'] . '"' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
    }

    $pageContent .= '
            </select>
        </div>

        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Bookmark</label>
            <input type="text" name="title" id="title" required 
                   value="' . htmlspecialchars($bookmark['title']) . '"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="url" name="url" id="url" required 
                   value="' . htmlspecialchars($bookmark['url']) . '"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição (Opcional)</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">'
                      . htmlspecialchars($bookmark['description']) . '</textarea>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="/bookmarks/dashboard" class="text-gray-600 hover:text-gray-800">Cancelar</a>
            <button type="submit" class="bg-green-600 text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-green-700 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
';

// Renderiza a página completa
echo renderHeader('Editar Bookmark');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>