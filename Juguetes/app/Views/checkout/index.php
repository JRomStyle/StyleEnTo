<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Confirmar compra</h1>
        <p class="text-sm text-slate-600">Revisa tu pedido antes de finalizar.</p>
    </div>
    <a href="?route=cart/index" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Volver</a>
</div>
<?php if (!$items): ?>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 text-slate-700">No hay productos en el carrito.</div>
<?php else: ?>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/70 flex items-center justify-between">
            <div class="font-semibold text-slate-900">Resumen</div>
            <div class="text-sm text-slate-600"><?php echo (int)count($items); ?> ítems</div>
        </div>
        <div class="p-6 space-y-3">
            <?php foreach ($items as $it): ?>
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-900 truncate"><?php echo htmlspecialchars($it['nombre']); ?></div>
                        <div class="text-sm text-slate-600">Cantidad: <?php echo (int)$it['cantidad']; ?></div>
                    </div>
                    <div class="font-semibold text-primary">$<?php echo number_format((float)$it['subtotal'], 2); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="px-6 py-5 border-t border-slate-200/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-slate-700">
                Total:
                <span class="ml-2 font-bold text-primary text-2xl">$<?php echo number_format((float)$total, 2); ?></span>
            </div>
            <form method="post" action="?route=order/checkout">
            <?php echo csrf_field(); ?>
            <button class="inline-flex items-center justify-center px-5 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:bg-blue-600 active:bg-blue-700 transition shadow-soft">
                Confirmar compra
            </button>
        </form>
        </div>
    </div>
<?php endif; ?>
