<h1 class="text-2xl font-bold text-secondary mb-4">Panel de administración</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="?route=admin/products" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
        <div class="font-semibold">Productos</div>
        <div class="text-sm text-gray-600">Gestiona el catálogo</div>
    </a>
    <a href="?route=admin/categories" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
        <div class="font-semibold">Categorías</div>
        <div class="text-sm text-gray-600">Gestiona categorías</div>
    </a>
    <a href="?route=admin/orders" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">
        <div class="font-semibold">Pedidos</div>
        <div class="text-sm text-gray-600">Gestiona y da seguimiento</div>
    </a>
</div>

<div class="mt-6 bg-white rounded-xl shadow p-6">
    <div class="font-semibold mb-2">Pedidos recientes</div>
    <div class="space-y-2">
        <?php foreach ($orders as $o): ?>
            <a class="flex items-center justify-between hover:text-secondary" href="?route=admin/orderShow&id=<?php echo (int)$o['id']; ?>">
                <div>#<?php echo (int)$o['id']; ?> - <?php echo htmlspecialchars($o['cliente'] ?? ''); ?></div>
                <div class="text-primary">$<?php echo number_format($o['total'], 2); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
