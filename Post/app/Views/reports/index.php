<div class="space-y-10">
    <!-- Reports Header -->
    <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Análisis de Operaciones</h2>
                <p class="text-slate-400 text-sm font-medium">Información detallada del rendimiento del negocio.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-slate-900 text-white rounded-2xl px-6 py-3 text-xs font-black hover:bg-slate-800 transition-all active:scale-95 flex items-center gap-2">
                    <span>🖨️</span> IMPRIMIR REPORTE
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Products Card -->
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#ffbc0d] rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-yellow-100">🏆</div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Productos Estrella</h3>
                </div>
            </div>
            <div class="p-8 flex-1 space-y-8">
                <?php 
                $maxQty = !empty($reports['topProducts']) ? max(array_column($reports['topProducts'], 'qty')) : 1;
                foreach ($reports['topProducts'] as $row): 
                    $percentage = ($row['qty'] / $maxQty) * 100;
                ?>
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest block mb-1">Producto</span>
                                <span class="font-bold text-slate-900 uppercase text-sm"><?= htmlspecialchars($row['name']) ?></span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-slate-900"><?= (int)$row['qty'] ?></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Unidades</span>
                            </div>
                        </div>
                        <div class="h-3 w-full bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-[#ffbc0d] rounded-full transition-all duration-1000" style="width: <?= $percentage ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($reports['topProducts'])): ?>
                    <div class="py-12 text-center">
                        <p class="text-slate-400 font-medium">No hay datos suficientes aún.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sales by Branch Card -->
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500 rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-emerald-100 text-white">🏢</div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Ventas por Sucursal</h3>
                </div>
            </div>
            <div class="p-0 flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/30">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">Sucursal</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 text-right">Total Facturado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($reports['salesByBranch'] as $row): ?>
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-900 uppercase text-sm"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sucursal Activa</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="text-lg font-black text-emerald-600">$<?= number_format((float)$row['total'], 2) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Ingresos acumulados</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($reports['salesByBranch'])): ?>
                            <tr>
                                <td colspan="2" class="px-8 py-12 text-center text-slate-400 font-medium">No hay registros de ventas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-8 mt-auto border-t border-slate-50 bg-slate-50/30">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Rendimiento General</span>
                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">+12% vs mes anterior</span>
                </div>
            </div>
        </div>
    </div>
</div>
