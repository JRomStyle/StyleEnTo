<?php
declare(strict_types=1);

namespace App\Core;

use App\Repositories\UserRepository;

final class Auth
{
    public static function user(): ?array
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        return (new UserRepository())->findById((int)$userId);
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(int $userId): void
    {
        Session::set('user_id', $userId);
    }

    public static function logout(): void
    {
        Session::forget('user_id');
    }

    public static function permissions(): array
    {
        return Session::get('permissions', []);
    }

    public static function can(string $permission): bool
    {
        return in_array($permission, self::permissions(), true);
    }
}
