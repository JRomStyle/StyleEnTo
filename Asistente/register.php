<?php
require_once 'config/db.php';
require_once 'controllers/UserController.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$userController = new UserController($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = $userController->register($name, $email, $password);
    $message = $result['message'];
    
    if ($result['success']) {
        header('Location: login.php');
        exit;
    }
}

// Render the view
ob_start();
?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Registrarse</h2>
    
    <?php if ($message): ?>
        <div class="mb-4 p-2 rounded bg-red-50 text-danger text-sm">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="register.php">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" id="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        
        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded hover:bg-primary/90 transition">
            Registrarse
        </button>
    </form>
    
    <p class="mt-4 text-center text-sm text-gray-600">
        ¿Ya tienes una cuenta? <a href="login.php" class="text-primary hover:underline">Inicia sesión</a>
    </p>
</div>
<?php
$content = ob_get_clean();
include 'views/template.php';
?>