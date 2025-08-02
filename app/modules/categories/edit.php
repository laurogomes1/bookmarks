<?php
// Define o ponto de entrada e carrega os arquivos necessários
require_once dirname(__DIR__, 3) . '/app/layout/common.php';
require_once dirname(__DIR__, 3) . '/app/data.php';

// Verifica a autenticação e obtém os dados do usuário
checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

// Pega o ID da categoria da URL. Se não houver, redireciona para o dashboard.
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($category_id === 0) {
    header('Location: /bookmarks/dashboard');
    exit;
}

// Processa o formulário quando ele é enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-folder');

    if (!empty($name)) {
        // Atualiza os dados da categoria no banco de dados
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$name, $icon, $category_id, $user['id']]);

        // Redireciona de volta para o dashboard
        header('Location: /bookmarks/dashboard');
        exit;
    }
}

// Busca os dados atuais da categoria para preencher o formulário
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND user_id = ?");
$stmt->execute([$category_id, $user['id']]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// Se a categoria não for encontrada ou não pertencer ao usuário, redireciona para o dashboard
if (!$category) {
    header('Location: /bookmarks/dashboard');
    exit;
}

// Constrói o conteúdo HTML da página
$pageContent = '
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Editar Categoria</h1>
    
    <form action="/bookmarks/categories/edit?id=' . $category_id . '" method="post">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria</label>
            <input type="text" name="name" id="name" required 
                   value="' . htmlspecialchars($category['name']) . '"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-6">
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Ícone (Font Awesome)</label>
            <input type="text" name="icon" id="icon" 
                   value="' . htmlspecialchars($category['icon']) . '"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">
                Visite o site do <a href="https://fontawesome.com/v5/search" target="_blank" class="text-blue-600 hover:underline">Font Awesome 5</a> para encontrar ícones. Use o nome completo, como "fas fa-star".
            </p>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="/bookmarks/dashboard" class="text-gray-600 hover:text-gray-800">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
';

// Renderiza a página completa
echo renderHeader('Editar Categoria');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>