<?php
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

checkAuth();
$user = $_SESSION['user'];

// Lógica para ADICIONAR um favorito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $url = trim($_POST['url']);

    if (!empty($title) && !empty($url)) {
        $pdo = db_connect();
        $stmt = $pdo->prepare('INSERT INTO bookmarks (user_id, title, url) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $title, $url]);
    }
    // Redireciona de volta para o dashboard após adicionar
    header('Location: /bookmarks/dashboard');
    exit;
}

// Se alguém tentar acessar a URL /dashboard/add diretamente, apenas redireciona.
header('Location: /bookmarks/dashboard');
exit;
?>