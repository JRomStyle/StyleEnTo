<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Tu Carrito de Compras</h1>
</div>
<?php flash('cart_message'); ?>

<?php if(empty($data['cart_items'])): ?>
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <p class="text-gray-500 text-lg mb-4">El carrito está vacío</p>
        <a href="<?php echo URLROOT; ?>/products/index" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ir a comprar</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">Revisión</th>
                            <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">Precio</th>
                            <th class="text-center py-3 px-4 font-semibold text-sm text-gray-600">Cant</th>
                            <th class="text-right py-3 px-4 font-semibold text-sm text-gray-600">Subtotal</th>
                            <th class="text-center py-3 px-4 font-semibold text-sm text-gray-600"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php 
                        $total = 0;
                        foreach($data['cart_items'] as $id => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td class="py-4 px-4 flex items-center">
                                <?php if($item['image']): ?>
                                    <img src="<?php echo URLROOT; ?>/assets/img/products/<?php echo $item['image']; ?>" class="w-16 h-16 object-cover rounded mr-4">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded mr-4 flex items-center justify-center text-gray-500"><i class="fa fa-image"></i></div>
                                <?php endif; ?>
                                <span class="font-medium text-gray-800"><?php echo $item['name']; ?></span>
                            </td>
                            <td class="py-4 px-4 text-gray-600">$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="py-4 px-4 text-center">
                                <form action="<?php echo URLROOT; ?>/cart/update/<?php echo $id; ?>" method="get" class="inline-flex">
                                    <input type="number" name="qty" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 text-center border rounded mx-1" onchange="window.location.href='<?php echo URLROOT; ?>/cart/update/<?php echo $id; ?>/'+this.value">
                                </form>
                            </td>
                            <td class="py-4 px-4 text-right font-bold text-gray-800">$<?php echo number_format($subtotal, 2); ?></td>
                            <td class="py-4 px-4 text-center">
                                <a href="<?php echo URLROOT; ?>/cart/remove/<?php echo $id; ?>" class="text-red-500 hover:text-red-700"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="<?php echo URLROOT; ?>/cart/clear" class="text-red-500 text-sm hover:underline">Vaciar Carrito</a>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Resumen</h3>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-bold">$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="flex justify-between mb-4 border-b pb-4">
                    <span class="text-gray-600">Impuestos</span>
                    <span class="font-bold">$0.00</span>
                </div>
                <div class="flex justify-between mb-6 text-xl font-bold text-gray-900">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <a href="<?php echo URLROOT; ?>/orders/checkout" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg text-center transition duration-300 shadow-lg">
                    Proceder al Pag
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
