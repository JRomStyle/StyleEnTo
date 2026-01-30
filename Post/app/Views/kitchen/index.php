<div class="space-y-8">
    <!-- Kitchen Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white rounded-[32px] p-8 shadow-sm border border-slate-100">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Monitor de Cocina (KDS)</h2>
            </div>
            <p class="text-slate-400 text-sm font-medium">Gestiona los pedidos en tiempo real para tu sucursal.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-slate-50 rounded-2xl px-6 py-3 text-sm font-bold text-slate-600 border border-slate-100">
                Pedidos: <span class="text-slate-900"><?= count($orders) ?></span>
            </div>
            <button id="kitchen-refresh" class="bg-[#ffbc0d] text-slate-900 rounded-2xl px-6 py-3 text-sm font-black hover:bg-[#eab308] transition-all active:scale-95 shadow-lg shadow-yellow-100 flex items-center gap-2">
                <span>🔄</span> ACTUALIZAR
            </button>
        </div>
    </div>

    <!-- Kitchen Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="kitchen-orders">
        <?php if (empty($orders)): ?>
            <div class="col-span-full py-20 flex flex-col items-center justify-center text-center bg-white rounded-[40px] border-2 border-dashed border-slate-100">
                <div class="text-6xl mb-6 opacity-20">👨‍🍳</div>
                <h3 class="text-xl font-black text-slate-900 uppercase">Cocina Despejada</h3>
                <p class="text-slate-400 font-medium">No hay pedidos pendientes por ahora.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($orders as $order): ?>
            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 flex flex-col overflow-hidden group hover:border-[#ffbc0d] transition-all duration-300">
                <!-- Ticket Header -->
                <div class="p-6 border-b border-slate-50 <?= $order['status'] === 'preparing' ? 'bg-amber-50' : 'bg-slate-50/50' ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-slate-900 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                            #<?= (int)$order['id'] ?>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <?= date('H:i', strtotime($order['created_at'])) ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl"><?= $order['order_type'] === 'delivery' ? '🛵' : ($order['order_type'] === 'takeaway' ? '🛍️' : '🍴') ?></span>
                        <h4 class="font-black text-slate-900 uppercase text-sm tracking-tight">
                            <?= $order['order_type'] === 'delivery' ? 'Para llevar / Domicilio' : 'Consumo Local' ?>
                        </h4>
                    </div>
                </div>

                <!-- Ticket Body (Items) -->
                <div class="flex-1 p-6 space-y-4">
                    <div class="space-y-3">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 bg-slate-100 rounded-lg flex items-center justify-center text-[10px] font-black text-slate-600 shrink-0">
                                    <?= (int)$item['quantity'] ?>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-slate-900 uppercase leading-tight"><?= htmlspecialchars($item['name']) ?></div>
                                    <?php if (!empty($item['notes'])): ?>
                                        <p class="text-[10px] text-red-500 font-bold mt-1 uppercase">⚠️ <?= htmlspecialchars($item['notes']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Ticket Footer (Actions) -->
                <div class="p-6 pt-0 mt-auto">
                    <div class="flex gap-2">
                        <?php if ($order['status'] === 'pending'): ?>
                            <button class="kitchen-status flex-1 bg-amber-100 text-amber-700 rounded-2xl py-3 text-xs font-black uppercase hover:bg-amber-200 transition-colors" 
                                    data-id="<?= (int)$order['id'] ?>" data-status="preparing">
                                PREPARAR
                            </button>
                        <?php endif; ?>
                        <button class="kitchen-status flex-1 bg-emerald-500 text-white rounded-2xl py-3 text-xs font-black uppercase hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-100 active:scale-95" 
                                data-id="<?= (int)$order['id'] ?>" data-status="ready">
                            FINALIZAR
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
window.KitchenCsrf = <?= json_encode($csrf ?? '') ?>;
</script>
