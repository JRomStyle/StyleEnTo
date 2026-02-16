<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Agregar Producto</h2>
        <a href="<?php echo URLROOT; ?>/products/manage" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded text-sm">Cancelar</a>
    </div>
    <form action="<?php echo URLROOT; ?>/products/add" method="post" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nombre: <sup>*</sup></label>
                <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring <?php echo (!empty($data['name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['name']; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $data['name_err']; ?></span>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                <select name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring">
                    <?php foreach($data['categories'] as $category) : ?>
                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Precio: <sup>*</sup></label>
                <input type="number" step="0.01" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring <?php echo (!empty($data['price_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['price']; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $data['price_err']; ?></span>
            </div>
             <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Stock:</label>
                <input type="number" name="stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring" value="<?php echo $data['stock']; ?>">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
            <textarea name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring h-24"><?php echo $data['description']; ?></textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Imagen:</label>
            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
            <span class="text-red-500 text-xs italic"><?php echo $data['image_err']; ?></span>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
             <select name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring">
                <option value="active" <?php echo ($data['status'] == 'active') ? 'selected' : ''; ?>>Activo</option>
                <option value="inactive" <?php echo ($data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>

        <input type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full cursor-pointer" value="Guardar Producto">
    </form>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
