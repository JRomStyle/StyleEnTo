<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
?>
<h1 class="text-2xl font-semibold mb-4">Editar cliente</h1>
<?php if ($cliente): ?>
<form action="<?= $base ?>/clientes/editar" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800 p-6 rounded">
    <input type="hidden" name="id" value="<?= (int)$cliente['id'] ?>">
    <div>
        <label class="block text-sm mb-1">Tipo</label>
        <select name="tipo" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
            <option value="natural" <?= $cliente['tipo'] === 'natural' ? 'selected' : '' ?>>Persona natural</option>
            <option value="juridico" <?= $cliente['tipo'] === 'juridico' ? 'selected' : '' ?>>Empresa</option>
        </select>
    </div>
    <div>
        <label class="block text-sm mb-1">Nombre</label>
        <input name="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm mb-1">Documento</label>
        <input name="documento" value="<?= htmlspecialchars($cliente['documento']) ?>" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm mb-1">Correo</label>
        <input name="correo" type="email" value="<?= htmlspecialchars($cliente['correo']) ?>" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Teléfono</label>
        <input name="telefono" value="<?= htmlspecialchars($cliente['telefono']) ?>" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm mb-1">Dirección</label>
        <input name="direccion" value="<?= htmlspecialchars($cliente['direccion']) ?>" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm mb-1">Régimen</label>
        <select name="regimen" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
            <option value="" <?= empty($cliente['regimen']) ? 'selected' : '' ?>>No aplica</option>
            <option value="simplificado" <?= $cliente['regimen'] === 'simplificado' ? 'selected' : '' ?>>Simplificado</option>
            <option value="responsable_iva" <?= $cliente['regimen'] === 'responsable_iva' ? 'selected' : '' ?>>Responsable de IVA</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Actualizar</button>
        <a href="<?= $base ?>/clientes" class="ml-2 px-4 py-2 rounded bg-gray-700">Cancelar</a>
    </div>
    </form>
<?php else: ?>
<div class="p-4 bg-gray-800 rounded">No encontrado</div>
<?php endif; ?>

