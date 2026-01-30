<?php
namespace App\Models;
use App\Database;
use PDO;
class User {
    public static function create(string $nombre, string $email, string $password, string $rol = 'cliente'): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_DEFAULT), $rol]);
    }
    public static function findByEmail(string $email): ?array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function verify(string $email, string $password): ?array {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
