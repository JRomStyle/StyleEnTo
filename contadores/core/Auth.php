<?php
class Auth
{
    public static function check()
    {
        Session::start();
        return isset($_SESSION['user']);
    }
    public static function user()
    {
        Session::start();
        return $_SESSION['user'] ?? null;
    }
    public static function login($correo, $password)
    {
        $user = Usuario::findByCorreo($correo);
        if (!$user) {
            return false;
        }
        if (password_verify($password, $user['contrasena'])) {
            Session::set('user', [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'correo' => $user['correo'],
                'rol' => $user['rol']
            ]);
            return true;
        }
        return false;
    }
    public static function logout()
    {
        Session::start();
        Session::unset('user');
    }
    public static function isAdmin()
    {
        $u = self::user();
        return $u && $u['rol'] === 'admin';
    }
}

