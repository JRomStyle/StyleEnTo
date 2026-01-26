<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Security\Csrf;

abstract class BaseController
{
    protected function view(string $view, array $data = []): Response
    {
        $data['authUser'] = Auth::user();
        $data['csrf'] = Csrf::token();
        $data['basePath'] = $this->basePath();
        $content = View::render($view, $data);
        $body = View::render('layout', [
            'content' => $content,
            'authUser' => $data['authUser'],
            'csrf' => $data['csrf'],
            'basePath' => $data['basePath']
        ]);
        return new Response($body);
    }

    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function requireAuth(): ?Response
    {
        if (!Auth::check()) {
            return new Response('', 302, ['Location' => $this->path('/login')]);
        }
        return null;
    }

    protected function requirePermission(string $permission): ?Response
    {
        if (!Auth::check()) {
            return new Response('', 302, ['Location' => $this->path('/login')]);
        }
        if (!Auth::can($permission)) {
            $content = View::render('errors/403');
            $body = View::render('layout', [
                'content' => $content,
                'authUser' => Auth::user(),
                'csrf' => Csrf::token(),
                'basePath' => $this->basePath()
            ]);
            return new Response($body, 403);
        }
        return null;
    }

    protected function basePath(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(str_replace('\\', '/', dirname($script)), '/');
        if ($base === '/') {
            $base = '';
        }
        if ($base === '') {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $path = parse_url($uri, PHP_URL_PATH) ?: '/';
            $project = basename(dirname(__DIR__, 2));
            $prefix = '/' . strtolower($project);
            if (str_starts_with(strtolower($path), $prefix)) {
                $base = substr($path, 0, strlen($prefix));
            }
        }
        return $base;
    }

    protected function path(string $path): string
    {
        $base = $this->basePath();
        $normalized = '/' . ltrim($path, '/');
        return $base . $normalized;
    }
}
