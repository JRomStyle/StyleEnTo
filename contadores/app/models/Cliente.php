<?php
class Cliente
{
    public static function all()
    {
        $pdo = Database::getInstance();
        return $pdo->query('SELECT * FROM clientes ORDER BY id DESC')->fetchAll();
    }
    public static function create($tipo, $nombre, $documento, $correo, $telefono, $direccion, $regimen = null)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO clientes (tipo, nombre, documento, correo, telefono, direccion, regimen) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$tipo, $nombre, $documento, $correo, $telefono, $direccion, $regimen]);
    }
    public static function find($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM clientes WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function update($id, $tipo, $nombre, $documento, $correo, $telefono, $direccion, $regimen = null)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE clientes SET tipo = ?, nombre = ?, documento = ?, correo = ?, telefono = ?, direccion = ?, regimen = ? WHERE id = ?');
        $stmt->execute([$tipo, $nombre, $documento, $correo, $telefono, $direccion, $regimen, $id]);
    }
    public static function delete($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM clientes WHERE id = ?');
        $stmt->execute([$id]);
    }
    public static function count()
    {
        $pdo = Database::getInstance();
        $row = $pdo->query('SELECT COUNT(*) AS c FROM clientes')->fetch();
        return (int)$row['c'];
    }
}

