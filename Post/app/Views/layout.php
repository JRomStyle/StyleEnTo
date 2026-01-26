<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf ?? '') ?>">
    <title>POS Empresarial</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f8f9fa] text-slate-900 min-h-screen font-sans">
    <!-- Sidebar Menu -->
    <div id="sidebar" class="fixed inset-0 z-[60] invisible">
        <div id="sidebar-overlay" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="sidebar-content" class="absolute inset-y-0 left-0 w-80 bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 p-8 flex flex-col">
            <div class="flex items-center justify-between mb-10">
                <svg class="h-10 w-10 text-[#ffbc0d]" viewBox="0 0 40 40" fill="currentColor">
                    <path d="M20 10c-3.3 0-6 2.7-6 6v14h4V16c0-1.1.9-2 2-2s2 .9 2 2v14h4V16c0-3.3-2.7-6-6-6zM10 10c-3.3 0-6 2.7-6 6v14h4V16c0-1.1.9-2 2-2s2 .9 2 2v14h4V16c0-3.3-2.7-6-6-6z"/>
                </svg>
                <button id="close-sidebar" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <nav class="flex-1 space-y-1 overflow-y-auto pr-2 scrollbar-hide">
                <?php if ($authUser): ?>
                    <div class="mb-8">
                        <div class="text-[10px] font-black uppercase text-slate-400 mb-4 px-4 tracking-widest">Sistema de Gestión</div>
                        <div class="grid grid-cols-1 gap-1">
                            <a href="<?= htmlspecialchars($basePath . '/dashboard') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">📊</span>
                                <span class="text-sm">Dashboard</span>
                            </a>
                            <?php if (\App\Core\Auth::can('pos.view')): ?>
                                <a href="<?= htmlspecialchars($basePath . '/pos') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                    <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">💰</span>
                                    <span class="text-sm">Ventas</span>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::can('kitchen.view')): ?>
                                <a href="<?= htmlspecialchars($basePath . '/kitchen') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                    <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">👨‍🍳</span>
                                    <span class="text-sm">Cocina</span>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::can('inventory.view')): ?>
                                <a href="<?= htmlspecialchars($basePath . '/inventory') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                    <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">📦</span>
                                    <span class="text-sm">Inventario</span>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::can('reports.view')): ?>
                                <a href="<?= htmlspecialchars($basePath . '/reports') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                    <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">📈</span>
                                    <span class="text-sm">Reportes</span>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::can('branches.view')): ?>
                                <a href="<?= htmlspecialchars($basePath . '/branches') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                    <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">🏢</span>
                                    <span class="text-sm">Sucursales</span>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::can('users.view')): ?>
                                <a href="<?= htmlspecialchars($basePath . '/users') ?>" class="flex items-center gap-4 py-4 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                                    <span class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-lg group-hover:bg-[#ffbc0d] transition-colors">👥</span>
                                    <span class="text-sm">Usuarios</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-8">
                    <div class="text-[10px] font-black uppercase text-slate-400 mb-4 px-4 tracking-widest">McNav</div>
                    <div class="grid grid-cols-1 gap-1">
                        <a href="#" class="flex items-center gap-4 py-3 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                            <span class="text-xl group-hover:scale-110 transition-transform">🍔</span>
                            <span class="text-sm">Productos</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 py-3 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                            <span class="text-xl group-hover:scale-110 transition-transform">👨‍👩‍👧‍👦</span>
                            <span class="text-sm">En familia</span>
                        </a>
                        <a href="#" class="flex items-center gap-4 py-3 px-4 rounded-2xl font-bold hover:bg-slate-50 transition-all group">
                            <span class="text-xl group-hover:scale-110 transition-transform">🌱</span>
                            <span class="text-sm">Receta del futuro</span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="mt-auto pt-8 border-t border-slate-100">
                <div class="bg-slate-50 rounded-[24px] p-6">
                    <div class="text-xs font-black text-slate-900 mb-4 uppercase tracking-wider">Descarga nuestra App</div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-white border border-slate-100 shadow-sm rounded-xl py-2 px-3 flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-tighter">
                            <span>🍎</span> APP STORE
                        </button>
                        <button class="flex-1 bg-white border border-slate-100 shadow-sm rounded-xl py-2 px-3 flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-tighter">
                            <span>🤖</span> PLAY STORE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="min-h-screen flex flex-col">
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <button id="open-sidebar" class="text-slate-900 hover:text-[#ffbc0d] transition-colors p-2 -ml-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16"></path></svg>
                    </button>
                    <a href="<?= htmlspecialchars($basePath . '/') ?>" class="flex items-center transform hover:scale-105 transition-transform">
                        <svg class="h-10 w-10 text-[#ffbc0d]" viewBox="0 0 40 40" fill="currentColor">
                            <path d="M20 10c-3.3 0-6 2.7-6 6v14h4V16c0-1.1.9-2 2-2s2 .9 2 2v14h4V16c0-3.3-2.7-6-6-6zM10 10c-3.3 0-6 2.7-6 6v14h4V16c0-1.1.9-2 2-2s2 .9 2 2v14h4V16c0-3.3-2.7-6-6-6z"/>
                        </svg>
                    </a>
                </div>
                
                <nav class="hidden lg:flex items-center gap-10">
                    <?php if ($authUser): ?>
                        <a class="text-sm font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-all" href="<?= htmlspecialchars($basePath . '/dashboard') ?>">Dashboard</a>
                        <?php if (\App\Core\Auth::can('pos.view')): ?>
                            <a class="text-sm font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-all" href="<?= htmlspecialchars($basePath . '/pos') ?>">Punto de Venta</a>
                        <?php endif; ?>
                        <?php if (\App\Core\Auth::can('kitchen.view')): ?>
                            <a class="text-sm font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-all" href="<?= htmlspecialchars($basePath . '/kitchen') ?>">Cocina</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a class="text-sm font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-all" href="#">Productos</a>
                        <a class="text-sm font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-all" href="#">Locales</a>
                        <a class="text-sm font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-all" href="#">Novedades</a>
                    <?php endif; ?>
                </nav>

                <div class="flex items-center gap-4">
                    <?php if ($authUser): ?>
                        <div class="flex items-center gap-3 pr-4 border-r border-slate-100 hidden sm:flex">
                            <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-xs">👤</div>
                            <span class="text-xs font-black text-slate-900 uppercase tracking-widest"><?= htmlspecialchars($authUser['name']) ?></span>
                        </div>
                        <form method="POST" action="<?= htmlspecialchars($basePath . '/logout') ?>">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                            <button class="bg-slate-900 text-white rounded-2xl px-6 py-2.5 text-[10px] font-black hover:bg-slate-800 transition-all active:scale-95 uppercase tracking-widest" type="submit">Salir</button>
                        </form>
                    <?php else: ?>
                        <a class="bg-[#ffbc0d] text-slate-900 rounded-2xl px-8 py-3 text-[10px] font-black hover:bg-[#eab308] transition-all active:scale-95 shadow-lg shadow-yellow-100 uppercase tracking-widest" href="<?= htmlspecialchars($basePath . '/login') ?>">Entrar</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <?php if (!$authUser): ?>
            <!-- Hero Section -->
            <section class="relative h-[500px] flex items-center overflow-hidden bg-[#222]">
                <div class="absolute inset-0">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent z-10"></div>
                    <!-- Placeholder for the burger holding image -->
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=2072&auto=format&fit=crop')] bg-cover bg-center opacity-60"></div>
                </div>
                <div class="relative z-20 max-w-7xl mx-auto px-4 w-full">
                    <div class="max-w-xl text-white">
                        <h1 class="text-5xl md:text-6xl font-black leading-tight">Cómo quieres hoy tu pedido</h1>
                        <div class="mt-10 flex gap-4">
                            <button class="bg-white text-slate-900 rounded-lg px-8 py-3 font-bold flex items-center gap-3 hover:bg-slate-100 transition-colors">
                                <span class="text-red-600">📍</span> Pickup
                            </button>
                            <button class="bg-white text-slate-900 rounded-lg px-8 py-3 font-bold flex items-center gap-3 hover:bg-slate-100 transition-colors">
                                <span class="text-yellow-500">🛵</span> McDelivery
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Category Bar -->
            <section class="bg-white border-b border-slate-100 overflow-x-auto scrollbar-hide">
                <div class="max-w-7xl mx-auto px-4 flex justify-between items-center py-6 min-w-max gap-8">
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🍔</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Hamburguesas</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🍟</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Papas y Acompañamientos</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🍗</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Pollo</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🥤</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Bebidas</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🍦</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Postres</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">☕</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Desayunos</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🥗</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Salsas</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group cursor-pointer">
                        <div class="text-3xl transform group-hover:scale-110 transition-transform">🧒</div>
                        <span class="text-[11px] font-bold text-center leading-tight max-w-[80px]">Cajita Feliz</span>
                    </div>
                </div>
            </section>

            <!-- Promotional Grid -->
            <section class="max-w-7xl mx-auto px-4 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/3] relative group">
                        <img src="https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-8 text-white">
                            <h3 class="text-2xl font-black">PRUÉBALO</h3>
                            <p class="text-sm mt-2 opacity-90">Es típico que te encante con trozos de Oblea.</p>
                        </div>
                    </div>
                    <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/3] relative group">
                        <img src="https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-8 text-white">
                            <h3 class="text-2xl font-black uppercase">Lilgendarios</h3>
                            <p class="text-sm mt-2 opacity-90">Un McDonald's miniatura para jugar en grande.</p>
                        </div>
                    </div>
                    <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/3] relative group">
                        <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-8 text-white">
                            <h3 class="text-2xl font-black uppercase">Big Mac Bacon</h3>
                            <p class="text-sm mt-2 opacity-90">La leyenda, ahora con el toque crujiente del tocino.</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <main class="max-w-7xl mx-auto px-4 py-12">
            <?= $content ?>
        </main>
    </div>

    <script>
        window.BasePath = <?= json_encode($basePath ?? '') ?>;
        
        // Sidebar logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const content = document.getElementById('sidebar-content');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        function toggleSidebar(show) {
            if (show) {
                sidebar.classList.remove('invisible');
                setTimeout(() => {
                    overlay.classList.replace('opacity-0', 'opacity-100');
                    content.classList.replace('-translate-x-full', 'translate-x-0');
                }, 10);
            } else {
                overlay.classList.replace('opacity-100', 'opacity-0');
                content.classList.replace('translate-x-0', '-translate-x-full');
                setTimeout(() => {
                    sidebar.classList.add('invisible');
                }, 300);
            }
        }

        openBtn?.addEventListener('click', () => toggleSidebar(true));
        closeBtn?.addEventListener('click', () => toggleSidebar(false));
        overlay?.addEventListener('click', () => toggleSidebar(false));
    </script>
    <script src="<?= htmlspecialchars($basePath . '/assets/app.js') ?>"></script>
</body>
</html>
