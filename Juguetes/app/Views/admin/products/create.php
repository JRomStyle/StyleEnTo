<h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-4">Nuevo producto</h1>
<?php if (!empty($error)): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<form class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 space-y-4" method="post" action="?route=admin/productCreate" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-medium text-slate-700">Nombre</label>
        <input name="nombre" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Descripción</label>
        <textarea name="descripcion" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" rows="4"></textarea>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Precio</label>
            <input type="number" step="0.01" name="precio" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Stock</label>
            <input type="number" name="stock" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Edad recomendada</label>
            <input name="edad_recomendada" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700">Edad mínima</label>
        <input type="number" min="0" name="edad_min" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700">Edad máxima</label>
        <input type="number" min="0" name="edad_max" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
    </div>
</div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Categoría</label>
            <select name="categoria_id" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Género</label>
            <select name="genero" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="unisex">Unisex</option>
                <option value="niño">Niño</option>
                <option value="niña">Niña</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Estado</label>
            <select name="estado" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Imagen</label>
            <input type="file" name="imagen" accept="image/*" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center justify-center px-4 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:opacity-90 transition duration-200 shadow-soft">Guardar</button>
        <a href="?route=admin/products" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Cancelar</a>
    </div>
</form>
