<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
$uploadUrl = $config['app']['upload_url'];
?>
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Documentos DIAN</h1>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-gray-800 p-6 rounded">
        <h2 class="text-lg font-semibold mb-3">Subir documento</h2>
        <form action="<?= $base ?>/documentos/subir" method="post" enctype="multipart/form-data" class="space-y-3">
            <div>
                <label class="block text-sm mb-1">Cliente</label>
                <select name="cliente_id" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Tipo de documento</label>
                <select name="tipo_documento" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                    <option value="renta">Declaración de renta</option>
                    <option value="iva">IVA</option>
                    <option value="retencion">Retención en la fuente</option>
                    <option value="camara">Cámara de comercio</option>
                    <option value="estados">Estados financieros</option>
                </select>
            </div>
            <div>
                <input type="file" name="archivo" accept=".pdf,.xls,.xlsx,.doc,.docx" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Subir</button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-gray-800 p-6 rounded">
        <h2 class="text-lg font-semibold mb-3">Listado</h2>
        <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">Cliente</th>
                <th class="px-4 py-2 text-left">Tipo</th>
                <th class="px-4 py-2 text-left">Archivo</th>
                <th class="px-4 py-2 text-left">Fecha</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($documentos as $d): ?>
                <tr class="border-t border-gray-700">
                    <td class="px-4 py-2"><?= htmlspecialchars($d['cliente_nombre']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($d['tipo_documento']) ?></td>
                    <td class="px-4 py-2"><a class="text-blue-400 hover:underline" href="<?= $uploadUrl . '/' . htmlspecialchars($d['archivo']) ?>" target="_blank"><?= htmlspecialchars($d['archivo']) ?></a></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($d['fecha_subida']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php if (empty($documentos)): ?>
        <div class="text-gray-400 mt-3">Sin registros</div>
        <?php endif; ?>
    </div>
</div>

