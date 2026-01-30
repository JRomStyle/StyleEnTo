<?php
$orderId = (int)($order['id'] ?? 0);
$estado = (string)($order['estado'] ?? 'pendiente');
?>
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-secondary">Pedido #<?php echo $orderId; ?></h1>
    <a href="?route=admin/orders" class="px-3 py-1 bg-secondary text-white rounded hover:opacity-90 transition duration-200">Volver</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-600">Cliente</div>
                    <div class="font-semibold"><?php echo htmlspecialchars($order['cliente'] ?? ''); ?></div>
                    <div class="text-sm text-gray-600"><?php echo htmlspecialchars($order['cliente_email'] ?? ''); ?></div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Fecha</div>
                    <div class="font-semibold"><?php echo htmlspecialchars($order['fecha'] ?? ''); ?></div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Total</div>
                    <div class="font-semibold text-primary">$<?php echo number_format((float)($order['total'] ?? 0), 2); ?></div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Estado</div>
                    <div class="font-semibold"><?php echo htmlspecialchars($estado); ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="font-semibold mb-3">Productos</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Producto</th>
                            <th class="py-2">Cantidad</th>
                            <th class="py-2">Precio</th>
                            <th class="py-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <?php
                            $qty = (int)($it['cantidad'] ?? 0);
                            $price = (float)($it['precio_unitario'] ?? 0);
                            $sub = $qty * $price;
                            ?>
                            <tr class="border-b">
                                <td class="py-2">
                                    <div class="flex items-center gap-3">
                                        <img src="<?php echo htmlspecialchars(product_image_url($it, 160, 160)); ?>" class="w-12 h-12 rounded object-cover" alt="<?php echo htmlspecialchars($it['nombre'] ?? ''); ?>">
                                        <div class="font-semibold"><?php echo htmlspecialchars($it['nombre'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td class="py-2"><?php echo $qty; ?></td>
                                <td class="py-2">$<?php echo number_format($price, 2); ?></td>
                                <td class="py-2 font-semibold text-primary">$<?php echo number_format($sub, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="font-semibold mb-3">Movimientos de inventario</div>
            <?php if (empty($movements)): ?>
                <div class="text-sm text-gray-600">Sin movimientos asociados a este pedido.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Fecha</th>
                                <th class="py-2">Producto</th>
                                <th class="py-2">Tipo</th>
                                <th class="py-2">Cantidad</th>
                                <th class="py-2">Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $m): ?>
                                <tr class="border-b">
                                    <td class="py-2"><?php echo htmlspecialchars($m['fecha'] ?? ''); ?></td>
                                    <td class="py-2"><?php echo htmlspecialchars($m['producto_nombre'] ?? ''); ?></td>
                                    <td class="py-2"><?php echo htmlspecialchars($m['tipo'] ?? ''); ?></td>
                                    <td class="py-2"><?php echo (int)($m['cantidad'] ?? 0); ?></td>
                                    <td class="py-2"><?php echo htmlspecialchars($m['nota'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="font-semibold mb-3">Actualizar estado</div>
            <form method="post" action="?route=admin/orderUpdate" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo $orderId; ?>">
                <input type="hidden" name="redirect" value="admin/orderShow&id=<?php echo $orderId; ?>">
                <div>
                    <select name="estado" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-secondary focus:border-transparent">
                        <option value="pendiente" <?php echo ($estado === 'pendiente') ? 'selected' : ''; ?>>pendiente</option>
                        <option value="pagado" <?php echo ($estado === 'pagado') ? 'selected' : ''; ?>>pagado</option>
                        <option value="enviado" <?php echo ($estado === 'enviado') ? 'selected' : ''; ?>>enviado</option>
                        <option value="cancelado" <?php echo ($estado === 'cancelado') ? 'selected' : ''; ?>>cancelado</option>
                    </select>
                </div>
                <div>
                    <input name="nota" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Nota (opcional)" maxlength="255">
                </div>
                <button class="w-full px-4 py-2 bg-secondary text-white rounded hover:opacity-90 transition duration-200">Guardar</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="font-semibold mb-3">Historial de estados</div>
            <?php if (empty($history)): ?>
                <div class="text-sm text-gray-600">Sin historial.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Fecha</th>
                                <th class="py-2">Estado</th>
                                <th class="py-2">Admin</th>
                                <th class="py-2">Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $h): ?>
                                <tr class="border-b">
                                    <td class="py-2"><?php echo htmlspecialchars($h['fecha'] ?? ''); ?></td>
                                    <td class="py-2 font-semibold"><?php echo htmlspecialchars($h['estado'] ?? ''); ?></td>
                                    <td class="py-2"><?php echo htmlspecialchars($h['admin_nombre'] ?? 'Sistema'); ?></td>
                                    <td class="py-2"><?php echo htmlspecialchars($h['nota'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
