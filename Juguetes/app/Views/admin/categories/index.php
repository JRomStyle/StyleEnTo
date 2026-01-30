<div class="flex items-center justify-between mb-4 gap-3">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Categorías</h1>
        <p class="text-sm text-slate-600">Organiza el catálogo para que sea más fácil buscar.</p>
    </div>
    <a href="?route=admin/categoryCreate" class="inline-flex items-center justify-center px-4 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:opacity-90 transition duration-200 shadow-soft">
        Nueva categoría
    </a>
</div>

<div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6">
    <ul class="space-y-3">
        <?php foreach ($categories as $c): ?>
            <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl border border-slate-200/70">
                <div class="min-w-0">
                    <div class="font-semibold text-slate-900 truncate"><?php echo htmlspecialchars($c['nombre']); ?></div>
                    <?php if (!empty($c['descripcion'])): ?>
                        <div class="text-sm text-slate-600 truncate"><?php echo htmlspecialchars($c['descripcion']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-2">
                    <a href="?route=admin/productsByCategory&id=<?php echo (int)$c['id']; ?>" class="px-3 py-2 bg-secondary text-white rounded-xl font-semibold hover:opacity-90 transition duration-200">Ver productos</a>
                    <a href="?route=admin/categoryEdit&id=<?php echo (int)$c['id']; ?>" class="px-3 py-2 bg-accent text-white rounded-xl font-semibold hover:opacity-90 transition duration-200">Editar</a>
                    <form method="post" action="?route=admin/categoryDelete" onsubmit="return confirm('¿Eliminar categoría?');">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                        <button class="px-3 py-2 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition duration-200">Eliminar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
