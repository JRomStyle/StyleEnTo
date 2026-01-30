<?php
$user = auth_user();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Juguetes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ef4444',
                        secondary: '#3b82f6',
                        accent: '#f59e0b'
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(15, 23, 42, 0.08)',
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-sky-50 via-white to-amber-50 text-slate-900">
    <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/80 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
            <a href="?route=home/index" class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-secondary/20 to-accent/30 text-secondary grid place-items-center font-bold shadow-soft">J</span>
                <span class="text-lg sm:text-xl font-bold tracking-tight">Juguetes</span>
            </a>
            <nav class="flex items-center gap-2 sm:gap-3 flex-wrap justify-end">
                <a href="?route=product/index" class="px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">Catálogo</a>
                <a href="?route=cart/index" class="relative px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">
                    Carrito
                    <?php $count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                    <span id="cartCount" class="ml-2 inline-flex items-center justify-center min-w-6 h-6 px-2 rounded-full bg-secondary text-white text-xs font-semibold"><?php echo (int)$count; ?></span>
                </a>
                <?php if ($user): ?>
                    <a href="?route=order/my" class="px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">Mis pedidos</a>
                    <?php if (($user['rol'] ?? '') === 'admin'): ?>
                        <a href="?route=admin/dashboard" class="px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">Admin</a>
                    <?php endif; ?>
                    <span class="hidden md:inline text-sm text-slate-600">Hola, <?php echo htmlspecialchars($user['nombre'] ?? ''); ?></span>
                    <a href="?route=auth/logout" class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:brightness-110 active:brightness-95 transition shadow-soft">Salir</a>
                <?php else: ?>
                    <a href="?route=auth/login" class="px-3 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition">Entrar</a>
                    <a href="?route=auth/register" class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-white bg-secondary rounded-lg hover:bg-blue-600 active:bg-blue-700 transition shadow-soft">Registro</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <?php echo $content; ?>
    </main>
    <footer class="border-t border-slate-200/70 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600">
            © <?php echo date('Y'); ?> Juguetes. Todos los derechos reservados.
        </div>
    </footer>
    <script src="assets/js/app.js"></script>
</body>
</html>
