<?php
$config = require __DIR__ . '/../../../config/config.php';
?>
<div class="bg-gray-800 p-6 rounded">
    <h1 class="text-2xl font-semibold mb-4">Configuración</h1>
    <div class="space-y-2 text-gray-300">
        <div>Base URL: <?= htmlspecialchars($config['app']['base_url']) ?></div>
        <div>Directorio de carga: <?= htmlspecialchars($config['app']['upload_dir']) ?></div>
        <div>Tamaño máximo de archivo: <?= (int)$config['security']['max_upload_size'] ?> bytes</div>
    </div>
</div>

