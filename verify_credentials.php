<?php
session_start();
require_once 'app/config.php';
require_once 'app/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    writeLog("Iniciando verificação de credenciais", 'info');
    writeLog("Dados recebidos - Email: {$email}", 'debug');

    if (!$email || !$password) {
        writeLog("Dados incompletos no formulário", 'warning');
        echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios']);
        exit;
    }

    $user = new User();
    $result = $user->verifyCredentials($email, $password);
    
    writeLog("Resultado da verificação: " . formatForLog($result), 'debug');
    
    echo json_encode($result);
    exit;
}

writeLog("Tentativa de acesso com método inválido: " . $_SERVER['REQUEST_METHOD'], 'warning');
echo json_encode(['success' => false, 'message' => 'Método não permitido']);
exit;