<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-6 text-center">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Nuestro Catálogo</h1>
    <p class="text-gray-600">Encuentra el regalo perfecto</p>
</div>

<div class="flex flex-col md:flex-row gap-6">
    <!-- Filters Sidebar -->
    <div class="w-full md:w-1/4">
        <div class="bg-white p-6 rounded-xl shadow-md sticky top-24">
            <h3 class="font-bold text-lg mb-4 text-gray-700">Filtros</h3>
            <div class="mb-4">
                <input type="text" id="searchProduct" placeholder="Buscar..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="mb-4">
                <h4 class="font-semibold text-sm mb-2 text-gray-600">Categorías</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="text-gray-500 hover:text-blue-500">Todos</a></li>
                    <!-- Dynamic categories could be loaded here via JS or simple PHP check -->
                </ul>
            </div>
             <div class="mb-4">
                <h4 class="font-semibold text-sm mb-2 text-gray-600">Precio</h4>
                <input type="range" class="w-full">
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="w-full md:w-3/4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="productGrid">
            <?php foreach($data['products'] as $product) : ?>
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group">
                    <div class="h-48 bg-gray-100 relative overflow-hidden">
                        <?php if(!empty($product->image)): ?>
                            <img src="<?php echo URLROOT; ?>/assets/img/products/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <i class="fa fa-image text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        <?php if($product->stock < 5 && $product->stock > 0): ?>
                             <span class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full">¡Pocos!</span>
                        <?php elseif($product->stock == 0): ?>
                             <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">Agotado</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-blue-500 font-bold uppercase mb-1"><?php echo $product->category_name; ?></div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight"><?php echo $product->name; ?></h3>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-xl font-bold text-gray-900">$<?php echo number_format($product->price, 2); ?></span>
                            <?php if($product->stock > 0): ?>
                                <a href="<?php echo URLROOT; ?>/cart/add/<?php echo $product->id; ?>" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition focus:outline-none">
                                    <i class="fa fa-plus"></i>
                                </a>
                            <?php else: ?>
                                <button class="bg-gray-300 text-gray-500 rounded-full w-8 h-8 flex items-center justify-center cursor-not-allowed">
                                    <i class="fa fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
