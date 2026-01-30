<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente para emprendedores pobres de tiempo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444',
                    },
                },
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-primary">Asistente Emprendedor</h1>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex space-x-4">
                        <a href="index.php" class="text-gray-700 hover:text-primary">Inicio</a>
                        <a href="companies.php" class="text-gray-700 hover:text-primary">Empresas</a>
                        <a href="income.php" class="text-gray-700 hover:text-primary">Ingresos</a>
                        <a href="expenses.php" class="text-gray-700 hover:text-primary">Gastos</a>
                        <a href="logout.php" class="text-gray-700 hover:text-primary">Cerrar Sesión</a>
                    </div>
                <?php else: ?>
                    <div class="flex space-x-4">
                        <a href="login.php" class="text-gray-700 hover:text-primary">Iniciar Sesión</a>
                        <a href="register.php" class="text-gray-700 hover:text-primary">Registrarse</a>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <?php echo $content; ?>
    </main>

    <footer class="bg-white shadow-inner mt-8 py-4">
        <div class="container mx-auto px-4 text-center text-gray-600">
            <p>Asistente para emprendedores pobres de tiempo © <?php echo date('Y'); ?></p>
        </div>
    </footer>
</body>
</html>