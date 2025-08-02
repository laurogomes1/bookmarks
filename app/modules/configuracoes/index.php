<?php
// A sessão já é iniciada pelo roteador principal (index.php)

// 1. Caminhos de inclusão corrigidos para usar a constante BASE_PATH
require_once BASE_PATH . 'app/layout/common.php';
require_once BASE_PATH . 'app/data.php';

// Verifica se o usuário está autenticado
checkAuth();

$user = $_SESSION['user'];

// Verifica se o usuário tem permissão de administrador
if ($user['role'] !== 'admin') {
    // 2. Redirecionamento corrigido para a rota amigável
    header('Location: /bookmarks/dashboard');
    exit;
}

$pdo = db_connect();
$success_message = '';
$error_message = '';

// Lógica para lidar com o upload da nova logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_logo'])) {
    if ($_FILES['new_logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['new_logo']['tmp_name'];
        $file_name = 'logo.png'; // Sempre salva como logo.png para facilitar a referência
        
        // 3. Caminho de destino corrigido usando BASE_PATH
        $dest_path = BASE_PATH . 'images/' . $file_name;

        // Garante que o diretório de imagens exista
        if (!is_dir(BASE_PATH . 'images/')) {
            mkdir(BASE_PATH . 'images/', 0755, true);
        }

        // Move o arquivo enviado para o destino
        if(move_uploaded_file($file_tmp_path, $dest_path)) {
            $success_message = 'Logo atualizada com sucesso!';
        } else {
            $error_message = 'Ocorreu um erro ao salvar a nova logo.';
        }
    } else {
        $error_message = 'Erro no upload do arquivo. Por favor, tente novamente.';
    }
}

// Inicia a construção do conteúdo da página
$pageContent = '<div class="bg-white shadow rounded-lg p-4 sm:p-6">';
$pageContent .= '<h1 class="text-2xl font-semibold text-gray-900 mb-6">Configurações</h1>';

// Exibe mensagens de sucesso ou erro
if ($success_message) {
    $pageContent .= '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p>' . $success_message . '</p></div>';
}
if ($error_message) {
    $pageContent .= '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p>' . $error_message . '</p></div>';
}

$pageContent .= '<div class="space-y-6">';

// Seção para upload da logo
$pageContent .= '<div>';
$pageContent .= '<h2 class="text-lg font-medium text-gray-900">Alterar Logo</h2>';
$pageContent .= '<p class="text-sm text-gray-500 mt-1">Faça o upload de um novo arquivo de imagem para a logo do sistema. Recomendado: formato PNG com fundo transparente.</p>';

// 4. Action do formulário corrigido para a rota amigável
$pageContent .= '<form action="/bookmarks/configuracoes" method="post" enctype="multipart/form-data" class="mt-4">';
$pageContent .= '<div class="flex items-center space-x-4">';
$pageContent .= '<div>';
$pageContent .= '<label for="new_logo" class="block text-sm font-medium text-gray-700">Arquivo da Logo</label>';
$pageContent .= '<input type="file" name="new_logo" id="new_logo" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>';
$pageContent .= '</div>';
$pageContent .= '<div class="pt-5">';
$pageContent .= '<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 transition">Salvar Logo</button>';
$pageContent .= '</div>';
$pageContent .= '</div>';
$pageContent .= '</form>';
$pageContent .= '</div>';

// Seção para visualizar a logo atual
$pageContent .= '<div class="border-t border-gray-200 pt-6">';
$pageContent .= '<h2 class="text-lg font-medium text-gray-900">Logo Atual</h2>';
$logo_path = BASE_PATH . 'images/logo.png';
$logo_url = '/bookmarks/images/logo.png'; // 5. URL absoluta para a imagem

if (file_exists($logo_path)) {
    // Adiciona um timestamp para evitar cache do navegador
    $pageContent .= '<img src="' . $logo_url . '?t=' . time() . '" alt="Logo Atual" class="mt-4 h-12 w-auto bg-gray-100 p-2 rounded">';
} else {
    $pageContent .= '<p class="mt-4 text-sm text-gray-500">Nenhuma logo definida. Faça o upload de uma.</p>';
}
$pageContent .= '</div>';


$pageContent .= '</div>'; // Fim do 'space-y-6'
$pageContent .= '</div>'; // Fim do 'bg-white'

// Renderiza a página completa
echo renderHeader('Configurações');
echo renderPageStructure($user, $pageContent);
echo renderFooter();

?>