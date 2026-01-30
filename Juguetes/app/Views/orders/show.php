<?php
$orderId = (int)($order['id'] ?? 0);
?>
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pedido #<?php echo $orderId; ?></h1>
        <p class="text-sm text-slate-600">Detalle y seguimiento de tu compra.</p>
    </div>
    <a href="?route=order/my" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Volver</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Fecha</div>
                    <div class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($order['fecha'] ?? ''); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</div>
                    <div class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($order['estado'] ?? ''); ?></div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</div>
                    <div class="mt-1 font-bold text-primary text-xl">$<?php echo number_format((float)($order['total'] ?? 0), 2); ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200/70 flex items-center justify-between">
                <div class="font-semibold text-slate-900">Productos</div>
                <div class="text-sm text-slate-600"><?php echo (int)count($items); ?> ítems</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200/70 bg-slate-50/60">
                            <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide">Producto</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide">Cantidad</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide">Precio</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <?php
                            $qty = (int)($it['cantidad'] ?? 0);
                            $price = (float)($it['precio_unitario'] ?? 0);
                            $sub = $qty * $price;
                            ?>
                            <tr class="border-b border-slate-200/70">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 flex-none">
                                            <img src="<?php echo htmlspecialchars(product_image_url($it, 200, 200)); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($it['nombre'] ?? ''); ?>">
                                        </div>
                                        <div class="font-semibold text-slate-900 truncate"><?php echo htmlspecialchars($it['nombre'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-700"><?php echo $qty; ?></td>
                                <td class="py-4 px-6 text-slate-700">$<?php echo number_format($price, 2); ?></td>
                                <td class="py-4 px-6 font-semibold text-primary text-right">$<?php echo number_format($sub, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200/70">
                <div class="font-semibold text-slate-900">Historial de estados</div>
            </div>
            <?php if (empty($history)): ?>
                <div class="p-6 text-sm text-slate-600">Sin historial.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-200/70 bg-slate-50/60">
                                <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide">Fecha</th>
                                <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide">Estado</th>
                                <th class="py-3 px-6 text-xs font-semibold text-slate-600 uppercase tracking-wide">Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $h): ?>
                                <tr class="border-b border-slate-200/70">
                                    <td class="py-4 px-6 text-slate-700"><?php echo htmlspecialchars($h['fecha'] ?? ''); ?></td>
                                    <td class="py-4 px-6 font-semibold text-slate-900"><?php echo htmlspecialchars($h['estado'] ?? ''); ?></td>
                                    <td class="py-4 px-6 text-slate-700"><?php echo htmlspecialchars($h['nota'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
