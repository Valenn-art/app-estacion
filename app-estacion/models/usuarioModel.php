<?php

class UsuarioModel {
    
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public function generarToken() {
        return bin2hex(random_bytes(32));
    }
    
    public function emailExiste($email) {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    // Crear usuario
    public function crearUsuario($email, $nombres, $password) {
        $token = $this->generarToken();
        $tokenAction = $this->generarToken();
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (token, email, nombres, password, token_action, add_date)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([$token, $email, $nombres, $passwordHash, $tokenAction]);
        
        return [
            'token' => $token,
            'token_action' => $tokenAction
        ];
    }
    
    public function obtenerPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorToken($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorTokenAction($tokenAction) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE token_action = ?");
        $stmt->execute([$tokenAction]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function validarPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function activarUsuario($id) {
        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET activo = 1, token_action = NULL, active_date = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
    
    public function bloquearUsuario($id) {
        $tokenAction = $this->generarToken();
        
        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET bloqueado = 1, token_action = ?, blocked_date = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$tokenAction, $id]);
        return $tokenAction;
    }
    
    public function iniciarRecuperacion($id) {
        $tokenAction = $this->generarToken();
        
        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET recupero = 1, token_action = ?, recover_date = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$tokenAction, $id]);
        return $tokenAction;
    }
    
    public function restablecerPassword($id, $nuevaPassword) {
        $passwordHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        
        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET password = ?, recupero = 0, bloqueado = 0, token_action = NULL, update_date = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([$passwordHash, $id]);
    }
    
    public function puedeAcceder($usuario) {
        return $usuario['activo'] == 1 && 
               $usuario['bloqueado'] == 0 && 
               $usuario['recupero'] == 0;
    }
}
?>