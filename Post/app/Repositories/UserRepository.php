<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = DB::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = DB::connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function all(): array
    {
        $stmt = DB::connection()->query('SELECT id, name, email, role_id, branch_id FROM users ORDER BY name');
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $stmt = DB::connection()->query('SELECT COUNT(*) as total FROM users');
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function create(string $name, string $email, string $password, int $branchId, int $roleId): ?array
    {
        $stmt = DB::connection()->prepare('INSERT INTO users (branch_id, role_id, name, email, password_hash, created_at) VALUES (:branch_id, :role_id, :name, :email, :password_hash, NOW())');
        $stmt->execute([
            'branch_id' => $branchId,
            'role_id' => $roleId,
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT)
        ]);
        return $this->findByEmail($email);
    }

    public function updatePasswordByEmail(string $email, string $password): bool
    {
        $stmt = DB::connection()->prepare('UPDATE users SET password_hash = :password_hash WHERE email = :email');
        $stmt->execute([
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT)
        ]);
        return $stmt->rowCount() > 0;
    }
}
