<?php
// Define o ponto de entrada e carrega os arquivos necessários
require_once dirname(__DIR__, 3) . '/app/layout/common.php';
require_once dirname(__DIR__, 3) . '/app/data.php';

// Verifica a autenticação do usuário
checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

// Processa o formulário quando ele é enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-folder'); // Ícone padrão caso nenhum seja fornecido

    if (!empty($name)) {
        // Encontra a maior ordem de exibição atual para colocar a nova categoria no final
        $stmt_order = $pdo->prepare("SELECT MAX(display_order) FROM categories WHERE user_id = ?");
        $stmt_order->execute([$user['id']]);
        $max_order = $stmt_order->fetchColumn();
        $new_order = ($max_order === null) ? 0 : $max_order + 1;

        // Insere a nova categoria no banco de dados
        $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, display_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user['id'], $name, $icon, $new_order]);

        // Redireciona de volta para o dashboard
        header('Location: /bookmarks/dashboard');
        exit;
    }
}

// Constrói o conteúdo HTML da página
$pageContent = '
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Adicionar Nova Categoria</h1>
    
    <form action="/bookmarks/categories/add" method="post">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria</label>
            <input type="text" name="name" id="name" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Ex: Ferramentas de Trabalho">
        </div>

        <div class="mb-6">
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Ícone (Font Awesome)</label>
            <input type="text" name="icon" id="icon" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Ex: fa-briefcase (opcional)">
            <p class="text-xs text-gray-500 mt-1">
                Visite o site do <a href="https://fontawesome.com/v5/search" target="_blank" class="text-blue-600 hover:underline">Font Awesome 5</a> para encontrar ícones. Use o nome completo, como "fas fa-star".
            </p>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="/bookmarks/dashboard" class="text-gray-600 hover:text-gray-800">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white font-bold px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                Salvar Categoria
            </button>
        </div>
    </form>
</div>
';

// Renderiza a página completa
echo renderHeader('Adicionar Categoria');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>