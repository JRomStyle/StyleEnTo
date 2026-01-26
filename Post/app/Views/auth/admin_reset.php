<div class="max-w-md mx-auto bg-white rounded-3xl p-10 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-slate-100">
    <div class="text-center mb-8">
        <svg class="h-16 w-16 text-[#ffbc0d] mx-auto mb-4" viewBox="0 0 40 40" fill="currentColor">
            <path d="M20 10c-3.3 0-6 2.7-6 6v14h4V16c0-1.1.9-2 2-2s2 .9 2 2v14h4V16c0-3.3-2.7-6-6-6zM10 10c-3.3 0-6 2.7-6 6v14h4V16c0-1.1.9-2 2-2s2 .9 2 2v14h4V16c0-3.3-2.7-6-6-6z"/>
        </svg>
        <h1 class="text-3xl font-black text-slate-900">Seguridad</h1>
        <p class="text-slate-500 mt-2">Restablecer acceso administrativo</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl mb-6 text-sm font-medium flex items-center gap-2">
            <span>⚠️</span> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($basePath . '/admin-reset') ?>" class="space-y-5">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 ml-1">Nueva contraseña</label>
            <input name="password" type="password" required placeholder="••••••••"
                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#ffbc0d] transition-all outline-none text-slate-700 placeholder:text-slate-300">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 ml-1">Confirmar contraseña</label>
            <input name="password_confirm" type="password" required placeholder="••••••••"
                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#ffbc0d] transition-all outline-none text-slate-700 placeholder:text-slate-300">
        </div>
        <button class="w-full bg-[#ffbc0d] text-slate-900 font-black rounded-2xl py-4 hover:bg-[#eab308] shadow-lg shadow-yellow-200 transition-all active:scale-[0.98]">
            ACTUALIZAR
        </button>
    </form>

    <div class="mt-10 pt-8 border-t border-slate-50 text-center">
        <p class="text-sm text-slate-500">
            Volver a 
            <a class="text-[#ffbc0d] font-black hover:underline" href="<?= htmlspecialchars($basePath . '/login') ?>">INGRESAR</a>
        </p>
    </div>
</div>
