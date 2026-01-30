<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = rtrim($request->uri(), '/') ?: '/';
        $handler = $this->routes[$method][$uri] ?? null;
        if (!$handler) {
            return new Response('Not Found', 404);
        }
        [$class, $action] = $handler;
        $controller = new $class();
        return $controller->$action($request);
    }
}
