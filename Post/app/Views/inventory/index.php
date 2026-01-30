<div class="flex flex-col lg:flex-row gap-8">
    <!-- Inventory List -->
    <div class="flex-1">
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Control de Inventario</h2>
                    <p class="text-slate-400 text-sm font-medium">Insumos y materias primas en stock.</p>
                </div>
                <div class="flex gap-2">
                    <span class="bg-slate-50 text-slate-400 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Total: <?= count($items) ?></span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Ingrediente</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Stock Actual</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($items as $item): ?>
                            <?php 
                                $isLow = (float)$item['quantity'] <= (float)$item['min_stock'];
                                $isVeryLow = (float)$item['quantity'] <= ((float)$item['min_stock'] * 0.5);
                            ?>
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-slate-900 uppercase text-sm"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?= htmlspecialchars($item['unit']) ?></div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-lg font-black <?= $isLow ? 'text-red-500' : 'text-slate-900' ?>">
                                        <?= number_format((float)$item['quantity'], 2) ?>
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mín: <?= number_format((float)$item['min_stock'], 2) ?></div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <?php if ($isVeryLow): ?>
                                        <span class="bg-red-50 text-red-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Crítico</span>
                                    <?php elseif ($isLow): ?>
                                        <span class="bg-amber-50 text-amber-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Bajo</span>
                                    <?php else: ?>
                                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Óptimo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Adjustment Form -->
    <div class="w-full lg:w-96 shrink-0">
        <div class="bg-white rounded-[32px] shadow-2xl border border-slate-100 overflow-hidden sticky top-8">
            <div class="p-8 border-b border-slate-50 bg-slate-900">
                <h3 class="text-xl font-black text-white uppercase tracking-tight">Ajustar Stock</h3>
                <p class="text-slate-400 text-xs font-medium mt-1 uppercase">Registro de movimientos</p>
            </div>
            
            <form id="inventory-adjust-form" class="p-8 space-y-6">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ingrediente</label>
                    <select name="ingredient_id" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#ffbc0d] transition-all">
                        <?php foreach ($items as $item): ?>
                            <option value="<?= (int)$item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cantidad (+ o -)</label>
                    <input name="quantity" type="number" step="0.01" placeholder="0.00" required
                           class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#ffbc0d] transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Motivo del ajuste</label>
                    <input name="reason" type="text" placeholder="Ej: Compra, Merma..." required
                           class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#ffbc0d] transition-all">
                </div>

                <button class="w-full bg-[#ffbc0d] text-slate-900 font-black rounded-2xl py-5 shadow-lg shadow-yellow-100 hover:bg-[#eab308] hover:shadow-yellow-200 transition-all active:scale-[0.98] uppercase tracking-widest text-xs" type="submit">
                    Aplicar Ajuste
                </button>
            </form>
            
            <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Los cambios se verán reflejados al instante</p>
            </div>
        </div>
    </div>
</div>
