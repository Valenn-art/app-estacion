<?php

class AuthController {
    
    private $usuarioModel;
    private $mailerService;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->mailerService = new MailerService();
    }
    
    public function login() {

        if (SessionManager::estaLogueado()) {
            header('Location: ' . BASE_URL . 'panel');
            exit;
        }
        
        $error = '';
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Por favor completa todos los campos';
            } else {
                $usuario = $this->usuarioModel->obtenerPorEmail($email);
                
                if (!$usuario) {
                    $error = 'Credenciales no válidas';
                } else {
                    if ($this->usuarioModel->validarPassword($password, $usuario['password'])) {
                        
                        if ($usuario['activo'] == 0) {
                            $error = 'Su usuario aún no se ha validado, revise su casilla de correo';
                        } elseif ($usuario['bloqueado'] == 1 || $usuario['recupero'] == 1) {
                            $error = 'Su usuario está bloqueado, revise su casilla de correo';
                        } else {
                            SessionManager::login($usuario);
                            
                            $info = UserAgentHelper::obtenerInfoCompleta();
                            $this->mailerService->enviarLoginExitoso(
                                $usuario['email'],
                                $usuario['nombres'],
                                $usuario['token'],
                                $info['ip'],
                                $info['so'],
                                $info['navegador']
                            );
                            
                            $destino = SessionManager::obtenerDestino();
                            header('Location: ' . ($destino ?? BASE_URL . 'panel'));
                            exit;
                        }
                    } else {
                        $info = UserAgentHelper::obtenerInfoCompleta();
                        $this->mailerService->enviarIntentoFallido(
                            $usuario['email'],
                            $usuario['nombres'],
                            $usuario['token'],
                            $info['ip'],
                            $info['so'],
                            $info['navegador']
                        );
                        
                        $error = 'Credenciales no válidas';
                    }
                }
            }
        }
        
        $this->render('login', [
            'titulo' => 'Iniciar Sesión',
            'css' => 'auth',
            'error' => $error
        ]);
    }
    
    public function register() {
        if (SessionManager::estaLogueado()) {
            header('Location: ' . BASE_URL . 'panel');
            exit;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $nombres = trim($_POST['nombres'] ?? '');
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            
            if (empty($email) || empty($nombres) || empty($password) || empty($password2)) {
                $error = 'Por favor completa todos los campos';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email no válido';
            } elseif ($password !== $password2) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } elseif ($this->usuarioModel->emailExiste($email)) {
                $error = 'El email ya está registrado. <a href="' . BASE_URL . 'login">Iniciar sesión</a>';
            } else {
                $tokens = $this->usuarioModel->crearUsuario($email, $nombres, $password);
                
                $this->mailerService->enviarValidacion($email, $nombres, $tokens['token_action']);
                
                $success = 'Cuenta creada exitosamente. Revisa tu correo para activar tu cuenta.';
            }
        }
        
        $this->render('register', [
            'titulo' => 'Registrarse',
            'css' => 'auth',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    public function validate($tokenAction) {
        if (SessionManager::estaLogueado()) {
            header('Location: ' . BASE_URL . 'panel');
            exit;
        }
        
        $mensaje = '';
        $tipo = 'error';
        
        if (empty($tokenAction)) {
            $mensaje = 'Token no válido';
        } else {
            $usuario = $this->usuarioModel->obtenerPorTokenAction($tokenAction);
            
            if (!$usuario) {
                $mensaje = 'El token no corresponde a un usuario';
            } elseif ($usuario['activo'] == 1) {
                $mensaje = 'Esta cuenta ya está activada';
                $tipo = 'info';
            } else {
                $this->usuarioModel->activarUsuario($usuario['id']);
                
                $this->mailerService->enviarCuentaActivada($usuario['email'], $usuario['nombres']);
                
                header('Location: ' . BASE_URL . 'login?activated=1');
                exit;
            }
        }
        
        $this->render('mensaje', [
            'titulo' => 'Validación de cuenta',
            'css' => 'auth',
            'mensaje' => $mensaje,
            'tipo' => $tipo
        ]);
    }
    
    public function recovery() {
        if (SessionManager::estaLogueado()) {
            header('Location: ' . BASE_URL . 'panel');
            exit;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                $error = 'Por favor ingresa tu email';
            } else {
                $usuario = $this->usuarioModel->obtenerPorEmail($email);
                
                if (!$usuario) {
                    $error = 'El email no se encuentra registrado. <a href="' . BASE_URL . 'register">Registrarse</a>';
                } else {
                    $tokenAction = $this->usuarioModel->iniciarRecuperacion($usuario['id']);
                    
                    $this->mailerService->enviarRecuperacion($usuario['email'], $usuario['nombres'], $tokenAction);
                    
                    $success = 'Se ha enviado un email con instrucciones para restablecer tu contraseña.';
                }
            }
        }
        
        $this->render('recovery', [
            'titulo' => 'Recuperar Contraseña',
            'css' => 'auth',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    public function reset($tokenAction) {
        // Si ya está logueado, redirigir a panel
        if (SessionManager::estaLogueado()) {
            header('Location: ' . BASE_URL . 'panel');
            exit;
        }
        
        $error = '';
        $usuario = null;
        
        if (empty($tokenAction)) {
            $error = 'Token no válido';
        } else {
            $usuario = $this->usuarioModel->obtenerPorTokenAction($tokenAction);
            
            if (!$usuario) {
                $error = 'El token no corresponde a un usuario';
            } elseif ($usuario['bloqueado'] == 0 && $usuario['recupero'] == 0) {
                $error = 'Este token ya no es válido';
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            
            if (empty($password) || empty($password2)) {
                $error = 'Por favor completa todos los campos';
            } elseif ($password !== $password2) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else {
                $this->usuarioModel->restablecerPassword($usuario['id'], $password);
                
                $info = UserAgentHelper::obtenerInfoCompleta();
                $this->mailerService->enviarPasswordRestablecido(
                    $usuario['email'],
                    $usuario['nombres'],
                    $usuario['token'],
                    $info['ip'],
                    $info['so'],
                    $info['navegador']
                );
                
                header('Location: ' . BASE_URL . 'login?reset=1');
                exit;
            }
        }
        
        $this->render('reset', [
            'titulo' => 'Restablecer Contraseña',
            'css' => 'auth',
            'error' => $error,
            'tokenValido' => !empty($usuario) && empty($error)
        ]);
    }
    
    public function blocked($token) {
        $mensaje = '';
        $tipo = 'error';
        
        if (empty($token)) {
            $mensaje = 'Token no válido';
        } else {
            $usuario = $this->usuarioModel->obtenerPorToken($token);
            
            if (!$usuario) {
                $mensaje = 'El token no corresponde a un usuario';
            } else {
                // Bloquear usuario
                $tokenAction = $this->usuarioModel->bloquearUsuario($usuario['id']);
                
                // Enviar email
                $this->mailerService->enviarCuentaBloqueada($usuario['email'], $usuario['nombres'], $tokenAction);
                
                $mensaje = 'Usuario bloqueado, revise su correo electrónico';
                $tipo = 'info';
                
                // Si estaba logueado, cerrar sesión
                if (SessionManager::estaLogueado()) {
                    SessionManager::logout();
                }
            }
        }
        
        $this->render('mensaje', [
            'titulo' => 'Cuenta Bloqueada',
            'css' => 'auth',
            'mensaje' => $mensaje,
            'tipo' => $tipo
        ]);
    }
    

    public function logout() {
        SessionManager::logout();
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
    

    private function render($vista, $datos = []) {
        extract($datos);
        include dirname(__DIR__) . "/templates/header.php";
        include dirname(__DIR__) . "/views/$vista.php";
        include dirname(__DIR__) . "/templates/footer.php";
    }
}
?>