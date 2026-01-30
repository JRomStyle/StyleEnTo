<h1 class="text-2xl font-bold text-secondary mb-4">Productos</h1>
<div class="mb-4 flex items-center gap-3">
    <a href="?route=admin/productCreate" class="px-4 py-2 bg-secondary text-white rounded hover:opacity-90 transition duration-200">Nuevo producto</a>
    <form method="get" action="" class="flex items-center gap-2">
        <input type="hidden" name="route" value="admin/productsByCategory">
        <select name="id" class="border rounded px-2 py-1 focus:ring-2 focus:ring-secondary focus:border-transparent">
            <option value="">Filtrar por categoría</option>
            <?php foreach (\App\Models\Category::all() as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition duration-200">Ir</button>
    </form>
 </div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($products as $p): ?>
        <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition duration-200">
            <img src="<?php echo htmlspecialchars(product_image_url($p, 800, 600)); ?>" class="w-full h-40 object-cover rounded" alt="<?php echo htmlspecialchars($p['nombre'] ?? ''); ?>">
            <div class="mt-2 font-semibold"><?php echo htmlspecialchars($p['nombre']); ?></div>
            <div class="text-sm text-gray-600"><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></div>
            <div class="mt-1 text-primary font-bold">$<?php echo number_format($p['precio'], 2); ?></div>
            <div class="mt-2">
                <a href="?route=admin/productEdit&id=<?php echo (int)$p['id']; ?>" class="px-3 py-1 bg-accent text-white rounded hover:opacity-90 transition duration-200">Editar</a>
                <form method="post" action="?route=admin/productDelete" class="inline-block ml-2" onsubmit="return confirm('¿Eliminar producto?');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                    <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">Eliminar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
