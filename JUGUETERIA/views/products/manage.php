<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Gestionar Productos</h1>
    <div>
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Volver</a>
        <a href="<?php echo URLROOT; ?>/products/add" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"><i class="fa fa-plus"></i> Nuevo Producto</a>
    </div>
</div>
<?php flash('product_message'); ?>
<div class="bg-white shadow-md rounded my-6 overflow-x-auto">
    <table class="min-w-full w-full table-auto">
        <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Imagen</th>
                <th class="py-3 px-6 text-left">Nombre</th>
                <th class="py-3 px-6 text-left">Categoría</th>
                <th class="py-3 px-6 text-center">Precio</th>
                <th class="py-3 px-6 text-center">Stock</th>
                <th class="py-3 px-6 text-center">Estado</th>
                <th class="py-3 px-6 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
            <?php foreach($data['products'] as $product) : ?>
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left">
                    <?php if(!empty($product->image)): ?>
                        <div class="flex items-center">
                            <div class="mr-2">
                                <img class="w-6 h-6 rounded-full" src="<?php echo URLROOT; ?>/assets/img/products/<?php echo $product->image; ?>"/>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="text-gray-400"><i class="fa fa-image"></i></span>
                    <?php endif; ?>
                </td>
                <td class="py-3 px-6 text-left font-medium"><?php echo $product->name; ?></td>
                <td class="py-3 px-6 text-left"><?php echo $product->category_name; ?></td>
                <td class="py-3 px-6 text-center">$<?php echo $product->price; ?></td>
                <td class="py-3 px-6 text-center">
                    <span class="<?php echo ($product->stock < 5) ? 'bg-red-200 text-red-600' : 'bg-green-200 text-green-600'; ?> py-1 px-3 rounded-full text-xs">
                        <?php echo $product->stock; ?>
                    </span>
                </td>
                <td class="py-3 px-6 text-center">
                    <span class="<?php echo ($product->status == 'active') ? 'bg-blue-200 text-blue-600' : 'bg-gray-200 text-gray-600'; ?> py-1 px-3 rounded-full text-xs">
                        <?php echo $product->status; ?>
                    </span>
                </td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center">
                        <a href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->id; ?>" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <form action="<?php echo URLROOT; ?>/products/delete/<?php echo $product->id; ?>" method="post" onsubmit="return confirm('¿Eliminar producto?');">
                            <button type="submit" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110 bg-transparent border-0 cursor-pointer">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
