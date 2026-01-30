<h1 class="text-2xl font-bold text-secondary mb-4">Productos de categoría: <?php echo htmlspecialchars($category['nombre']); ?></h1>
<div class="mb-4 flex items-center gap-3">
    <label class="text-sm text-gray-600">Cambiar categoría de productos</label>
    <a href="?route=admin/products" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 transition duration-200">Ver todos</a>
 </div>
<?php if (!$products): ?>
    <div class="bg-white rounded-xl shadow p-6">No hay productos en esta categoría.</div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($products as $p): ?>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition duration-200">
                <img src="<?php echo htmlspecialchars(product_image_url($p, 800, 600)); ?>" class="w-full h-40 object-cover rounded" alt="<?php echo htmlspecialchars($p['nombre'] ?? ''); ?>">
                <div class="mt-2 font-semibold"><?php echo htmlspecialchars($p['nombre']); ?></div>
                <div class="text-sm text-gray-600"><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></div>
                <div class="mt-1 text-primary font-bold">$<?php echo number_format($p['precio'], 2); ?></div>
                <div class="mt-2 flex items-center gap-2">
                    <a href="?route=admin/productEdit&id=<?php echo (int)$p['id']; ?>" class="px-3 py-1 bg-accent text-white rounded hover:opacity-90 transition duration-200">Editar</a>
                    <form method="post" action="?route=admin/productMoveCategory" class="flex items-center gap-2">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                        <select name="categoria_id" class="border rounded px-2 py-1 focus:ring-2 focus:ring-secondary focus:border-transparent">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)$p['categoria_id'] === (int)$c['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="px-3 py-1 bg-secondary text-white rounded hover:opacity-90 transition duration-200">Mover</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
