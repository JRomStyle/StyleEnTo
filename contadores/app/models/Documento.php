<?php
class Documento
{
    public static function create($cliente_id, $tipo_documento, $archivo, $fecha_subida)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO documentos (cliente_id, tipo_documento, archivo, fecha_subida, estado) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$cliente_id, $tipo_documento, $archivo, $fecha_subida, 'pendiente']);
    }
    public static function forCliente($cliente_id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM documentos WHERE cliente_id = ? ORDER BY fecha_subida DESC');
        $stmt->execute([$cliente_id]);
        return $stmt->fetchAll();
    }
    public static function all()
    {
        $pdo = Database::getInstance();
        return $pdo->query('SELECT d.*, c.nombre AS cliente_nombre FROM documentos d LEFT JOIN clientes c ON c.id = d.cliente_id ORDER BY fecha_subida DESC')->fetchAll();
    }
    public static function countPendientes()
    {
        $pdo = Database::getInstance();
        $row = $pdo->query("SELECT COUNT(*) AS c FROM documentos WHERE estado = 'pendiente'")->fetch();
        return (int)$row['c'];
    }
}

