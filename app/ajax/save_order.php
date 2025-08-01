<?php
session_start();
require_once '../data.php';

// Apenas usuários logados podem executar esta ação
if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

// Apenas requisições POST são permitidas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$pdo = db_connect();

try {
    $pdo->beginTransaction();

    // Atualiza a ordem das categorias
    // Esta parte agora funciona porque o JavaScript enviará 'categories[]' corretamente.
    if (isset($_POST['categories']) && is_array($_POST['categories'])) {
        $categories_order = $_POST['categories'];
        $stmt_cat = $pdo->prepare("UPDATE categories SET display_order = ? WHERE id = ? AND user_id = ?");
        foreach ($categories_order as $order => $id) {
            $stmt_cat->execute([$order, (int)$id, $user_id]);
        }
    }

    // Atualiza a ordem dos bookmarks
    // CORREÇÃO: Decodifica a string JSON enviada pelo JavaScript
    if (isset($_POST['bookmarks'])) {
        $bookmarks_order = json_decode($_POST['bookmarks'], true); // O 'true' transforma em array associativo

        if (is_array($bookmarks_order)) {
            $stmt_book_update_category = $pdo->prepare("UPDATE bookmarks SET display_order = ?, category_id = ? WHERE id = ? AND user_id = ?");
            
            foreach ($bookmarks_order as $category_id => $bookmarks) {
                if (is_array($bookmarks)) {
                    foreach ($bookmarks as $order => $id) {
                        $stmt_book_update_category->execute([$order, (int)$category_id, (int)$id, $user_id]);
                    }
                }
            }
        }
    }

    $pdo->commit();

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Ordem salva com sucesso.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    header('Content-Type: application/json', true, 500);
    // Para depuração, você pode usar: 'Erro no banco de dados: ' . $e->getMessage()
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao salvar a ordem no banco de dados.']);
}