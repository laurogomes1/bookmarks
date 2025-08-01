<?php
// Configurações do sistema
define('DEBUG_MODE', true); // Define se os logs estão ativos
define('LOG_PATH', __DIR__ . '/logs/'); // Caminho para a pasta de logs

// Função para registrar logs
function writeLog($message, $type = 'info') {
    if (!DEBUG_MODE) {
        return false;
    }

    $date = new DateTime();
    $logFile = LOG_PATH . $date->format('Y-m-d') . '.txt';
    $timestamp = $date->format('Y-m-d H:i:s');
    
    $logMessage = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;
    
    return file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Função para converter array/objeto em string para log
function formatForLog($data) {
    if (is_array($data) || is_object($data)) {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    return $data;
}

// Função para obter a URL do logo
function get_logo_url() {
    $pdo = db_connect();
    try {
        $stmt = $pdo->prepare("SELECT config_value FROM system_config WHERE config_key = 'logo_path' LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['config_value'])) {
            return '/bookmarks/images/' . $result['config_value'];
        }
    } catch (PDOException $e) {
        // Em caso de erro, retorna o logo padrão
        return '/bookmarks/images/logo.png';
    }
    // Retorna o logo padrão se não houver configuração
    return '/bookmarks/images/logo.png';
}


// Função para definir headers de segurança
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.tailwindcss.com; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com; img-src \'self\' data: https:;');
}

// Função para verificar rate limit
function checkRateLimit($action) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateLimitFile = __DIR__ . '/logs/rate_limit_' . $action . '.txt';
    
    // Verifica se o arquivo de rate limit existe
    if (!file_exists($rateLimitFile)) {
        return true;
    }
    
    $attempts = file_get_contents($rateLimitFile);
    $attempts = json_decode($attempts, true) ?: [];
    
    // Remove tentativas antigas (mais de 15 minutos)
    $currentTime = time();
    $attempts = array_filter($attempts, function($attempt) use ($currentTime) {
        return ($currentTime - $attempt['time']) < 900; // 15 minutos
    });
    
    // Verifica se o IP atual tem muitas tentativas
    $ipAttempts = array_filter($attempts, function($attempt) use ($ip) {
        return $attempt['ip'] === $ip;
    });
    
    if (count($ipAttempts) >= 5) {
        return false; // Bloqueado
    }
    
    return true;
}

// Função para gerar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}