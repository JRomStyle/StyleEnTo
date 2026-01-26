<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class DB
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance) {
            return self::$instance;
        }
        $host = Config::get('db.host');
        $port = Config::get('db.port');
        $db = Config::get('db.database');
        $user = Config::get('db.username');
        $pass = Config::get('db.password');
        $charset = Config::get('db.charset', 'utf8mb4');
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
        self::$instance = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return self::$instance;
    }
}
