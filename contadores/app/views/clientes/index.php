<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
$isAdmin = Auth::isAdmin();
?>
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Clientes</h1>
    <a href="<?= $base ?>/clientes/crear" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Nuevo cliente</a>
    </div>
<div class="bg-gray-800 rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Tipo</th>
                <th class="px-4 py-2 text-left">Documento</th>
                <th class="px-4 py-2 text-left">Correo</th>
                <th class="px-4 py-2 text-left">Teléfono</th>
                <th class="px-4 py-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $c): ?>
            <tr class="border-t border-gray-700">
                <td class="px-4 py-2"><?= htmlspecialchars($c['nombre']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($c['tipo']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($c['documento']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($c['correo']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($c['telefono']) ?></td>
                <td class="px-4 py-2 space-x-2">
                    <a href="<?= $base ?>/clientes/editar?id=<?= (int)$c['id'] ?>" class="px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Editar</a>
                    <?php if ($isAdmin): ?>
                    <a href="<?= $base ?>/clientes/eliminar?id=<?= (int)$c['id'] ?>" class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($clientes)): ?>
    <div class="p-4 text-gray-400">Sin registros</div>
    <?php endif; ?>
</div>

