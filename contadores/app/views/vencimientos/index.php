<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-gray-800 p-6 rounded">
        <h2 class="text-lg font-semibold mb-3">Registrar vencimiento</h2>
        <form action="<?= $base ?>/vencimientos/crear" method="post" class="space-y-3">
            <div>
                <label class="block text-sm mb-1">Cliente</label>
                <select name="cliente_id" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Descripción</label>
                <input name="descripcion" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Fecha límite</label>
                <input type="date" name="fecha_limite" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Estado</label>
                <select name="estado" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-gray-800 p-6 rounded">
        <h2 class="text-lg font-semibold mb-3">Vencimientos</h2>
        <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Cliente</th>
                <th class="px-4 py-2 text-left">Descripción</th>
                <th class="px-4 py-2 text-left">Fecha límite</th>
                <th class="px-4 py-2 text-left">Estado</th>
                <th class="px-4 py-2 text-left">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($vencimientos as $v): ?>
                <?php $dias = (new DateTime($v['fecha_limite']))->diff(new DateTime())->invert ? (new DateTime())->diff(new DateTime($v['fecha_limite']))->days : 0; ?>
                <tr class="border-t border-gray-700 <?= ($v['estado'] === 'pendiente' && $dias <= 7) ? 'bg-red-900/30' : '' ?>" data-days="<?= (int)$dias ?>">
                    <td class="px-4 py-2"><?= htmlspecialchars($v['cliente_nombre']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($v['descripcion']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($v['fecha_limite']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($v['estado']) ?></td>
                    <td class="px-4 py-2">
                        <?php if ($v['estado'] === 'pendiente'): ?>
                            <a href="<?= $base ?>/vencimientos/pagar?id=<?= (int)$v['id'] ?>" class="px-3 py-1 rounded bg-green-600 hover:bg-green-700 text-white">Marcar pagado</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php if (empty($vencimientos)): ?>
        <div class="text-gray-400 mt-3">Sin registros</div>
        <?php endif; ?>
    </div>
</div>

