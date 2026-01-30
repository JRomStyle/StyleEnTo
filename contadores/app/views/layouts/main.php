<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
$user = Auth::user();
?>
<!doctype html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ContaGestor DIAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/custom.css">
</head>
<body class="bg-gray-900 text-gray-100">
<?php if ($user): ?>
<div class="min-h-screen flex">
    <aside class="w-64 bg-gray-800 border-r border-gray-700">
        <div class="p-4">
            <div class="text-xl font-semibold">ContaGestor DIAN</div>
            <div class="text-sm text-gray-400 mt-1"><?= htmlspecialchars($user['nombre']) ?> (<?= htmlspecialchars($user['rol']) ?>)</div>
        </div>
        <nav class="px-2 space-y-1">
            <a href="<?= $base ?>/dashboard" class="block px-3 py-2 rounded hover:bg-gray-700">Dashboard</a>
            <a href="<?= $base ?>/clientes" class="block px-3 py-2 rounded hover:bg-gray-700">Clientes</a>
            <a href="<?= $base ?>/documentos" class="block px-3 py-2 rounded hover:bg-gray-700">Documentos</a>
            <a href="<?= $base ?>/vencimientos" class="block px-3 py-2 rounded hover:bg-gray-700">Vencimientos</a>
            <?php if ($user['rol'] === 'admin'): ?>
            <a href="<?= $base ?>/usuarios" class="block px-3 py-2 rounded hover:bg-gray-700">Usuarios</a>
            <a href="<?= $base ?>/configuracion" class="block px-3 py-2 rounded hover:bg-gray-700">Configuración</a>
            <?php endif; ?>
            <a href="<?= $base ?>/logout" class="block px-3 py-2 rounded hover:bg-gray-700">Salir</a>
        </nav>
    </aside>
    <main class="flex-1">
        <div class="p-6">
            <?php require $viewFile; ?>
        </div>
    </main>
</div>
<?php else: ?>
<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <?php require $viewFile; ?>
    </div>
</div>
<?php endif; ?>
<script src="<?= $base ?>/assets/js/app.js"></script>
</body>
</html>

