<?php
//Config Database
define('DB_HOST', 'localhost');
define('DB_USER', 'u821507382_bookmarks');
define('DB_PASS', 'L@uro@10');
define('DB_NAME', 'u821507382_bookmarks');
define('TIMEZONE', 'America/Sao_Paulo');

// Configurar timezone
date_default_timezone_set(TIMEZONE);

// Função para conectar ao banco de dados
function db_connect() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        return $pdo;
    } catch(PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}