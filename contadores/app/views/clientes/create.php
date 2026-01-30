<?php
$config = require __DIR__ . '/../../../config/config.php';
$base = $config['app']['base_url'];
?>
<h1 class="text-2xl font-semibold mb-4">Nuevo cliente</h1>
<form action="<?= $base ?>/clientes/crear" method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800 p-6 rounded">
    <div>
        <label class="block text-sm mb-1">Tipo</label>
        <select name="tipo" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
            <option value="natural">Persona natural</option>
            <option value="juridico">Empresa</option>
        </select>
    </div>
    <div>
        <label class="block text-sm mb-1">Nombre</label>
        <input name="nombre" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm mb-1">Documento</label>
        <input name="documento" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm mb-1">Correo</label>
        <input name="correo" type="email" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm mb-1">Teléfono</label>
        <input name="telefono" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm mb-1">Dirección</label>
        <input name="direccion" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm mb-1">Régimen (solo natural)</label>
        <select name="regimen" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
            <option value="">No aplica</option>
            <option value="simplificado">Simplificado</option>
            <option value="responsable_iva">Responsable de IVA</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar</button>
        <a href="<?= $base ?>/clientes" class="ml-2 px-4 py-2 rounded bg-gray-700">Cancelar</a>
    </div>
    </form>

