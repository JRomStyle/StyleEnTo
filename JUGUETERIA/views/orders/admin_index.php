<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Gestionar Ventas</h1>
    <div>
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Volver</a>
    </div>
</div>
<?php flash('order_message'); ?>
<div class="bg-white shadow-md rounded my-6 overflow-x-auto">
    <table class="min-w-full w-full table-auto">
        <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">ID</th>
                <th class="py-3 px-6 text-left">Cliente</th>
                <th class="py-3 px-6 text-left">Fecha</th>
                <th class="py-3 px-6 text-center">Total</th>
                <th class="py-3 px-6 text-center">Estado</th>
               <!-- Actions if needed (view details) -->
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
            <?php foreach($data['orders'] as $order) : ?>
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <span class="font-medium">#<?php echo $order->id; ?></span>
                </td>
                <td class="py-3 px-6 text-left">
                    <span><?php echo $order->user_name; ?></span>
                </td>
                 <td class="py-3 px-6 text-left">
                    <span><?php echo $order->created_at; ?></span>
                </td>
                <td class="py-3 px-6 text-center font-bold">
                    $<?php echo number_format($order->total_amount, 2); ?>
                </td>
                <td class="py-3 px-6 text-center">
                    <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">
                        <?php echo $order->status; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
