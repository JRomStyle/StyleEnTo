<section class="space-y-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-soft border border-slate-200/70 p-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary text-sm font-semibold">
                Catálogo actualizado
            </div>
            <h1 class="mt-4 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                Bienvenido a la juguetería
            </h1>
            <p class="mt-3 text-slate-600 leading-relaxed">
                Descubre juguetes educativos, muñecas, figuras de acción, vehículos, juegos de mesa, para bebés, tecnológicos y de exterior.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="?route=product/index" class="inline-flex items-center justify-center px-5 py-3 bg-secondary text-white rounded-xl font-semibold hover:bg-blue-600 active:bg-blue-700 transition shadow-soft">
                    Ver catálogo
                </a>
                <a href="?route=cart/index" class="inline-flex items-center justify-center px-5 py-3 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 active:bg-slate-900 transition shadow-soft">
                    Ir al carrito
                </a>
            </div>
        </div>
        <div class="rounded-2xl bg-white shadow-soft border border-slate-200/70 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-slate-900">Categorías</h2>
                <span class="text-xs text-slate-500"><?php echo (int)count($categories); ?></span>
            </div>
            <?php
            $chipColors = [
                'bg-secondary/10 text-secondary',
                'bg-accent/15 text-amber-800',
                'bg-primary/10 text-primary',
                'bg-emerald-100 text-emerald-800',
                'bg-violet-100 text-violet-800',
            ];
            $ci = 0;
            ?>
            <div class="mt-4 flex flex-wrap gap-2">
                <?php foreach ($categories as $c): ?>
                    <?php $cls = $chipColors[$ci++ % count($chipColors)]; ?>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $cls; ?>">
                        <?php echo htmlspecialchars($c['nombre']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div>
        <div class="flex items-end justify-between gap-4 mb-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Novedades</h2>
                <p class="text-sm text-slate-600">Los últimos juguetes agregados al catálogo.</p>
            </div>
            <a href="?route=product/index" class="text-sm font-semibold text-secondary hover:text-blue-700">Ver todo</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $p): ?>
                <div class="group bg-gradient-to-br from-white via-white to-accent/10 rounded-2xl border border-slate-200/70 shadow-soft overflow-hidden hover:-translate-y-1 hover:shadow-xl transition duration-300">
                    <a href="?route=product/show&id=<?php echo (int)$p['id']; ?>" class="block">
                        <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
                            <img src="<?php echo htmlspecialchars(product_image_url($p, 800, 600)); ?>" class="w-full h-full object-cover group-hover:scale-[1.05] transition duration-300" alt="<?php echo htmlspecialchars($p['nombre'] ?? ''); ?>">
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-slate-900 leading-snug"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            <p class="text-sm text-slate-500"><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></p>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-lg font-bold text-primary">$<?php echo number_format($p['precio'], 2); ?></p>
                                <span class="text-xs px-2 py-1 rounded-full bg-secondary/10 text-secondary font-semibold">Nuevo</span>
                            </div>
                        </div>
                    </a>
                    <div class="p-4 pt-0">
                        <form class="add-to-cart" method="post" action="?route=cart/add">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                            <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-accent text-white rounded-xl font-semibold hover:bg-yellow-600 active:bg-yellow-700 transition shadow-soft hover:-translate-y-0.5 active:translate-y-0">
                                Agregar al carrito
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
