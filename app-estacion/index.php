<?php
require_once 'env.php';
require_once 'models/estacionModel.php';
require_once 'models/usuarioModel.php';
require_once 'models/MailerService.php';
require_once 'models/SessionManager.php';
require_once 'models/UserAgentHelper.php';
require_once 'controllers/estacionController.php';
require_once 'controllers/AuthController.php';

SessionManager::iniciar();

$route = '';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = parse_url(BASE_URL, PHP_URL_PATH);
if (strpos($uri, $basePath) === 0) {
    $route = substr($uri, strlen($basePath));
} else {
    $route = $uri;
}
$route = trim($route, '/');

if (isset($_GET['__debug_route']) && $_GET['__debug_route'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "BASE_PATH: " . $basePath . "\n";
    echo "REQUEST_URI: " . $uri . "\n";
    echo "CALCULATED_ROUTE: " . $route . "\n";
    echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
    echo "LOGGED_IN: " . (SessionManager::estaLogueado() ? 'YES' : 'NO') . "\n";
    exit;
}

$estacionController = new EstacionController();
$authController = new AuthController();


if ($route === '' || $route === 'landing') {
    $estacionController->landing();
    
} elseif ($route === 'panel') {
    $estacionController->panel();
    
} elseif (strpos($route, 'detalle/') === 0) {
    $chipid = substr($route, 8); 
    $estacionController->detalle($chipid);


} elseif ($route === 'login') {
    $authController->login();

} elseif ($route === 'register') {
    $authController->register();

} elseif (strpos($route, 'validate/') === 0) {
    $tokenAction = substr($route, 9);
    $authController->validate($tokenAction);

} elseif ($route === 'recovery') {
    $authController->recovery();

} elseif (strpos($route, 'reset/') === 0) {
    $tokenAction = substr($route, 6);
    $authController->reset($tokenAction);

} elseif (strpos($route, 'blocked/') === 0) {
    $token = substr($route, 8);
    $authController->blocked($token);

} elseif ($route === 'logout') {
    $authController->logout();

} else {
    header("HTTP/1.0 404 Not Found");
    $estacionController->landing();
}
?>