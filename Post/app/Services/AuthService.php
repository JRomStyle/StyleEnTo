<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Core\Session;

final class AuthService
{
    public function attempt(string $email, string $password): ?array
    {
        $user = (new UserRepository())->findByEmail($email);
        if (!$user) {
            return null;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }
        Auth::login((int)$user['id']);
        $permissions = (new RoleRepository())->permissions((int)$user['role_id']);
        Session::set('permissions', $permissions);
        return $user;
    }

    public function register(string $name, string $email, string $password, int $branchId, int $roleId): ?array
    {
        $users = new UserRepository();
        if ($users->findByEmail($email)) {
            return null;
        }
        $user = $users->create($name, $email, $password, $branchId, $roleId);
        if (!$user) {
            return null;
        }
        Auth::login((int)$user['id']);
        $permissions = (new RoleRepository())->permissions((int)$user['role_id']);
        Session::set('permissions', $permissions);
        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
        Session::forget('permissions');
    }
}
