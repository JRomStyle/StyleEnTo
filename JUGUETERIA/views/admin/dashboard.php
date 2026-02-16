<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Panel de Administración</h1>
    <p class="text-gray-600">Bienvenido al control de mando</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Ventas Hoy</p>
                <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($data['total_sales'] ?? 0, 2); ?></p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full text-blue-500">
                <i class="fa fa-dollar-sign text-2xl"></i>
            </div>
        </div>
    </div>
    <!-- Stat Card 2 -->
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Ordenes Pendientes</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $data['pending_orders']; ?></p>
            </div>
            <div class="p-3 bg-green-100 rounded-full text-green-500">
                <i class="fa fa-shopping-cart text-2xl"></i>
            </div>
        </div>
    </div>
    <!-- Stat Card 3 -->
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Productos Bajos</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $data['low_stock']; ?></p>
            </div>
            <div class="p-3 bg-yellow-100 rounded-full text-yellow-500">
                <i class="fa fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
    </div>
    <!-- Stat Card 4 -->
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Clientes</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $data['user_count']; ?></p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full text-purple-500">
                <i class="fa fa-users text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-md p-6 md:col-span-1">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 gap-4">
            <a href="<?php echo URLROOT; ?>/products/add" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-200">
                <div class="p-3 bg-blue-500 text-white rounded-lg mr-4">
                    <i class="fa fa-box"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Nuevo Producto</h4>
                    <p class="text-sm text-gray-500">Agregar al inventario</p>
                </div>
            </a>
            <a href="<?php echo URLROOT; ?>/categories/index" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-200">
                <div class="p-3 bg-purple-500 text-white rounded-lg mr-4">
                    <i class="fa fa-tags"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Gestionar Categorías</h4>
                    <p class="text-sm text-gray-500">Ver y editar</p>
                </div>
            </a>
            <a href="<?php echo URLROOT; ?>/orders/index" class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-200">
                <div class="p-3 bg-green-500 text-white rounded-lg mr-4">
                    <i class="fa fa-list"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Ver Ventas</h4>
                    <p class="text-sm text-gray-500">Historial completo</p>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Recent Sales Chart Placeholder -->
     <div class="bg-white rounded-xl shadow-md p-6 md:col-span-2">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Ventas Recientes</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg border border-dashed border-gray-300 text-gray-400">
            <p>Gráfico de ventas aquí (Chart.js)</p>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
