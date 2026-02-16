<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-6">
    <a href="<?php echo URLROOT; ?>/products/index" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
        <i class="fa fa-arrow-left mr-2"></i> Volver al Catálogo
    </a>
</div>

<div class="bg-white rounded-2xl shadow-lg p-8">
    <div class="flex flex-col md:flex-row gap-10">
        <!-- Product Image -->
        <div class="w-full md:w-1/2">
            <?php if(!empty($data['product']->image)): ?>
                <img src="<?php echo URLROOT; ?>/assets/img/products/<?php echo $data['product']->image; ?>" alt="<?php echo $data['product']->name; ?>" class="w-full h-auto rounded-xl shadow-md object-cover">
            <?php else: ?>
                <div class="w-full h-96 bg-gray-100 flex items-center justify-center rounded-xl text-gray-400">
                    <i class="fa fa-image text-6xl"></i>
                    <span class="ml-4 text-xl">Sin Imagen</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div class="w-full md:w-1/2 flex flex-col justify-center">
            <span class="text-blue-500 font-bold uppercase tracking-wide mb-2"><?php echo $data['category_name']; ?></span>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4"><?php echo $data['product']->name; ?></h1>
            <div class="flex items-center mb-6">
                <span class="text-3xl font-bold text-gray-800 mr-4">$<?php echo number_format($data['product']->price, 2); ?></span>
                <?php if($data['product']->stock > 0): ?>
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Disponible</span>
                <?php else: ?>
                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Agotado</span>
                <?php endif; ?>
            </div>
            
            <p class="text-gray-600 mb-8 text-lg leading-relaxed"><?php echo $data['product']->description; ?></p>
            
            <?php if($data['product']->stock > 0): ?>
                <div class="flex items-center gap-4">
                    <div class="w-24">
                        <label for="quantity" class="sr-only">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $data['product']->stock; ?>" value="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center py-2 border">
                    </div>
                    <a href="<?php echo URLROOT; ?>/cart/add/<?php echo $data['product']->id; ?>" class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-3 px-8 rounded-full shadow-lg transition transform hover:scale-105 flex items-center justify-center">
                        <i class="fa fa-shopping-cart mr-2"></i> Agregar al Carrito
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-gray-100 p-4 rounded-lg text-center text-gray-500">
                    Este producto no está disponible por el momento.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
