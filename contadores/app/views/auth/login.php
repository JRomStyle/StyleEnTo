<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
?>
<div class="bg-gray-800 rounded-lg shadow p-6">
    <h1 class="text-2xl font-semibold mb-4">Ingresar</h1>
    <?php if (!empty($error)): ?>
        <div class="mb-4 bg-red-600 text-white px-3 py-2 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form action="<?= $base ?>/login" method="post" class="space-y-4">
        <div>
            <label class="block text-sm mb-1">Correo</label>
            <input type="email" name="correo" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
        </div>
        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Entrar</button>
    </form>
</div>

