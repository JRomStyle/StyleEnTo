<?php
class Router
{
    private $routes = [];
    public function get($path, $handler, $middlewares = [])
    {
        $this->routes['GET'][$this->normalize($path)] = ['handler' => $handler, 'middlewares' => $middlewares];
    }
    public function post($path, $handler, $middlewares = [])
    {
        $this->routes['POST'][$this->normalize($path)] = ['handler' => $handler, 'middlewares' => $middlewares];
    }
    private function normalize($path)
    {
        $p = rtrim($path, '/');
        return $p === '' ? '/' : $p;
    }
    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $config = require __DIR__ . '/../config/config.php';
        $bases = [
            rtrim($config['app']['base_url'], '/'),
            rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')
        ];
        foreach ($bases as $base) {
            if ($base && strpos($uri, $base) === 0) {
                $uri = substr($uri, strlen($base));
                break;
            }
        }
        $uri = $this->normalize($uri);
        $method = $_SERVER['REQUEST_METHOD'];
        $route = $this->routes[$method][$uri] ?? null;
        if (!$route) {
            http_response_code(404);
            echo '404';
            return;
        }
        foreach ($route['middlewares'] as $m) {
            if ($m === 'auth' && !Auth::check()) {
                header('Location: ' . $config['app']['base_url'] . '/login');
                return;
            }
            if ($m === 'admin' && !Auth::isAdmin()) {
                http_response_code(403);
                echo '403';
                return;
            }
        }
        $parts = explode('@', $route['handler']);
        $controller = new $parts[0]();
        $controller->{$parts[1]}();
    }
}
