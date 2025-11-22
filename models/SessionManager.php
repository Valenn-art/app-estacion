<?php

class SessionManager {
    
    public static function iniciar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }
    
    public static function estaLogueado() {
        self::iniciar();
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_token']);
    }
    
    public static function login($usuario) {
        self::iniciar();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_token'] = $usuario['token'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nombres'] = $usuario['nombres'];
    }
    
    public static function logout() {
        self::iniciar();
        session_unset();
        session_destroy();
    }
    
    public static function obtenerUsuarioId() {
        self::iniciar();
        return $_SESSION['usuario_id'] ?? null;
    }
    
    public static function obtenerUsuarioEmail() {
        self::iniciar();
        return $_SESSION['usuario_email'] ?? null;
    }
    
    public static function obtenerUsuarioNombres() {
        self::iniciar();
        return $_SESSION['usuario_nombres'] ?? null;
    }
    
    public static function obtenerUsuarioToken() {
        self::iniciar();
        return $_SESSION['usuario_token'] ?? null;
    }
    
    // Guardar URL de destino después del login
    public static function guardarDestino($url) {
        self::iniciar();
        $_SESSION['destino_login'] = $url;
    }
    
    public static function obtenerDestino() {
        self::iniciar();
        $destino = $_SESSION['destino_login'] ?? null;
        unset($_SESSION['destino_login']);
        return $destino;
    }
}
?>