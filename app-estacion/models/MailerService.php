<?php
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService {
    
    private $mail;
    
    private function getFullBaseUrl() {
        // Si el desarrollador configuró APP_URL explícitamente, usarla
        if (defined('APP_URL') && !empty(APP_URL)) {
            return rtrim(APP_URL, '/');
        }

        // Si BASE_URL ya contiene esquema, usarla
        if (strpos(BASE_URL, 'http://') === 0 || strpos(BASE_URL, 'https://') === 0) {
            return rtrim(BASE_URL, '/');
        }

        // Construir a partir de $_SERVER como última opción
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . rtrim(BASE_URL, '/');
    }
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Configuración SMTP
        $this->mail->isSMTP();
        $this->mail->Host = MAIL_HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = MAIL_USERNAME;
        $this->mail->Password = MAIL_PASSWORD;
        $this->mail->SMTPSecure = MAIL_ENCRYPTION;
        $this->mail->Port = MAIL_PORT;
        $this->mail->CharSet = 'UTF-8';
        
        // Remitente
        $this->mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    }
    
    public function enviarEmail($destino, $asunto, $cuerpoHTML) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($destino);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = $cuerpoHTML;
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    // Email de validación de cuenta
    public function enviarValidacion($email, $nombres, $tokenAction) {
        $base = $this->getFullBaseUrl();
        $linkValidacion = $base . '/validate/' . $tokenAction;
        
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #4CAF50;'>¡Bienvenido a App Estación, {$nombres}!</h2>
                <p>Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente botón:</p>
                <a href='{$linkValidacion}' style='display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    Click aquí para activar tu usuario
                </a>
                <p style='color: #666; font-size: 12px; margin-top: 30px;'>Si no te registraste en nuestra aplicación, ignora este mensaje.</p>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, "Valida tu cuenta - App Estación", $html);
    }
    
    // Email de inicio de sesión exitoso
    public function enviarLoginExitoso($email, $nombres, $token, $ip, $so, $navegador) {
        $base = $this->getFullBaseUrl();
        $linkBloquear = $base . "/blocked/" . $token;
        
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #2196F3;'>Inicio de sesión detectado</h2>
                <p>Hola {$nombres},</p>
                <p>Se ha iniciado sesión en tu cuenta con los siguientes datos:</p>
                <ul style='background: #f9f9f9; padding: 20px; border-radius: 5px;'>
                    <li><strong>IP:</strong> {$ip}</li>
                    <li><strong>Sistema Operativo:</strong> {$so}</li>
                    <li><strong>Navegador:</strong> {$navegador}</li>
                </ul>
                <p>Si no fuiste tú, bloquea tu cuenta inmediatamente:</p>
                <a href='{$linkBloquear}' style='display: inline-block; padding: 12px 24px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    No fui yo, bloquear cuenta
                </a>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, "Inicio de sesión - App Estación", $html);
    }
    
    // Email de intento de acceso con contraseña inválida
    public function enviarIntentoFallido($email, $nombres, $token, $ip, $so, $navegador) {
        $base = $this->getFullBaseUrl();
        $linkBloquear = $base . "/blocked/" . $token;
        
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #ff9800;'> Intento de acceso con contraseña inválida</h2>
                <p>Hola {$nombres},</p>
                <p>Se detectó un intento de inicio de sesión con contraseña incorrecta:</p>
                <ul style='background: #fff3e0; padding: 20px; border-radius: 5px;'>
                    <li><strong>IP:</strong> {$ip}</li>
                    <li><strong>Sistema Operativo:</strong> {$so}</li>
                    <li><strong>Navegador:</strong> {$navegador}</li>
                </ul>
                <p>Si no fuiste tú, bloquea tu cuenta inmediatamente:</p>
                <a href='{$linkBloquear}' style='display: inline-block; padding: 12px 24px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    No fui yo, bloquear cuenta
                </a>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, " Intento de acceso fallido - App Estación", $html);
    }
    
    // Email de cuenta activada
    public function enviarCuentaActivada($email, $nombres) {
        $base = $this->getFullBaseUrl();
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #4CAF50;'> Cuenta activada</h2>
                <p>Hola {$nombres},</p>
                <p>Tu cuenta ha sido activada exitosamente. Ya puedes iniciar sesión en App Estación.</p>
                <a href='" . $base . "/login' style='display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    Ir a Login
                </a>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, "Cuenta activada - App Estación", $html);
    }
    
    // Email de cuenta bloqueada
    public function enviarCuentaBloqueada($email, $nombres, $tokenAction) {
        $base = $this->getFullBaseUrl();
        $linkReset = $base . "/reset/" . $tokenAction;
        
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #f44336;'> Cuenta bloqueada</h2>
                <p>Hola {$nombres},</p>
                <p>Tu cuenta ha sido bloqueada por seguridad. Para cambiar tu contraseña y desbloquearla:</p>
                <a href='{$linkReset}' style='display: inline-block; padding: 12px 24px; background-color: #FF9800; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    Click aquí para cambiar contraseña
                </a>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, "Cuenta bloqueada - App Estación", $html);
    }
    
    // Email de recuperación de contraseña
    public function enviarRecuperacion($email, $nombres, $tokenAction) {
        $base = $this->getFullBaseUrl();
        $linkReset = $base . "/reset/" . $tokenAction;
        
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #2196F3;'>Recuperación de contraseña</h2>
                <p>Hola {$nombres},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña. Haz clic en el siguiente botón:</p>
                <a href='{$linkReset}' style='display: inline-block; padding: 12px 24px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    Click aquí para restablecer contraseña
                </a>
                <p style='color: #666; font-size: 12px; margin-top: 30px;'>Si no solicitaste este cambio, ignora este mensaje.</p>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, "Recuperación de contraseña - App Estación", $html);
    }
    
    // Email de contraseña restablecida
    public function enviarPasswordRestablecido($email, $nombres, $token, $ip, $so, $navegador) {
        $base = $this->getFullBaseUrl();
        $linkBloquear = $base . "/blocked/" . $token;
        
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px;'>
                <h2 style='color: #4CAF50;'> Contraseña restablecida</h2>
                <p>Hola {$nombres},</p>
                <p>Tu contraseña ha sido restablecida exitosamente con los siguientes datos:</p>
                <ul style='background: #f9f9f9; padding: 20px; border-radius: 5px;'>
                    <li><strong>IP:</strong> {$ip}</li>
                    <li><strong>Sistema Operativo:</strong> {$so}</li>
                    <li><strong>Navegador:</strong> {$navegador}</li>
                </ul>
                <p>Si no fuiste tú, bloquea tu cuenta inmediatamente:</p>
                <a href='{$linkBloquear}' style='display: inline-block; padding: 12px 24px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0;'>
                    No fui yo, bloquear cuenta
                </a>
            </div>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($email, "Contraseña restablecida - App Estación", $html);
    }
}
?>