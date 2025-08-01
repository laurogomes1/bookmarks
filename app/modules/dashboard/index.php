<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

// Verifica se o usuário está logado
checkAuth();

$user = $_SESSION['user'];

// Obtém a data e hora atual
$dataAtual = date('d/m/Y');
$horaAtual = date('H:i');
$diaSemana = date('l');
$diasSemana = [
    'Monday' => 'Segunda-feira',
    'Tuesday' => 'Terça-feira', 
    'Wednesday' => 'Quarta-feira',
    'Thursday' => 'Quinta-feira',
    'Friday' => 'Sexta-feira',
    'Saturday' => 'Sábado',
    'Sunday' => 'Domingo'
];
$diaSemanaPt = $diasSemana[$diaSemana];

// Conteúdo do dashboard simplificado
$dashboardContent = '<div class="bg-white shadow rounded-lg p-8 sm:p-12">
    <div class="text-center">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Bem-vindo, ' . htmlspecialchars($user['name']) . '!</h1>
            <p class="text-xl text-gray-600">Sistema de Gerenciamento</p>
        </div>
        
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-8">
            <div class="text-center">
                <div class="text-2xl font-semibold text-gray-800 mb-2">' . $diaSemanaPt . '</div>
                <div class="text-lg text-gray-600">' . $dataAtual . ' às ' . $horaAtual . '</div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
            <div class="bg-green-50 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-700 mb-2">✓</div>
                <div class="text-lg font-semibold text-green-800">Sistema Ativo</div>
                <div class="text-sm text-green-600">Tudo funcionando normalmente</div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-700 mb-2">👤</div>
                <div class="text-lg font-semibold text-blue-800">Usuário Logado</div>
                <div class="text-sm text-blue-600">' . htmlspecialchars($user['email']) . '</div>
            </div>
        </div>
        
        <div class="mt-8 text-gray-500">
            <p>Use o menu lateral para navegar pelas opções disponíveis.</p>
        </div>
    </div>
</div>';

// Renderiza a página completa
echo renderHeader('Dashboard');
echo renderPageStructure($user, $dashboardContent);
echo renderFooter();
?>
