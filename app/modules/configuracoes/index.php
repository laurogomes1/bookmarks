<?php
session_start();
require_once '../../layout/common.php';
require_once '../../data.php';

// Verifica se o usuário está logado
checkAuth();

$user = $_SESSION['user'];

// Verifica se é administrador
if ($user['role'] !== 'admin') {
    header('Location: ../dashboard/');
    exit;
}

$pdo = db_connect();
$success = false;
$error = '';

// Lógica de upload de logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_logo'])) {
    $target_dir = "../../../images/";
    // Garante que o diretório de imagens exista
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES["new_logo"]["name"], PATHINFO_EXTENSION));
    $new_logo_name = 'logo_' . time() . '.' . $imageFileType;
    $target_file = $target_dir . $new_logo_name;
    $uploadOk = 1;

    // Verifica se o arquivo é uma imagem
    $check = getimagesize($_FILES["new_logo"]["tmp_name"]);
    if($check === false) {
        $error = "O arquivo não é uma imagem válida.";
        $uploadOk = 0;
    }

    // Verifica o tamanho do arquivo (limite de 2MB)
    if ($_FILES["new_logo"]["size"] > 2000000) {
        $error = "Desculpe, seu arquivo é muito grande.";
        $uploadOk = 0;
    }

    // Permite apenas formatos de imagem específicos
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $error = "Desculpe, apenas arquivos JPG, JPEG, PNG & GIF são permitidos.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        // o erro já foi definido
    } else {
        if (move_uploaded_file($_FILES["new_logo"]["tmp_name"], $target_file)) {
            // Salva o novo caminho do logo no banco de dados
            $stmt = $pdo->prepare("INSERT INTO system_config (config_key, config_value) VALUES ('logo_path', ?) ON DUPLICATE KEY UPDATE config_value = ?");
            if ($stmt->execute([$new_logo_name, $new_logo_name])) {
                $success = true;
            } else {
                $error = "Houve um erro ao salvar a configuração no banco de dados.";
            }
        } else {
            $error = "Desculpe, houve um erro ao enviar seu arquivo.";
        }
    }
}


$pageContent = '<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Configurações do Sistema</h1>';
    
if ($success) {
    $pageContent .= '<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">Logo atualizado com sucesso!</div>';
}
if ($error) {
    $pageContent .= '<div class="mb-4 p-3 bg-red-100 text-red-800 rounded">' . htmlspecialchars($error) . '</div>';
}

$pageContent .= '
    <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Atualizar Logo do Sistema</h2>
        <form action="" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="new_logo" class="block text-sm font-medium text-gray-700">Selecione a nova imagem do logo:</label>
                <input type="file" name="new_logo" id="new_logo" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-green-50 file:text-green-700
                    hover:file:bg-green-100
                "/>
                <p class="mt-2 text-xs text-gray-500">PNG, JPG, GIF até 2MB.</p>
            </div>
            <div>
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition">Enviar Novo Logo</button>
            </div>
        </form>
    </div>
';


$pageContent .= '<div class="mt-6 space-y-6">
    <div class="bg-blue-50 rounded-lg p-4">
        <h2 class="text-lg font-semibold text-blue-900 mb-2">Informações do Sistema</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-blue-800">Versão do PHP:</span>
                <span class="text-blue-600">' . phpversion() . '</span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Servidor:</span>
                <span class="text-blue-600">' . $_SERVER['SERVER_SOFTWARE'] . '</span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Data/Hora:</span>
                <span class="text-blue-600">' . date('d/m/Y H:i:s') . '</span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Usuário Logado:</span>
                <span class="text-blue-600">' . htmlspecialchars($user['name']) . '</span>
            </div>
        </div>
    </div>
    
    <div class="bg-yellow-50 rounded-lg p-4">
        <h2 class="text-lg font-semibold text-yellow-900 mb-2">Módulos Disponíveis</h2>
        <ul class="space-y-2 text-sm">
            <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-yellow-800">Dashboard</span>
            </li>
            <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-yellow-800">Usuários</span>
            </li>
            <li class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-yellow-800">Configurações</span>
            </li>
        </ul>
    </div>
    
    <div class="bg-green-50 rounded-lg p-4">
        <h2 class="text-lg font-semibold text-green-900 mb-2">Status do Sistema</h2>
        <div class="space-y-2 text-sm">
            <div class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-green-800">Sistema operacional</span>
            </div>
            <div class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-green-800">Autenticação funcionando</span>
            </div>
            <div class="flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                <span class="text-green-800">Banco de dados conectado</span>
            </div>
        </div>
    </div>
</div>';

$pageContent .= '</div>';

echo renderHeader('Configurações');
echo renderPageStructure($user, $pageContent);
echo renderFooter();
?>