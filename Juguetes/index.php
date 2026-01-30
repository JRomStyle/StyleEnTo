<?php
require __DIR__ . '/app/bootstrap.php';
$route = isset($_GET['route']) ? $_GET['route'] : 'home/index';
$parts = explode('/', trim($route, '/'));
$controllerName = ucfirst($parts[0]) . 'Controller';
$action = $parts[1] ?? 'index';
$controllerClass = 'App\\Controllers\\' . $controllerName;
if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
    http_response_code(404);
    echo '404';
    exit;
}
$controller = new $controllerClass();
$controller->$action();
