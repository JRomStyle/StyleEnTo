<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Hero Section -->
<div class="relative overflow-hidden rounded-3xl shadow-2xl mb-12 animate-fade-in">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 opacity-90"></div>
    <div class="absolute inset-0 bg-shimmer"></div>
    <div class="relative z-10 p-12 md:p-16 text-center text-white">
        <h1 class="text-5xl md:text-7xl font-extrabold mb-6 animate-slide-in-up text-shadow-playful">
            <?php echo $data['title']; ?>
        </h1>
        <p class="text-xl md:text-2xl mb-10 opacity-95 animate-slide-in-up stagger-1 max-w-3xl mx-auto">
            <?php echo $data['description']; ?>
        </p>
        <a href="#catalogo" class="inline-block bg-yellow-400 text-yellow-900 font-extrabold py-4 px-10 rounded-full shadow-2xl hover-lift transition-all duration-300 transform hover:scale-110 btn-playful animate-slide-in-up stagger-2">
            <i class="fa fa-rocket mr-3"></i>¡Descubre la Magia!
        </a>
    </div>
    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-yellow-300 rounded-full opacity-20 animate-float"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 bg-pink-300 rounded-full opacity-20 animate-float stagger-2"></div>
    <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-blue-300 rounded-full opacity-20 animate-bounce-gentle"></div>
</div>

<!-- Featured Products -->
<div class="text-center mb-10">
    <h2 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-3">
        ✨ Productos Destacados
    </h2>
    <p class="text-gray-600 text-lg">Los favoritos de nuestros pequeños aventureros</p>
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
                <div class="relative h-64 bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
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
                        <span class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 text-xs font-extrabold px-3 py-1 rounded-full shadow-lg animate-pulse-soft">
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
                    <div class="text-xs text-blue-600 font-extrabold uppercase mb-2 tracking-wider">
                        <?php echo $product->category_name; ?>
                    </div>
                    <h3 class="text-xl font-extrabold text-gray-900 mb-3 leading-tight group-hover:text-purple-600 transition-colors">
                        <?php echo $product->name; ?>
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-3xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            $<?php echo number_format($product->price, 2); ?>
                        </span>
                        <a href="<?php echo URLROOT; ?>/products/show/<?php echo $product->id; ?>" 
                           class="bg-gradient-to-r from-blue-500 to-purple-500 text-white font-bold py-2 px-5 rounded-full hover-lift shadow-lg transition-all duration-300">
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
           class="inline-block bg-white text-purple-600 font-extrabold py-4 px-10 rounded-full border-4 border-purple-500 hover:bg-purple-500 hover:text-white transition-all duration-300 shadow-xl hover-lift">
            <i class="fa fa-grid mr-3"></i>Ver Todo el Catálogo
        </a>
    </div>
<?php endif; ?>

<?php require APPROOT . '/views/inc/footer.php'; ?>
