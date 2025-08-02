<?php
// Define o ponto de entrada e carrega os arquivos necessários
require_once dirname(__DIR__, 3) . '/app/layout/common.php';
require_once dirname(__DIR__, 3) . '/app/data.php';

// Verifica a autenticação do usuário
checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

// Processa o formulário quando ele é enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

    // Validação simples
    if (!empty($title) && !empty($url) && $category_id > 0) {
        // Encontra a maior ordem de exibição para a categoria selecionada
        $stmt_order = $pdo->prepare("SELECT MAX(display_order) FROM bookmarks WHERE user_id = ? AND category_id = ?");
        $stmt_order->execute([$user['id'], $category_id]);
        $max_order = $stmt_order->fetchColumn();
        $new_order = ($max_order === null) ? 0 : $max_order + 1;
        
        // Insere o novo bookmark no banco de dados
        $stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, category_id, title, url, description, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $category_id, $title, $url, $description, $new_order]);

        // Redireciona de volta para o dashboard
        header('Location: /bookmarks/dashboard');
        exit;
    }
}

// Busca as categorias do usuário para preencher o menu <select>
$stmt_categories = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name ASC");
$stmt_categories->execute([$user['id']]);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Constrói o conteúdo HTML da página
$pageContent = '
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Adicionar Novo Bookmark</h1>';

// Se não houver categorias, exibe um aviso em vez do formulário
if (empty($categories)) {
    $pageContent .= '
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
        <p class="font-bold">Atenção</p>
        <p>Você precisa ter pelo menos uma categoria para adicionar um bookmark.</p>
        <a href="/bookmarks/categories/add" class="mt-2 inline-block text-yellow-800 hover:underline font-semibold">Clique aqui para criar uma.</a>
    </div>';
} else {
    // Exibe o formulário de adição
    $pageContent .= '
    <form action="/bookmarks/bookmarks/add" method="post">
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="category_id" id="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="">Selecione uma categoria</option>';
    
    foreach ($categories as $category) {
        $pageContent .= '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
    }

    $pageContent .= '
            </select>
        </div>

        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do Bookmark</label>
            <input type="text" name="title" id="title" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Ex: Google Docs">
        </div>

        <div class="mb-4">
            <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="url" name="url" id="url" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   placeholder="https://docs.google.com">
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição (Opcional)</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Pequeno resumo sobre o link."></textarea>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="/bookmarks/dashboard" class="text-gray-600 hover:text-gray-800">Cancelar</a>
            <button type="submit" class="bg-green-600 text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-green-700 transition">
                Salvar Bookmark
            </button>
        </div>
    </form>';
}

$pageContent .= '</div>';

// Renderiza a página completa
echo renderHeader('Adicionar Bookmark');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>