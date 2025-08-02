<?php
// Caminhos de inclusão corrigidos
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

checkAuth();
$user = $_SESSION['user'];
$pdo = db_connect();

// Lógica para EXCLUIR um usuário (recebe o POST do formulário abaixo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    
    // Impede a exclusão do usuário administrador principal (ID 1)
    if ($delete_id !== 1) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$delete_id]);
        
        // Redireciona de volta para a lista de usuários com a rota correta
        header('Location: /bookmarks/usuarios');
        exit;
    }
}

// Busca todos os usuários para listar na tabela
$stmt = $pdo->query('SELECT id, email, name, role, created_at FROM users ORDER BY id DESC');
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicia a construção do conteúdo HTML
$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<div class="flex items-center justify-between mb-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900">Usuários</h1>';

// Botão "Novo Usuário" com a ROTA CORRETA
$pageContent .= '<a href="/bookmarks/usuarios/add" class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition">+ Novo Usuário</a>';
$pageContent .= '</div>';

if (count($usuarios) > 0) {
    $pageContent .= '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
    $pageContent .= '<thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th></tr></thead>';
    $pageContent .= '<tbody class="bg-white divide-y divide-gray-200">';
    foreach ($usuarios as $u) {
        $pageContent .= '<tr>';
        $pageContent .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($u['id']) . '</td>';
        $pageContent .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . htmlspecialchars($u['name']) . '</td>';
        $pageContent .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($u['email']) . '</td>';
        $pageContent .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars(ucfirst($u['role'])) . '</td>';
        $pageContent .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
        
        // Botão "Editar" com a ROTA CORRETA
        $pageContent .= '<a href="/bookmarks/usuarios/edit?id=' . $u['id'] . '" class="text-blue-600 hover:text-blue-900 mr-4">Editar</a>';
        
        if ($u['id'] != 1) {
            // Formulário de "Excluir" com a ACTION CORRETA
            $pageContent .= '<form method="post" action="/bookmarks/usuarios" class="inline" onsubmit="return confirm(\'Tem certeza que deseja excluir este usuário? Essa ação não pode ser desfeita.\');">';
            $pageContent .= '<input type="hidden" name="delete_id" value="' . $u['id'] . '">';
            $pageContent .= '<button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>';
            $pageContent .= '</form>';
        }
        
        $pageContent .= '</td></tr>';
    }
    $pageContent .= '</tbody></table></div>';
} else {
    $pageContent .= '<p class="text-gray-500">Nenhum usuário encontrado.</p>';
}
$pageContent .= '</div>';

echo renderHeader('Usuários');
echo renderPageStructure($user, $pageContent);
echo renderFooter();

?>