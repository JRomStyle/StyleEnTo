<div class="space-y-8">
    <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Gestión de Sucursales</h2>
            <p class="text-slate-400 text-sm font-medium">Administra las ubicaciones físicas de tu negocio.</p>
        </div>
        <button class="bg-[#ffbc0d] text-slate-900 rounded-2xl px-6 py-3 text-xs font-black hover:bg-[#eab308] transition-all active:scale-95 shadow-lg shadow-yellow-100 uppercase tracking-widest">
            + Nueva Sucursal
        </button>
    </div>

    <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sucursal</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Ubicación</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($branches as $branch): ?>
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-xl group-hover:bg-[#ffbc0d] transition-colors duration-300">🏢</div>
                                <div>
                                    <div class="font-bold text-slate-900 uppercase text-sm"><?= htmlspecialchars($branch['name']) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ID: #BR-<?= str_pad((string)$branch['id'], 3, '0', STR_PAD_LEFT) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-sm font-medium text-slate-600"><?= htmlspecialchars($branch['address']) ?></div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?= htmlspecialchars($branch['city']) ?></div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <button class="text-slate-400 hover:text-slate-900 font-bold text-xs uppercase tracking-widest transition-colors">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
