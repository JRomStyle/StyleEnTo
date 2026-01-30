<?php
class Usuario
{
    public static function findByCorreo($correo)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE correo = ? LIMIT 1');
        $stmt->execute([$correo]);
        return $stmt->fetch();
    }
    public static function createAssistant($nombre, $correo, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nombre, $correo, $hash, 'asistente']);
    }
    public static function createAdmin($nombre, $correo, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nombre, $correo, $hash, 'admin']);
    }
    public static function all()
    {
        $pdo = Database::getInstance();
        return $pdo->query('SELECT id, nombre, correo, rol FROM usuarios ORDER BY id DESC')->fetchAll();
    }
}

