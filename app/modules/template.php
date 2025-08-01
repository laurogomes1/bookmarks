<?php
/**
 * Template para novas páginas do Sistema de Gerenciamento
 * Copie este arquivo para criar novas páginas mantendo a consistência
 */
session_start();
require_once '../../layout/common.php'; // Arquivo com funções e configurações comuns

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../../index.php');
    exit;
}

$user = $_SESSION['user'];

// Conteúdo específico da página
// Substitua este conteúdo pelo conteúdo específico da sua página
$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Título da Página</h1>
    
    <div class="bg-gray-50 rounded-md p-4 mb-6">
        <h2 class="text-lg font-medium text-gray-800 mb-3">Seção de Conteúdo</h2>
        <p class="text-gray-600">
            Substitua este texto pelo conteúdo específico da sua página.
        </p>
    </div>
</div>';

// Renderiza a página completa
echo renderHeader('Título da Página'); // Substitua pelo título da sua página
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>
