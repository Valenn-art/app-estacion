<?php
require_once 'env.php';
require_once 'controllers/estacionController.php';
require_once 'models/estacionModel.php';

$route = '';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = parse_url(BASE_URL, PHP_URL_PATH);
if (strpos($uri, $basePath) === 0) {
    $route = substr($uri, strlen($basePath));
} else {
    $route = $uri;
}
$route = trim($route, '/');

// Modo debug opcional: muestra la ruta calculada
if (isset($_GET['__debug_route']) && $_GET['__debug_route'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "BASE_PATH: " . $basePath . "\n";
    echo "REQUEST_URI: " . $uri . "\n";
    echo "CALCULATED_ROUTE: " . $route . "\n";
    echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
    exit;
}

// Instanciar controller
$controller = new EstacionController();

// Rutas disponibles
if ($route === '' || $route === 'landing') {
    $controller->landing();
} elseif ($route === 'panel') {
    $controller->panel();
} elseif (strpos($route, 'detalle/') === 0) {
    $chipid = substr($route, 8); 
    $controller->detalle($chipid);
} else {
    header("HTTP/1.0 404 Not Found");
    $controller->landing();
}
?>