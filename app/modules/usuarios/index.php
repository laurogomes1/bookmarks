<?php
// A sessão já é iniciada pelo roteador (index.php)
// DE: require_once '../../layout/common.php';
// DE: require_once '../../data.php';
// PARA:
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

checkAuth(); // A função checkAuth() já verifica a sessão

$user = $_SESSION['user'];
$pdo = db_connect();

$stmt = $pdo->query('SELECT id, email, name, role, created_at FROM users ORDER BY id DESC');
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<div class="flex items-center justify-between mb-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900">Usuários</h1>';
// Link corrigido para a nova rota
$pageContent .= '<a href="/bookmarks/usuarios/add" class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition">+ Novo Usuário</a>';
$pageContent .= '</div>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    if ($delete_id !== 1) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$delete_id]);
        header('Location: /bookmarks/usuarios'); // Redirecionamento corrigido
        exit;
    }
}

if (count($usuarios) > 0) {
    $pageContent .= '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
    $pageContent .= '<thead><tr>...</tr></thead><tbody>'; // Conteúdo da tabela mantido
    foreach ($usuarios as $u) {
        $pageContent .= '<tr>';
        // ... (células da tabela mantidas)
        $pageContent .= '<td class="px-4 py-2 text-sm">'
            // Link de edição corrigido
            . '<a href="/bookmarks/usuarios/edit?id=' . $u['id'] . '" class="text-blue-600 hover:underline mr-3">Editar</a>';
        if ($u['id'] != 1) {
            // Action do formulário corrigido
            $pageContent .= '<form method="post" action="/bookmarks/usuarios" style="display:inline" onsubmit="return confirm(\'Tem certeza que deseja excluir este usuário?\');">'
                . '<input type="hidden" name="delete_id" value="' . $u['id'] . '">' 
                . '<button type="submit" class="text-red-600 hover:underline">Excluir</button>'
                . '</form>';
        } else {
            $pageContent .= '<span class="text-gray-400 ml-2" title="Usuário protegido">Não pode excluir</span>';
        }
        $pageContent .= '</td>';
        $pageContent .= '</tr>';
    }
    $pageContent .= '</tbody></table></div>';
} else {
    $pageContent .= '<p class="text-gray-600">Nenhum usuário encontrado.</p>';
}
$pageContent .= '</div>';

echo renderHeader('Usuários');
echo renderPageStructure($user, $pageContent);
echo renderFooter();