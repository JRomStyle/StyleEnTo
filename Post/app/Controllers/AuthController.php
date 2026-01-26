<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Security\Csrf;
use App\Services\AuthService;
use App\Services\AuditService;
use App\Repositories\UserRepository;

final class AuthController extends BaseController
{
    public function showLogin(Request $request): Response
    {
        return $this->view('auth/login');
    }

    public function showRegister(Request $request): Response
    {
        return $this->view('auth/register');
    }

    public function showAdminReset(Request $request): Response
    {
        if (!$this->isLocalRequest()) {
            return new Response('Not Found', 404);
        }
        return $this->view('auth/admin_reset');
    }

    public function login(Request $request): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return new Response('Invalid CSRF', 419);
        }
        $email = (string)$request->input('email');
        $password = (string)$request->input('password');
        $authService = new AuthService();
        $user = $authService->attempt($email, $password);
        if (!$user) {
            return $this->view('auth/login', ['error' => 'Credenciales inválidas']);
        }
        (new AuditService())->log('login', $user['id'], ['email' => $email]);
        return new Response('', 302, ['Location' => $this->path('/dashboard')]);
    }

    public function register(Request $request): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return new Response('Invalid CSRF', 419);
        }
        $name = trim((string)$request->input('name'));
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');
        $confirm = (string)$request->input('password_confirm');
        if ($name === '' || $email === '' || $password === '') {
            return $this->view('auth/register', ['error' => 'Todos los campos son obligatorios']);
        }
        if ($password !== $confirm) {
            return $this->view('auth/register', ['error' => 'Las contraseñas no coinciden']);
        }
        $authService = new AuthService();
        $user = $authService->register($name, $email, $password, 1, 3);
        if (!$user) {
            return $this->view('auth/register', ['error' => 'El correo ya está registrado']);
        }
        (new AuditService())->log('register', $user['id'], ['email' => $email]);
        return new Response('', 302, ['Location' => $this->path('/dashboard')]);
    }

    public function resetAdmin(Request $request): Response
    {
        if (!$this->isLocalRequest()) {
            return new Response('Not Found', 404);
        }
        if (!Csrf::validate($request->input('_csrf'))) {
            return new Response('Invalid CSRF', 419);
        }
        $password = (string)$request->input('password');
        $confirm = (string)$request->input('password_confirm');
        if ($password === '' || $confirm === '') {
            return $this->view('auth/admin_reset', ['error' => 'Todos los campos son obligatorios']);
        }
        if ($password !== $confirm) {
            return $this->view('auth/admin_reset', ['error' => 'Las contraseñas no coinciden']);
        }
        $users = new UserRepository();
        $updated = $users->updatePasswordByEmail('admin@pos.com', $password);
        if (!$updated) {
            return $this->view('auth/admin_reset', ['error' => 'No se encontró el usuario admin@pos.com']);
        }
        (new AuditService())->log('admin_password_reset');
        return new Response('', 302, ['Location' => $this->path('/login')]);
    }

    private function isLocalRequest(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return $ip === '127.0.0.1' || $ip === '::1';
    }

    public function logout(Request $request): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return new Response('Invalid CSRF', 419);
        }
        (new AuditService())->log('logout');
        (new AuthService())->logout();
        return new Response('', 302, ['Location' => $this->path('/login')]);
    }
}
