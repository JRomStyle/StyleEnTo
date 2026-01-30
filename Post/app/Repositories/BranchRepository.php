<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;

final class BranchRepository
{
    public function all(): array
    {
        $stmt = DB::connection()->query('SELECT * FROM branches ORDER BY name');
        return $stmt->fetchAll();
    }
}
