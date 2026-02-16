<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Editar Categoría</h2>
        <a href="<?php echo URLROOT; ?>/categories/index" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded text-sm">Cancelar</a>
    </div>
    <form action="<?php echo URLROOT; ?>/categories/edit/<?php echo $data['id']; ?>" method="post">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nombre: <sup>*</sup></label>
            <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring <?php echo (!empty($data['name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['name']; ?>">
            <span class="text-red-500 text-xs italic"><?php echo $data['name_err']; ?></span>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
            <textarea name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring"><?php echo $data['description']; ?></textarea>
        </div>
        <input type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full cursor-pointer" value="Actualizar">
    </form>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
