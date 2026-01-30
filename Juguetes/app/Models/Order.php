<?php
namespace App\Models;
use App\Database;
use PDO;
class Order {
    public static function create(int $usuario_id, float $total, string $estado = 'pendiente'): int {
        $pdo = Database::conn();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, ?)");
            $stmt->execute([$usuario_id, $total, $estado]);
            $id = (int)$pdo->lastInsertId();
            $hist = $pdo->prepare("INSERT INTO pedido_historial (pedido_id, estado, usuario_admin_id, nota) VALUES (?, ?, NULL, NULL)");
            $hist->execute([$id, $estado]);
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
    public static function all(): array {
        $pdo = Database::conn();
        $stmt = $pdo->query("SELECT p.*, u.nombre AS cliente FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id=u.id ORDER BY p.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function byUser(int $usuario_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id=? ORDER BY id DESC");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function find(int $id): ?array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT p.*, u.nombre AS cliente, u.email AS cliente_email FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id=u.id WHERE p.id=? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function findForUser(int $id, int $usuario_id): ?array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id=? AND usuario_id=? LIMIT 1");
        $stmt->execute([$id, $usuario_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function items(int $pedido_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT d.*, p.nombre, p.imagen FROM detalle_pedido d LEFT JOIN productos p ON d.producto_id=p.id WHERE d.pedido_id=? ORDER BY d.id ASC");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function history(int $pedido_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT h.*, u.nombre AS admin_nombre FROM pedido_historial h LEFT JOIN usuarios u ON h.usuario_admin_id=u.id WHERE h.pedido_id=? ORDER BY h.id DESC");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function historyPublic(int $pedido_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT estado, nota, fecha FROM pedido_historial WHERE pedido_id=? ORDER BY id DESC");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function inventoryMovements(int $pedido_id): array {
        $pdo = Database::conn();
        $stmt = $pdo->prepare("SELECT m.*, p.nombre AS producto_nombre FROM inventario_movimientos m LEFT JOIN productos p ON m.producto_id=p.id WHERE m.pedido_id=? ORDER BY m.id DESC");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function updateStatus(int $id, string $estado, ?int $adminId = null, ?string $nota = null): bool {
        $allowed = ['pendiente','pagado','enviado','cancelado'];
        if (!in_array($estado, $allowed, true)) return false;
        $pdo = Database::conn();
        $pdo->beginTransaction();
        try {
            $cur = $pdo->prepare("SELECT estado FROM pedidos WHERE id=? LIMIT 1");
            $cur->execute([$id]);
            $old = $cur->fetch(PDO::FETCH_ASSOC);
            if (!$old) {
                $pdo->rollBack();
                return false;
            }
            $oldEstado = (string)$old['estado'];
            if ($oldEstado === $estado) {
                $pdo->rollBack();
                return true;
            }
            $stmt = $pdo->prepare("UPDATE pedidos SET estado=? WHERE id=?");
            $ok = $stmt->execute([$estado, $id]);
            if ($ok) {
                $hist = $pdo->prepare("INSERT INTO pedido_historial (pedido_id, estado, usuario_admin_id, nota) VALUES (?, ?, ?, ?)");
                $hist->execute([$id, $estado, $adminId, $nota]);
            }
            $pdo->commit();
            return $ok;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
