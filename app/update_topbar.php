<?php
session_start();
require_once 'topbar.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    exit('Não autorizado');
}

$isCollapsed = isset($_GET['collapsed']) && $_GET['collapsed'] === 'true';
echo renderTopBar($_SESSION['user'], $isCollapsed);
?>