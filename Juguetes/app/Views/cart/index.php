<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Carrito</h1>
    <a href="?route=product/index" class="text-sm font-semibold text-secondary hover:text-blue-700">Seguir comprando</a>
</div>
<?php if (!$items): ?>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 text-slate-700">Tu carrito está vacío.</div>
<?php else: ?>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6">
        <div class="space-y-4">
            <?php foreach ($items as $it): ?>
                <div class="flex items-center gap-4 border-b border-slate-200/70 pb-4">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100">
                        <img src="<?php echo htmlspecialchars(product_image_url($it, 160, 160)); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($it['nombre'] ?? ''); ?>">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-slate-900 truncate"><?php echo htmlspecialchars($it['nombre']); ?></div>
                        <div class="text-sm text-slate-600">$<?php echo number_format($it['precio'], 2); ?></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" min="0" class="qty-input border border-slate-200 rounded-xl px-3 py-2 w-24 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" value="<?php echo (int)$it['cantidad']; ?>" data-id="<?php echo (int)$it['id']; ?>">
                        <form class="remove-item" method="post" action="?route=cart/update">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$it['id']; ?>">
                            <input type="hidden" name="qty" value="0">
                            <input type="hidden" name="redirect" value="1">
                            <button class="px-3 py-2 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 active:bg-slate-900 transition">Eliminar</button>
                        </form>
                    </div>
                    <div class="font-semibold text-primary w-28 text-right">$<?php echo number_format($it['subtotal'], 2); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-lg text-slate-700">
                Total: <span class="font-bold text-primary text-2xl">$<?php echo number_format($total, 2); ?></span>
            </div>
            <div class="flex items-center gap-2">
                <form method="post" action="?route=cart/clear">
                    <?php echo csrf_field(); ?>
                    <button class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Vaciar</button>
                </form>
                <a href="?route=order/checkout" class="px-5 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:bg-blue-600 active:bg-blue-700 transition shadow-soft">Comprar</a>
            </div>
        </div>
    </div>
<?php endif; ?>
