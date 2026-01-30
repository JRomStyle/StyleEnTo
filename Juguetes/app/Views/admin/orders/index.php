<h1 class="text-2xl font-bold text-secondary mb-4">Pedidos</h1>
<div class="bg-white rounded-xl shadow p-6">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="py-2">ID</th>
                <th class="py-2">Cliente</th>
                <th class="py-2">Fecha</th>
                <th class="py-2">Total</th>
                <th class="py-2">Estado</th>
                <th class="py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr class="border-b">
                    <td class="py-2">#<?php echo (int)$o['id']; ?></td>
                    <td class="py-2"><?php echo htmlspecialchars($o['cliente'] ?? ''); ?></td>
                    <td class="py-2"><?php echo htmlspecialchars($o['fecha']); ?></td>
                    <td class="py-2 text-primary font-semibold">$<?php echo number_format($o['total'], 2); ?></td>
                    <td class="py-2"><?php echo htmlspecialchars($o['estado']); ?></td>
                    <td class="py-2">
                        <div class="flex items-center gap-2">
                            <a href="?route=admin/orderShow&id=<?php echo (int)$o['id']; ?>" class="px-3 py-1 bg-accent text-white rounded hover:opacity-90 transition duration-200">Ver</a>
                            <form method="post" action="?route=admin/orderUpdate" class="flex items-center gap-2">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$o['id']; ?>">
                                <select name="estado" class="border rounded px-2 py-1 focus:ring-2 focus:ring-secondary focus:border-transparent">
                                    <option value="pendiente" <?php echo ($o['estado']==='pendiente')?'selected':''; ?>>pendiente</option>
                                    <option value="pagado" <?php echo ($o['estado']==='pagado')?'selected':''; ?>>pagado</option>
                                    <option value="enviado" <?php echo ($o['estado']==='enviado')?'selected':''; ?>>enviado</option>
                                    <option value="cancelado" <?php echo ($o['estado']==='cancelado')?'selected':''; ?>>cancelado</option>
                                </select>
                                <button class="px-3 py-1 bg-secondary text-white rounded hover:opacity-90 transition duration-200">Actualizar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
