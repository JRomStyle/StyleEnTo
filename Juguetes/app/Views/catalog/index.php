<section class="space-y-6">
    <div class="bg-gradient-to-r from-secondary/5 via-white to-accent/10 border border-slate-200/70 rounded-2xl shadow-soft p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input id="searchInput" type="text" placeholder="Buscar juguetes..." value="<?php echo htmlspecialchars($q ?? ''); ?>" class="w-full sm:w-80 border border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                </div>
                <span class="text-sm text-slate-500">Búsqueda en tiempo real</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:flex lg:flex-wrap items-center gap-3">
                <select id="categoryFilter" class="border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)($cat ?? 0) === (int)$c['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="genderFilter" class="border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="">Todos los géneros</option>
                <option value="unisex" <?php echo (($gender ?? '') === 'unisex') ? 'selected' : ''; ?>>Unisex</option>
                <option value="niño" <?php echo (($gender ?? '') === 'niño') ? 'selected' : ''; ?>>Niño</option>
                <option value="niña" <?php echo (($gender ?? '') === 'niña') ? 'selected' : ''; ?>>Niña</option>
            </select>
            <select id="ageFilter" class="border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="">Todas las edades</option>
                <option value="0-2" <?php echo (($age ?? '') === '0-2') ? 'selected' : ''; ?>>0-2</option>
                <option value="3-5" <?php echo (($age ?? '') === '3-5') ? 'selected' : ''; ?>>3-5</option>
                <option value="6-8" <?php echo (($age ?? '') === '6-8') ? 'selected' : ''; ?>>6-8</option>
                <option value="9-12" <?php echo (($age ?? '') === '9-12') ? 'selected' : ''; ?>>9-12</option>
                <option value="13+" <?php echo (($age ?? '') === '13+') ? 'selected' : ''; ?>>13+</option>
            </select>
            <select id="priceFilter" class="border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="">Todos los precios</option>
                <option value="0-20" <?php echo (($price ?? '') === '0-20') ? 'selected' : ''; ?>>Hasta $20</option>
                <option value="20-50" <?php echo (($price ?? '') === '20-50') ? 'selected' : ''; ?>>$20-$50</option>
                <option value="50-100" <?php echo (($price ?? '') === '50-100') ? 'selected' : ''; ?>>$50-$100</option>
                <option value="100+" <?php echo (($price ?? '') === '100+') ? 'selected' : ''; ?>>Más de $100</option>
            </select>
            <select id="sortSelect" class="border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-secondary/30 focus:border-secondary/40">
                <option value="recent" <?php echo (($sort ?? 'recent')==='recent')?'selected':''; ?>>Más recientes</option>
                <option value="name_asc" <?php echo (($sort ?? 'recent')==='name_asc')?'selected':''; ?>>Nombre A→Z</option>
                <option value="name_desc" <?php echo (($sort ?? 'recent')==='name_desc')?'selected':''; ?>>Nombre Z→A</option>
                <option value="price_asc" <?php echo (($sort ?? 'recent')==='price_asc')?'selected':''; ?>>Precio menor a mayor</option>
                <option value="price_desc" <?php echo (($sort ?? 'recent')==='price_desc')?'selected':''; ?>>Precio mayor a menor</option>
            </select>
            </div>
        </div>
    </div>
    <?php if (empty($products)): ?>
        <div class="bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 text-slate-700">
            No hay productos en el catálogo.
            <a href="?route=admin/products" class="ml-2 font-semibold text-secondary hover:text-blue-700">Agregar productos</a>
        </div>
    <?php endif; ?>
    <div id="catalogGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($products as $p): ?>
            <div class="group bg-gradient-to-br from-white via-white to-secondary/10 rounded-2xl border border-slate-200/70 shadow-soft overflow-hidden hover:-translate-y-1 hover:shadow-xl transition duration-300 product-card"
                 data-cat="<?php echo (int)$p['categoria_id']; ?>"
                 data-age="<?php echo htmlspecialchars($p['edad_recomendada'] ?? ''); ?>"
                 data-price="<?php echo (float)$p['precio']; ?>"
                 data-genero="<?php echo htmlspecialchars($p['genero'] ?? 'unisex'); ?>">
                <a href="?route=product/show&id=<?php echo (int)$p['id']; ?>" class="block">
                    <div class="aspect-[4/3] bg-slate-100 overflow-hidden">
                        <img src="<?php echo htmlspecialchars(product_image_url($p, 800, 600)); ?>" class="w-full h-full object-cover group-hover:scale-[1.05] transition duration-300" alt="<?php echo htmlspecialchars($p['nombre'] ?? ''); ?>">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-slate-900 leading-snug"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                        <p class="text-sm text-slate-500"><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <?php if (!empty($p['edad_recomendada'])): ?>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-700">Edad <?php echo htmlspecialchars($p['edad_recomendada']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($p['genero'])): ?>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-accent/15 text-amber-800"><?php echo htmlspecialchars($p['genero']); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="mt-2 text-lg font-bold text-primary">$<?php echo number_format($p['precio'], 2); ?></p>
                    </div>
                </a>
                <div class="p-4 pt-0">
                <form class="add-to-cart" method="post" action="?route=cart/add">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                    <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-accent text-white rounded-xl font-semibold hover:bg-yellow-600 active:bg-yellow-700 transition shadow-soft hover:-translate-y-0.5 active:translate-y-0">Agregar al carrito</button>
                </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="searchResults" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
    <?php if (!empty($products) && ($pages ?? 1) > 1): ?>
        <div class="flex items-center justify-center gap-2">
            <?php
                $cur = (int)($page ?? 1);
                $max = (int)$pages;
                $base = [
                    'route' => 'product/index',
                    'sort' => $sort ?? 'recent',
                ];
                if (!empty($cat)) $base['cat'] = (int)$cat;
                if (!empty($gender)) $base['gender'] = $gender;
                if (!empty($age)) $base['age'] = $age;
                if (!empty($price)) $base['price'] = $price;
                if (!empty($q)) $base['q'] = $q;
            ?>
            <a class="px-3 py-2 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 <?php echo ($cur<=1)?'opacity-50 pointer-events-none':''; ?>" href="?<?php echo http_build_query(array_merge($base, ['page' => max(1,$cur-1)])); ?>">Anterior</a>
            <?php for ($i=1; $i<=$max; $i++): ?>
                <a class="px-3 py-2 border border-slate-200 rounded-xl text-sm font-semibold <?php echo ($i===$cur)?'bg-secondary text-white border-secondary':'text-slate-700 hover:bg-slate-50'; ?>" href="?<?php echo http_build_query(array_merge($base, ['page' => $i])); ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <a class="px-3 py-2 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 <?php echo ($cur>=$max)?'opacity-50 pointer-events-none':''; ?>" href="?<?php echo http_build_query(array_merge($base, ['page' => min($max,$cur+1)])); ?>">Siguiente</a>
        </div>
    <?php endif; ?>
</section>
