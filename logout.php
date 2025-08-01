<?php
session_start();
require_once 'app/config.php';

// Registra o logout nos logs
if (isset($_SESSION['user'])) {
    writeLog("Usuário {$_SESSION['user']['name']} (ID: {$_SESSION['user']['id']}) fez logout", 'info');
}

// Destroi todas as variáveis de sessão
$_SESSION = array();

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header('Location: index.php');
exit;