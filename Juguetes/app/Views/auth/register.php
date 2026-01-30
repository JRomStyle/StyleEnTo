<div class="max-w-md mx-auto bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Crear cuenta</h1>
    <p class="mt-1 text-sm text-slate-600">Te toma menos de un minuto.</p>
    <?php if (!empty($error)): ?>
        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form class="mt-6 space-y-4" method="post" action="?route=auth/doRegister">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="nombre" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" placeholder="Tu nombre">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" required class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" placeholder="tu@email.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Contraseña</label>
            <input type="password" name="password" required minlength="6" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" placeholder="Mínimo 6 caracteres">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Confirmar contraseña</label>
            <input type="password" name="confirm" required minlength="6" class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40" placeholder="Repite la contraseña">
        </div>
        <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:bg-blue-600 active:bg-blue-700 transition shadow-soft">
            Crear cuenta
        </button>
    </form>
    <p class="mt-4 text-sm text-slate-600">
        ¿Ya tienes cuenta?
        <a href="?route=auth/login" class="font-semibold text-secondary hover:text-blue-700">Entrar</a>
    </p>
 </div>
