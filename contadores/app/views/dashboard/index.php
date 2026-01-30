<?php
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-gray-800 rounded-lg p-6 shadow">
        <div class="text-sm text-gray-400">Clientes activos</div>
        <div class="text-3xl font-bold mt-2"><?= (int)$clientes ?></div>
    </div>
    <div class="bg-gray-800 rounded-lg p-6 shadow">
        <div class="text-sm text-gray-400">Documentos pendientes</div>
        <div class="text-3xl font-bold mt-2"><?= (int)$docPendientes ?></div>
    </div>
    <div class="bg-gray-800 rounded-lg p-6 shadow">
        <div class="text-sm text-gray-400">Vencimientos próximos</div>
        <div class="text-3xl font-bold mt-2"><?= (int)$vencimientosProximos ?></div>
    </div>
    <div class="bg-gray-800 rounded-lg p-6 shadow">
        <div class="text-sm text-gray-400">Alertas fiscales</div>
        <div class="text-3xl font-bold mt-2"><?= (int)$alertasFiscales ?></div>
    </div>
</div>

