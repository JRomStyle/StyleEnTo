<?php
class Vencimiento
{
    public static function create($cliente_id, $descripcion, $fecha_limite, $estado = 'pendiente')
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO vencimientos (cliente_id, descripcion, fecha_limite, estado) VALUES (?, ?, ?, ?)');
        $stmt->execute([$cliente_id, $descripcion, $fecha_limite, $estado]);
    }
    public static function allUpcoming()
    {
        $pdo = Database::getInstance();
        return $pdo->query('SELECT v.*, c.nombre AS cliente_nombre FROM vencimientos v LEFT JOIN clientes c ON c.id = v.cliente_id ORDER BY fecha_limite ASC')->fetchAll();
    }
    public static function markPagado($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE vencimientos SET estado = 'pagado' WHERE id = ?");
        $stmt->execute([$id]);
    }
    public static function countProximos7Dias()
    {
        $pdo = Database::getInstance();
        $row = $pdo->query("SELECT COUNT(*) AS c FROM vencimientos WHERE estado = 'pendiente' AND DATEDIFF(fecha_limite, CURDATE()) BETWEEN 0 AND 7")->fetch();
        return (int)$row['c'];
    }
}

