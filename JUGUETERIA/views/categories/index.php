<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Categorías</h1>
    <div>
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Volver</a>
        <a href="<?php echo URLROOT; ?>/categories/add" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"><i class="fa fa-plus"></i> Nueva Categoría</a>
    </div>
</div>
<?php flash('category_message'); ?>
<div class="bg-white shadow-md rounded my-6 overflow-x-auto">
    <table class="min-w-full w-full table-auto">
        <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">ID</th>
                <th class="py-3 px-6 text-left">Nombre</th>
                <th class="py-3 px-6 text-left">Descripción</th>
                <th class="py-3 px-6 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
            <?php foreach($data['categories'] as $category) : ?>
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left whitespace-nowrap">
                    <span class="font-medium"><?php echo $category->id; ?></span>
                </td>
                <td class="py-3 px-6 text-left">
                    <span><?php echo $category->name; ?></span>
                </td>
                <td class="py-3 px-6 text-left">
                    <span><?php echo $category->description; ?></span>
                </td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center">
                        <a href="<?php echo URLROOT; ?>/categories/edit/<?php echo $category->id; ?>" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <form action="<?php echo URLROOT; ?>/categories/delete/<?php echo $category->id; ?>" method="post" onsubmit="return confirm('¿Estás seguro?');">
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
