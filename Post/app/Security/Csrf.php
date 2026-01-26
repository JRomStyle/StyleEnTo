<?php
declare(strict_types=1);

namespace App\Security;

use App\Core\Session;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf', $token);
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        $stored = Session::get('_csrf');
        return $stored && hash_equals($stored, (string)$token);
    }
}
