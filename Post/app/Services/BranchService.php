<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\BranchRepository;

final class BranchService
{
    public function all(): array
    {
        return (new BranchRepository())->all();
    }
}
