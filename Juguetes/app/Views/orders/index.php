<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Mis pedidos</h1>
    <a href="?route=product/index" class="text-sm font-semibold text-secondary hover:text-blue-700">Ir al catálogo</a>
</div>

<?php if (empty($orders)): ?>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 text-slate-700">
        Aún no tienes pedidos.
    </div>
<?php else: ?>
    <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-200/70 bg-slate-50/60">
                    <th class="py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">ID</th>
                    <th class="py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">Fecha</th>
                    <th class="py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">Total</th>
                    <th class="py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">Estado</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr class="border-b border-slate-200/70 hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-semibold text-slate-900">#<?php echo (int)$o['id']; ?></td>
                        <td class="py-3 px-4 text-slate-700"><?php echo htmlspecialchars($o['fecha'] ?? ''); ?></td>
                        <td class="py-3 px-4 font-semibold text-primary">$<?php echo number_format((float)($o['total'] ?? 0), 2); ?></td>
                        <td class="py-3 px-4 text-slate-700"><?php echo htmlspecialchars($o['estado'] ?? ''); ?></td>
                        <td class="py-3 px-4 text-right">
                            <a class="inline-flex items-center justify-center px-3 py-2 bg-accent text-white rounded-xl font-semibold hover:bg-yellow-600 active:bg-yellow-700 transition shadow-soft" href="?route=order/show&id=<?php echo (int)$o['id']; ?>">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
