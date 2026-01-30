<?php
namespace App\Models;
use App\Database;
use PDO;
class Category {
    public static function all(): array {
        $pdo = Database::conn();
        $stmt = $pdo->query('SELECT * FROM categorias ORDER BY nombre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function create(string $nombre, string $descripcion): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare('INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)');
        return $stmt->execute([$nombre, $descripcion]);
    }
    public static function find(int $id): ?array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function update(int $id, string $nombre, string $descripcion): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare('UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?');
        return $stmt->execute([$nombre, $descripcion, $id]);
    }
    public static function delete(int $id): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
