<?php
declare(strict_types=1);
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
require __DIR__ . '/Support/helpers.php';
