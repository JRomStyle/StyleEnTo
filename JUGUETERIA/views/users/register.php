<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-10">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 transform transition duration-300 hover:scale-[1.01]">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Crear Cuenta</h2>
        <p class="text-center text-gray-500 mb-6">Regístrate para comprar en nuestra tienda</p>
        <form action="<?php echo URLROOT; ?>/users/register" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre: <sup>*</sup></label>
                <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo (!empty($data['name_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['name']; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $data['name_err']; ?></span>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email: <sup>*</sup></label>
                <input type="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo (!empty($data['email_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['email']; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $data['email_err']; ?></span>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password: <sup>*</sup></label>
                <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo (!empty($data['password_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['password']; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $data['password_err']; ?></span>
            </div>
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Password: <sup>*</sup></label>
                <input type="password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo (!empty($data['confirm_password_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $data['confirm_password_err']; ?></span>
            </div>

            <div class="flex items-center justify-between">
                <input type="submit" value="Registrarse" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline cursor-pointer w-full transition duration-300">
            </div>
            <div class="mt-4 text-center">
                <a href="<?php echo URLROOT; ?>/users/login" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    ¿Ya tienes cuenta? Ingresar
                </a>
            </div>
        </form>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
