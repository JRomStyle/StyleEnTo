<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class UserService
{
    public function all(): array
    {
        return (new UserRepository())->all();
    }
}
