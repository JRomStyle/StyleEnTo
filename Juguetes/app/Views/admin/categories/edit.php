<h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-4">Editar categoría</h1>
<form class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 space-y-4" method="post" action="?route=admin/categoryEdit&id=<?php echo (int)$category['id']; ?>">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-medium text-slate-700">Nombre</label>
        <input name="nombre" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" value="<?php echo htmlspecialchars($category['nombre']); ?>">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Descripción</label>
        <textarea name="descripcion" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" rows="4"><?php echo htmlspecialchars($category['descripcion'] ?? ''); ?></textarea>
    </div>
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center justify-center px-4 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:opacity-90 transition duration-200 shadow-soft">Guardar</button>
        <a href="?route=admin/categories" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Cancelar</a>
    </div>
</form>
