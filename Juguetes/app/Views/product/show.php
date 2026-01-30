<?php if ($product): ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft overflow-hidden">
        <div class="aspect-[4/3] bg-slate-100">
            <img src="<?php echo htmlspecialchars(product_image_url($product, 1200, 900)); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($product['nombre'] ?? ''); ?>">
        </div>
    </div>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6">
        <div class="flex flex-wrap items-center gap-2">
            <?php if (!empty($product['categoria'])): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-secondary/10 text-secondary text-sm font-semibold">
                    <?php echo htmlspecialchars($product['categoria']); ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($product['edad_recomendada'])): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">
                    Edad: <?php echo htmlspecialchars($product['edad_recomendada']); ?>
                </span>
            <?php endif; ?>
        </div>
        <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900"><?php echo htmlspecialchars($product['nombre']); ?></h1>
        <?php if (!empty($product['descripcion'])): ?>
            <p class="mt-3 text-slate-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($product['descripcion'] ?? '')); ?></p>
        <?php endif; ?>

        <div class="mt-6 flex items-end justify-between gap-4">
            <div>
                <div class="text-sm text-slate-500">Precio</div>
                <div class="text-3xl font-bold text-primary">$<?php echo number_format((float)$product['precio'], 2); ?></div>
            </div>
            <form class="add-to-cart" method="post" action="?route=cart/add">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
                <button class="inline-flex items-center justify-center px-5 py-3 bg-accent text-white rounded-xl font-semibold hover:bg-yellow-600 active:bg-yellow-700 transition shadow-soft">
                    Agregar al carrito
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
