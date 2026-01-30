<?php
$config = require __DIR__ . '/../config/config.php';
spl_autoload_register(function ($class) {
    $dirs = [
        __DIR__ . '/../core',
        __DIR__ . '/../app/controllers',
        __DIR__ . '/../app/models',
        __DIR__ . '/../app/middleware'
    ];
    foreach ($dirs as $d) {
        $file = $d . '/' . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
Session::start();
if (!is_dir($config['app']['upload_dir'])) {
    @mkdir($config['app']['upload_dir'], 0777, true);
}
$router = require __DIR__ . '/../config/routes.php';
$pdo = Database::getInstance();
$row = $pdo->query('SELECT COUNT(*) AS c FROM usuarios')->fetch();
if ((int)$row['c'] === 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)');
    $stmt->execute(['Administrador', 'admin@local', $hash, 'admin']);
}
$router->dispatch();
