<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-gray-800 p-6 rounded">
        <h2 class="text-lg font-semibold mb-3">Crear usuario</h2>
        <form action="<?= $base ?>/usuarios/crear" method="post" class="space-y-3">
            <div>
                <label class="block text-sm mb-1">Nombre</label>
                <input name="nombre" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Correo</label>
                <input type="email" name="correo" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Contraseña</label>
                <input type="password" name="password" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Rol</label>
                <select name="rol" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                    <option value="asistente">Asistente</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Crear</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-gray-800 p-6 rounded">
        <h2 class="text-lg font-semibold mb-3">Usuarios</h2>
        <table class="min-w-full">
            <thead class="bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Nombre</th>
                <th class="px-4 py-2 text-left">Correo</th>
                <th class="px-4 py-2 text-left">Rol</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr class="border-t border-gray-700">
                    <td class="px-4 py-2"><?= htmlspecialchars($u['nombre']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($u['correo']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($u['rol']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($usuarios)): ?>
        <div class="text-gray-400 mt-3">Sin registros</div>
        <?php endif; ?>
    </div>
</div>

