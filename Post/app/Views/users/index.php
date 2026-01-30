<div class="space-y-8">
    <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Gestión de Usuarios</h2>
            <p class="text-slate-400 text-sm font-medium">Control de acceso y roles del personal.</p>
        </div>
        <button class="bg-[#ffbc0d] text-slate-900 rounded-2xl px-6 py-3 text-xs font-black hover:bg-[#eab308] transition-all active:scale-95 shadow-lg shadow-yellow-100 uppercase tracking-widest">
            + Nuevo Usuario
        </button>
    </div>

    <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Colaborador</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Rol</th>
                    <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($users as $user): ?>
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-xl group-hover:bg-[#ffbc0d] transition-colors duration-300">👤</div>
                                <div>
                                    <div class="font-bold text-slate-900 uppercase text-sm"><?= htmlspecialchars($user['name']) ?></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"><?= htmlspecialchars($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <?php 
                                $roleClass = $user['role_id'] == 1 ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-600';
                                $roleName = $user['role_id'] == 1 ? 'ADMINISTRADOR' : 'OPERADOR';
                            ?>
                            <span class="<?= $roleClass ?> text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                                <?= $roleName ?>
                            </span>
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
