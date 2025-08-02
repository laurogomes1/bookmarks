<?php
session_start();
require_once 'app/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $pin = $_POST['pin'] ?? '';

    if (!$email || !$password || !$pin) {
        $_SESSION['login_error'] = true;
        header('Location: index.php');
        exit;
    }

    $user = new User();
    $result = $user->authenticate($email, $password, $pin);

    if ($result['success']) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = [
            'id' => $result['user']['id'],
            'name' => $result['user']['name'],
            'email' => $result['user']['email'],
            'role' => $result['user']['role']
        ];
        
        header('Location: /bookmarks/dashboard');
        exit;
    } else {
        // Se temos credenciais válidas mas PIN inválido
        if ($result['error'] === 'invalid_pin') {
            $_SESSION['pin_error'] = true;
            $_SESSION['temp_credentials'] = [
                'email' => $email,
                'password' => $password
            ];
            header('Location: index.php');
        } else {
            $_SESSION['login_error'] = true;
            header('Location: index.php');
        }
        exit;
    }
}

$_SESSION['login_error'] = true;
header('Location: index.php');
exit;