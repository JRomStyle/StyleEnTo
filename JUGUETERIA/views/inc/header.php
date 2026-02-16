<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?> - La Magia de Jugar</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/custom.css">
    <style>
        /* Inline critical styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 font-sans leading-normal tracking-normal flex flex-col min-h-screen">
    <nav class="bg-white/95 backdrop-blur-md shadow-xl sticky top-0 z-50 border-b-4 border-gradient-playful">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="<?php echo URLROOT; ?>" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <i class="fa-solid fa-shapes text-4xl bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 bg-clip-text text-transparent group-hover:animate-wiggle"></i>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full animate-pulse-soft"></div>
                    </div>
                    <span class="font-extrabold text-2xl bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent group-hover:scale-105 transition-transform">
                        <?php echo SITENAME; ?>
                    </span>
                </a>

                <!-- Navigation Items -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                        <!-- User Info -->
                        <div class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-purple-50 to-pink-50 rounded-full">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold shadow-lg animate-pop-in">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <span class="text-gray-700 font-medium">
                                Hola, <span class="text-purple-600 font-bold"><?php echo $_SESSION['user_name']; ?></span>
                            </span>
                        </div>
                        
                        <!-- Admin Button -->
                        <?php if($_SESSION['user_role'] == 1) : ?>
                            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="px-5 py-2.5 font-bold text-white bg-gradient-to-r from-purple-500 to-pink-500 rounded-full hover-lift shadow-lg transition-all duration-300 hover:shadow-glow-purple">
                                <i class="fa fa-crown mr-2"></i>Admin
                            </a>
                        <?php endif; ?>
                        
                        <!-- Logout Button -->
                        <a href="<?php echo URLROOT; ?>/users/logout" class="px-5 py-2.5 font-medium text-gray-700 hover:text-red-500 rounded-full hover:bg-red-50 transition-all duration-300 border-2 border-transparent hover:border-red-200">
                            <i class="fa fa-sign-out mr-2"></i>Salir
                        </a>
                    <?php else : ?>
                        <!-- Login Button -->
                        <a href="<?php echo URLROOT; ?>/users/login" class="px-5 py-2.5 font-medium text-gray-700 hover:text-green-600 rounded-full hover:bg-green-50 transition-all duration-300 hover-scale border-2 border-transparent hover:border-green-200">
                            <i class="fa fa-sign-in mr-2"></i>Ingresar
                        </a>
                        <!-- Register Button -->
                        <a href="<?php echo URLROOT; ?>/users/register" class="px-6 py-2.5 font-bold text-white bg-gradient-to-r from-blue-500 to-purple-500 rounded-full hover-lift shadow-lg transition-all duration-300 btn-playful">
                            <i class="fa fa-user-plus mr-2"></i>Registrarse
                        </a>
                    <?php endif; ?>
                    
                    <!-- Cart Icon -->
                    <a href="<?php echo URLROOT; ?>/cart" class="relative px-4 py-2 group">
                        <div class="relative">
                            <i class="fa-solid fa-cart-shopping text-2xl text-gray-700 group-hover:text-yellow-500 transition-colors duration-300 group-hover:animate-wiggle"></i>
                            <?php 
                            $cartCount = 0;
                            if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
                                foreach($_SESSION['cart'] as $item){
                                    $cartCount += $item['quantity'];
                                }
                            }
                            ?>
                            <?php if($cartCount > 0): ?>
                            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-gradient-to-r from-red-500 to-pink-500 rounded-full shadow-lg animate-bounce-gentle">
                                <?php echo $cartCount; ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <?php flash('register_success'); ?>
