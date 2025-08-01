<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../../index.php');
    exit;
}

$user = $_SESSION['user'];
$pdo = db_connect();

// Consulta todos os usuários
$stmt = $pdo->query('SELECT id, email, name, role, created_at FROM users ORDER BY id DESC');
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<div class="flex items-center justify-between mb-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900">Usuários</h1>';
$pageContent .= '<a href="add.php" class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition">+ Novo Usuário</a>';
$pageContent .= '</div>';

// Exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    if ($delete_id !== 1) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$delete_id]);
        header('Location: index.php');
        exit;
    }
}

if (count($usuarios) > 0) {
    $pageContent .= '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
    $pageContent .= '<thead><tr>';
    $pageContent .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>';
    $pageContent .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>';
    $pageContent .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>';
    $pageContent .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>';
    $pageContent .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>';
    $pageContent .= '</tr></thead><tbody>';
    foreach ($usuarios as $u) {
        $pageContent .= '<tr>';
        $pageContent .= '<td class="px-4 py-2 text-sm">' . htmlspecialchars($u['id']) . '</td>';
        $pageContent .= '<td class="px-4 py-2 text-sm">' . htmlspecialchars($u['name']) . '</td>';
        $pageContent .= '<td class="px-4 py-2 text-sm">' . htmlspecialchars($u['email']) . '</td>';
        $pageContent .= '<td class="px-4 py-2 text-sm">' . htmlspecialchars($u['role']) . '</td>';
        $pageContent .= '<td class="px-4 py-2 text-sm">' . date('d/m/Y H:i', strtotime($u['created_at'])) . '</td>';
        $pageContent .= '<td class="px-4 py-2 text-sm">'
            . '<a href="edit.php?id=' . $u['id'] . '" class="text-blue-600 hover:underline mr-3">Editar</a>';
        if ($u['id'] != 1) {
            $pageContent .= '<form method="post" action="index.php" style="display:inline" onsubmit="return confirm(\'Tem certeza que deseja excluir este usuário?\');">'
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
