<?php
namespace App\Models;
use App\Database;
class OrderItem {
    public static function create(int $pedido_id, int $producto_id, int $cantidad, float $precio_unitario): bool {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$pedido_id, $producto_id, $cantidad, $precio_unitario]);
    }
    public static function byOrder(int $pedido_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT d.*, p.nombre, p.imagen FROM detalle_pedido d LEFT JOIN productos p ON d.producto_id=p.id WHERE d.pedido_id=?");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
