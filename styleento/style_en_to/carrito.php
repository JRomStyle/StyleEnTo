<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';
$user = current_user();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="app-base" content="<?= e(rtrim(base_url(), '/')) ?>" />
  <title>Carrito — style en to!</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { accent: "#00ff85", danger: "#ff2d2d" } } } }
  </script>
  <link rel="stylesheet" href="<?= e(url('assets/css/tailwind.css')) ?>" />
</head>
<body class="bg-white text-black">
  <header class="sticky top-0 z-40 border-b border-black/10 glass">
    <div class="mx-auto max-w-7xl px-5 py-4 flex items-center justify-between gap-4">
      <a href="<?= e(url('index.php')) ?>" class="font-extrabold tracking-tight text-lg">
        style <span class="text-accent">en</span> to<span class="text-danger">!</span>
      </a>
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a class="link-underline" href="<?= e(url('index.php')) ?>">Inicio</a>
        <a class="link-underline" href="<?= e(url('tienda.php')) ?>">Tienda</a>
        <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Hombre') ?>">Men/Hombre</a>
        <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Mujer') ?>">Women/Mujer</a>
        <a class="link-underline" href="<?= e(url('tienda.php') . '?ofertas=1') ?>">Ofertas</a>
        <a class="link-underline" href="<?= e(url('contacto.php')) ?>">Contacto</a>
      </nav>
      <div class="flex items-center gap-3">
        <a href="<?= e(url('carrito.php')) ?>" class="relative inline-flex items-center gap-2 px-4 py-2 rounded-full border border-black/10 hover:border-black/20 transition">
          <span class="text-sm font-medium">Carrito</span>
          <span id="cart-count" class="min-w-6 h-6 px-2 rounded-full bg-black text-black text-xs font-semibold inline-flex items-center justify-center">0</span>
        </a>
        <?php if ($user): ?>
          <a href="<?= e(url('logout.php')) ?>" class="hidden sm:inline-flex px-5 py-2 rounded-full border border-black/10 hover:border-black/20 transition text-sm">Salir</a>
          <?php if (($user['rol'] ?? '') === 'admin'): ?>
            <a href="<?= e(url('admin/dashboard.php')) ?>" class="hidden sm:inline-flex px-5 py-2 rounded-full btn-primary text-sm">Admin</a>
          <?php endif; ?>
        <?php else: ?>
          <a href="<?= e(url('login.php')) ?>" class="hidden sm:inline-flex px-5 py-2 rounded-full btn-primary text-sm">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-5 py-10">
    <div class="flex items-end justify-between gap-6">
      <div>
        <h1 class="text-4xl font-extrabold tracking-tight">Carrito</h1>
        <div class="mt-2 text-white/60">Agrega, elimina o cambia cantidades. Total automático con JavaScript.</div>
      </div>
      <a href="<?= e(url('tienda.php')) ?>" class="hidden sm:inline-flex px-6 py-2 rounded-full border border-white/10 hover:border-white/20 transition font-semibold">Seguir comprando</a>
    </div>

    <div class="mt-10 text-black grid lg:grid-cols-[1fr_360px] gap-8 items-start">
      <div id="cart-items" class="grid gap-4"></div>
      <aside class="border border-black/10 rounded-3xl p-6 bg-white">
        <div class="text-xl text-black font-extrabold tracking-tight">Resumen</div>
        <div class="mt-6 flex items-center justify-between">
          <div class="text-black">Total</div>
          <div id="cart-total" class="text-2xl text-black font-extrabold">COP 0,00</div>
        </div>
        <div class="mt-6 grid gap-3">
          <a id="checkout-btn" href="<?= e(url('checkout.php')) ?>" class="text-center px-7 py-3 rounded-full btn-primary font-semibold ">Ir a checkout</a>
          <div class="text-xs text-black/60">Pago simulado. No se almacenan datos bancarios.</div>
        </div>
      </aside>
    </div>
  </main>

  <footer class="border-t border-black/10">
    <div class="mx-auto max-w-7xl px-5 py-12 grid md:grid-cols-3 gap-10">
      <div>
        <div class="font-extrabold tracking-tight text-lg">style <span class="text-accent">en</span> to<span class="text-danger">!</span></div>
        <div class="mt-3 text-white/60">Streetwear premium inspirado en performance.</div>
      </div>
      <div>
        <div class="font-semibold">Redes</div>
        <div class="mt-3 space-y-2 text-sm text-white/70">
          <a class="link-underline" href="#">Instagram</a><br />
          <a class="link-underline" href="#">TikTok</a><br />
          <a class="link-underline" href="#">YouTube</a>
        </div>
      </div>
      <div>
        <div class="font-semibold">Contacto</div>
        <div class="mt-3 text-sm text-white/70">
          <div>contacto@styleento.com</div>
          <div class="mt-1 text-white/70">+57 321 550 1870</div>
          <div class="mt-1 text-white/70">Ciudad Manizales</div>
        </div>
      </div>
    </div>
    <div class="border-t border-black/10 py-6 text-center text-xs text-white/60">© <?= date('Y') ?> styleento! — Todos los derechos reservados.</div>
  </footer>

  <script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>

