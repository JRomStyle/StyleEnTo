<?php

// Iniciar sesión
session_start();

// Autocarga de clases (simplificada para este ejemplo)
spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Configurar cabeceras CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Obtener la URI y el método de solicitud
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Eliminar la parte del directorio base de la URI (si es necesario)
$basePath = '/AL';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Dividir la URI en partes
$uriParts = explode('/', trim($uri, '/'));

// Router simple
$controller = null;
$action = null;
$params = [];

// Manejar rutas API
if (isset($uriParts[0]) && $uriParts[0] === 'api') {
    if (isset($uriParts[1])) {
        $controllerName = ucfirst($uriParts[1]) . 'Controller';
        $controllerPath = __DIR__ . '/controllers/' . $controllerName . '.php';
        
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new $controllerName();
            
            // Determinar la acción
            if (isset($uriParts[2])) {
                $action = $uriParts[2];
                
                // Obtener parámetros adicionales
                if (isset($uriParts[3])) {
                    $params[] = $uriParts[3];
                }
            } else {
                // Acción por defecto basada en el método HTTP
                switch ($method) {
                    case 'GET':
                        $action = 'getAll';
                        break;
                    case 'POST':
                        $action = 'create';
                        break;
                    default:
                        http_response_code(405);
                        echo json_encode(['error' => 'Método no permitido']);
                        exit;
                }
            }
            
            // Ejecutar la acción
            if (method_exists($controller, $action)) {
                call_user_func_array([$controller, $action], $params);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Acción no encontrada']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Controlador no encontrado']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint no encontrado']);
    }
} else {
    // Rutas web (frontend)
    include __DIR__ . '/public/index.html';
}
