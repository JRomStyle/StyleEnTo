<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;

final class RoleRepository
{
    public function findById(int $id): ?array
    {
        $stmt = DB::connection()->prepare('SELECT * FROM roles WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $role = $stmt->fetch();
        return $role ?: null;
    }

    public function permissions(int $roleId): array
    {
        $stmt = DB::connection()->prepare('SELECT p.key_name FROM permissions p INNER JOIN role_permissions rp ON rp.permission_id = p.id WHERE rp.role_id = :role_id');
        $stmt->execute(['role_id' => $roleId]);
        return array_column($stmt->fetchAll(), 'key_name');
    }
}
