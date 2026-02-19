<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Promotions Banner -->
<div class="mb-8 rounded-2xl bg-brand-primary text-white shadow-lg overflow-hidden">
    <div class="px-6 py-5 sm:px-8 md:py-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/20 backdrop-blur">
                <i class="fa fa-tags text-white text-lg"></i>
            </span>
            <div>
                <p class="text-sm uppercase tracking-wide font-semibold">Promociones</p>
                <p class="text-lg md:text-xl font-bold font-display">Hasta 30% OFF en juguetes seleccionados</p>
            </div>
        </div>
        <a href="<?php echo URLROOT; ?>/products" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white text-brand-primary font-bold hover:bg-gray-100 transition">
            Ver ofertas
            <i class="fa fa-arrow-right ml-2"></i>
        </a>
    </div>
    <div class="h-1 w-full bg-white/20"></div>
    <div class="px-6 py-3 text-center text-sm sm:text-base bg-white/10">
        Envío gratis a partir de $50.000 | Cambios sin costo
    </div>
</div>

<!-- Hero Section -->
<div class="relative overflow-hidden rounded-3xl shadow-2xl mb-12 animate-fade-in bg-brand-secondary">
    <div class="relative z-10 p-12 md:p-16 text-center text-brand-accent">
        <h1 class="text-5xl md:text-7xl font-display font-bold mb-6 animate-slide-in-up">
            <?php echo $data['title']; ?>
        </h1>
        <p class="text-xl md:text-2xl mb-10 opacity-95 animate-slide-in-up stagger-1 max-w-3xl mx-auto">
            <?php echo $data['description']; ?>
        </p>
        <a href="#catalogo" class="inline-block bg-brand-primary text-white font-bold py-4 px-10 rounded-full shadow-2xl hover-lift transition-all duration-300 transform hover:scale-110 btn-playful animate-slide-in-up stagger-2">
            <i class="fa fa-rocket mr-3"></i>¡Descubre la Magia!
        </a>
    </div>
    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-primary rounded-full opacity-20 animate-float"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-brand-primary rounded-full opacity-20 animate-float stagger-2"></div>
    <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-brand-primary rounded-full opacity-20 animate-bounce-gentle"></div>
</div>

<!-- Featured Products -->
<div class="text-center mb-10">
    <h2 class="section-title">
        Productos Destacados
    </h2>
    <p class="text-brand-text text-lg">Los favoritos de nuestros pequeños aventureros</p>
</div>

<?php if(empty($data['products'])): ?>
    <div class="text-center py-20 bg-white rounded-3xl shadow-lg">
        <i class="fa fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-xl">Próximamente nuevos productos...</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        <?php foreach($data['products'] as $index => $product) : ?>
            <div class="product-card bg-white rounded-3xl shadow-lg overflow-hidden group animate-slide-in-up stagger-<?php echo ($index % 6) + 1; ?>">
                <!-- Image Container -->
                <div class="relative h-64 bg-brand-background overflow-hidden">
                    <?php if(!empty($product->image)): ?>
                        <img src="<?php echo URLROOT; ?>/assets/img/products/<?php echo $product->image; ?>" 
                             alt="<?php echo $product->name; ?>" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full text-gray-300">
                            <i class="fa fa-image text-6xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Stock Badge -->
                    <?php if($product->stock < 5 && $product->stock > 0): ?>
                        <span class="absolute top-3 right-3 bg-brand-secondary text-brand-accent text-xs font-extrabold px-3 py-1 rounded-full shadow-lg animate-pulse-soft">
                            ¡Últimas unidades!
                        </span>
                    <?php elseif($product->stock == 0): ?>
                        <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-lg">
                            Agotado
                        </span>
                    <?php else: ?>
                        <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-lg">
                            Disponible
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <div class="text-xs text-brand-primary font-extrabold uppercase mb-2 tracking-wider">
                        <?php echo $product->category_name; ?>
                    </div>
                    <h3 class="text-xl font-bold text-brand-accent mb-3 leading-tight group-hover:text-brand-primary transition-colors">
                        <?php echo $product->name; ?>
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-3xl font-display font-bold text-brand-primary">
                            $<?php echo number_format($product->price, 2); ?>
                        </span>
                        <a href="<?php echo URLROOT; ?>/products/show/<?php echo $product->id; ?>" 
                           class="bg-brand-accent text-white font-bold py-2 px-5 rounded-full hover-lift shadow-lg transition-all duration-300">
                            Ver más
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- View All Button -->
    <div class="text-center">
        <a href="<?php echo URLROOT; ?>/products" 
           class="inline-block bg-white text-brand-primary font-bold py-4 px-10 rounded-full border-4 border-brand-primary hover:bg-brand-primary hover:text-white transition-all duration-300 shadow-xl hover-lift">
            <i class="fa fa-grid mr-3"></i>Ver Todo el Catálogo
        </a>
    </div>
<?php endif; ?>

<!-- Popular Categories -->
<div class="mt-16">
    <div class="text-center mb-10">
        <h2 class="section-title">
            Categorías Populares
        </h2>
        <p class="text-brand-text text-lg">Explora mundos de diversión</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <?php 
        $categories = [
            ['name' => 'Bloques', 'icon' => 'fa-cubes'],
            ['name' => 'Muñecas', 'icon' => 'fa-child-dress'],
            ['name' => 'Puzzles', 'icon' => 'fa-puzzle-piece'],
            ['name' => 'Vehículos', 'icon' => 'fa-car']
        ];
        foreach($categories as $index => $category): ?>
            <a href="#" class="category-card block bg-white p-6 rounded-2xl shadow-lg hover-lift transition-all duration-300 animate-slide-in-up stagger-<?php echo $index + 1; ?>">
                <div class="card-icon inline-block p-5 rounded-full mb-4">
                    <i class="fa <?php echo $category['icon']; ?> text-4xl text-brand-primary"></i>
                </div>
                <h3 class="font-bold text-lg text-brand-accent"><?php echo $category['name']; ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
