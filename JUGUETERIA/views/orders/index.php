<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Mis Compras</h1>
    <?php flash('order_message'); ?>
    
    <?php if(empty($data['orders'])): ?>
        <p class="text-gray-500">No has realizado ninguna compra aún.</p>
    <?php else: ?>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID Orden
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['orders'] as $order) : ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">#<?php echo $order->id; ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap"><?php echo $order->created_at; ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap font-bold">$<?php echo $order->total_amount; ?></p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                <span class="relative"><?php echo $order->status; ?></span>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
