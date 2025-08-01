<?php
require_once 'data.php';
require_once 'config.php';

class User {
    private $db;

    public function __construct() {
        $this->db = db_connect();
        writeLog('Nova instância da classe User criada', 'debug');
    }

    public function verifyCredentials($email, $password) {
        try {
            writeLog("Tentativa de verificação de credenciais para email: {$email}", 'info');
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug detalhado
            $debug = [
                'email_found' => !empty($user),
                'password_received' => $password,
                'stored_hash' => $user['password'] ?? 'não encontrado'
            ];

            writeLog("Resultado da busca do usuário: " . formatForLog($debug), 'debug');

            if ($user) {
                // Verifica a senha usando password_verify
                $passwordValid = password_verify($password, $user['password']);
                $debug['password_valid'] = $passwordValid;
                
                writeLog("Verificação de senha: " . ($passwordValid ? 'válida' : 'inválida'), 'info');

                if ($passwordValid) {
                    writeLog("Login bem-sucedido para o email: {$email}", 'success');
                    return ['success' => true, 'user' => $user];
                }
            }

            writeLog("Falha no login para o email: {$email}", 'warning');
            return [
                'success' => false, 
                'message' => 'Credenciais inválidas',
                'debug' => $debug
            ];
        } catch (PDOException $e) {
            $errorMessage = "Erro ao verificar credenciais: " . $e->getMessage();
            writeLog($errorMessage, 'error');
            return [
                'success' => false, 
                'message' => 'Erro ao verificar credenciais',
                'debug' => ['error' => $e->getMessage()]
            ];
        }
    }

    public function authenticate($email, $password, $pin) {
        try {
            writeLog("Tentativa de autenticação completa para email: {$email}", 'info');
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verifica a senha
                $passwordValid = password_verify($password, $user['password']);
                writeLog("Verificação de senha na autenticação completa: " . ($passwordValid ? 'válida' : 'inválida'), 'debug');
                
                if ($passwordValid) {
                    // Verifica o PIN usando password_verify
                    writeLog("Verificando PIN para o usuário: {$email}", 'debug');
                    
                    if (password_verify($pin, $user['pin'])) {
                        writeLog("Autenticação completa bem-sucedida para: {$email}", 'success');
                        return [
                            'success' => true,
                            'user' => [
                                'id' => $user['id'],
                                'name' => $user['name'],
                                'email' => $user['email'],
                                'role' => $user['role']
                            ]
                        ];
                    }
                    writeLog("PIN inválido para o usuário: {$email}", 'warning');
                    return [
                        'success' => false,
                        'error' => 'invalid_pin',
                        'message' => 'PIN inválido'
                    ];
                }
            }

            writeLog("Falha na autenticação completa para: {$email}", 'warning');
            return [
                'success' => false,
                'error' => 'invalid_credentials',
                'message' => 'Credenciais inválidas'
            ];
        } catch (PDOException $e) {
            $errorMessage = "Erro na autenticação: " . $e->getMessage();
            writeLog($errorMessage, 'error');
            return [
                'success' => false,
                'error' => 'system_error',
                'message' => 'Erro ao autenticar: ' . $e->getMessage()
            ];
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}