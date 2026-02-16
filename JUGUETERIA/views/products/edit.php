<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex justify-center">
    <div class="w-full max-w-lg">
        <div class="bg-white p-8 rounded-xl shadow-lg mt-10">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Editar Producto</h2>
                <a href="<?php echo URLROOT; ?>/products/manage" class="text-gray-500 hover:text-gray-700"><i class="fa fa-arrow-left"></i> Volver</a>
            </div>
            
            <form action="<?php echo URLROOT; ?>/products/edit/<?php echo $data['id']; ?>" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($data['name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['name']; ?>">
                    <span class="text-red-500 text-xs italic"><?php echo $data['name_err']; ?></span>
                </div>
                
                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                    <div class="relative">
                        <select name="category_id" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                            <?php foreach($data['categories'] as $category) : ?>
                                <option value="<?php echo $category->id; ?>" <?php echo ($data['category_id'] == $category->id) ? 'selected' : ''; ?>><?php echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="4"><?php echo $data['description']; ?></textarea>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="w-1/2">
                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                        <input type="number" step="0.01" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($data['price_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['price']; ?>">
                        <span class="text-red-500 text-xs italic"><?php echo $data['price_err']; ?></span>
                    </div>
                    <div class="w-1/2">
                        <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">Stock:</label>
                        <input type="number" name="stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $data['stock']; ?>">
                    </div>
                </div>
                
                 <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
                    <select name="status" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                         <option value="active" <?php echo ($data['status'] == 'active') ? 'selected' : ''; ?>>Activo</option>
                         <option value="inactive" <?php echo ($data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Imagen del Producto:</label>
                    <?php if(!empty($data['image'])) : ?>
                        <div class="mb-2">
                             <img src="<?php echo URLROOT; ?>/assets/img/products/<?php echo $data['image']; ?>" alt="Actual" class="h-20 w-20 object-cover rounded">
                             <p class="text-xs text-gray-500">Imagen actual (subir nueva para cambiar)</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                    "/>
                    <span class="text-red-500 text-xs italic"><?php echo $data['image_err']; ?></span>
                </div>

                <div class="flex items-center justify-between">
                    <input type="submit" value="Actualizar Producto" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline cursor-pointer w-full transition duration-300">
                </div>
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
