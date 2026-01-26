<div class="space-y-10">
    <!-- Hero Section para Dashboard -->
    <section class="relative rounded-[40px] overflow-hidden bg-[#222] h-[350px] flex items-center shadow-2xl">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=2072&auto=format&fit=crop" class="w-full h-full object-cover opacity-60">
        </div>
        <div class="relative z-20 px-12 max-w-xl text-white">
            <h1 class="text-4xl font-black leading-tight mb-4">¡Bienvenido de nuevo!</h1>
            <p class="text-slate-300 mb-8 font-medium">Aquí tienes el resumen de lo que está pasando hoy en tu sucursal.</p>
            <div class="flex gap-4">
                <a href="<?= htmlspecialchars($basePath . '/pos') ?>" class="bg-[#ffbc0d] text-slate-900 rounded-2xl px-8 py-3 font-black hover:bg-[#eab308] transition-all active:scale-95">
                    NUEVA VENTA
                </a>
            </div>
        </div>
    </section>

    <!-- Category Bar (Visual) -->
    <section class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto scrollbar-hide">
        <div class="flex justify-between items-center min-w-max gap-8 px-4">
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">🍔</div>
                <span class="text-[11px] font-bold text-center leading-tight">Hamburguesas</span>
            </div>
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">🍟</div>
                <span class="text-[11px] font-bold text-center leading-tight">Papas</span>
            </div>
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">🍗</div>
                <span class="text-[11px] font-bold text-center leading-tight">Pollo</span>
            </div>
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">🥤</div>
                <span class="text-[11px] font-bold text-center leading-tight">Bebidas</span>
            </div>
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">🍦</div>
                <span class="text-[11px] font-bold text-center leading-tight">Postres</span>
            </div>
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">☕</div>
                <span class="text-[11px] font-bold text-center leading-tight">Desayunos</span>
            </div>
            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                <div class="text-3xl transform group-hover:scale-110 transition-transform">🧒</div>
                <span class="text-[11px] font-bold text-center leading-tight">Cajita Feliz</span>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section>
        <div class="flex items-center gap-3 mb-6">
            <div class="h-8 w-1 bg-[#ffbc0d] rounded-full"></div>
            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Métricas Principales</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.08)] border border-slate-100 group hover:border-[#ffbc0d] transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-green-50 rounded-2xl text-green-600 group-hover:bg-green-100 transition-colors text-2xl">💰</div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Ventas</span>
                </div>
                <div class="text-3xl font-black text-slate-900">$<?= number_format($stats['salesToday'] ?? 0, 2) ?></div>
                <div class="mt-2 text-sm text-slate-400 font-medium italic">Ingresos del día</div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.08)] border border-slate-100 group hover:border-[#ffbc0d] transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 group-hover:bg-blue-100 transition-colors text-2xl">📝</div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pedidos</span>
                </div>
                <div class="text-3xl font-black text-slate-900"><?= (int)($stats['ordersToday'] ?? 0) ?></div>
                <div class="mt-2 text-sm text-slate-400 font-medium italic">Total transacciones</div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.08)] border border-slate-100 group hover:border-[#ffbc0d] transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-amber-50 rounded-2xl text-amber-600 group-hover:bg-amber-100 transition-colors text-2xl">🔥</div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Cocina</span>
                </div>
                <div class="text-3xl font-black text-slate-900"><?= (int)($stats['pendingOrders'] ?? 0) ?></div>
                <div class="mt-2 text-sm text-slate-400 font-medium italic">En preparación</div>
            </div>
        </div>
    </section>

    <!-- Promotional Grid -->
    <section>
        <div class="flex items-center gap-3 mb-6">
            <div class="h-8 w-1 bg-red-600 rounded-full"></div>
            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Novedades</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/3] relative group cursor-pointer">
                <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-end p-8 text-white">
                    <h3 class="text-xl font-black uppercase">Nuevas Salsas</h3>
                    <p class="text-xs mt-2 opacity-80 font-medium uppercase">McNuggets Edition</p>
                </div>
            </div>
            <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/3] relative group cursor-pointer">
                <img src="https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-end p-8 text-white">
                    <h3 class="text-xl font-black uppercase">McFlurry Oblea</h3>
                    <p class="text-xs mt-2 opacity-80 font-medium uppercase">Dulzura Local</p>
                </div>
            </div>
            <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/3] relative group cursor-pointer">
                <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-end p-8 text-white">
                    <h3 class="text-xl font-black uppercase">Big Mac Bacon</h3>
                    <p class="text-xs mt-2 opacity-80 font-medium uppercase">La leyenda</p>
                </div>
            </div>
        </div>
    </section>
</div>
