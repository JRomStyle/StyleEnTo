<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

$user = current_user();
$ok = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
        if ($nombre === '' || $email === '' || $mensaje === '') {
            $error = 'Completa nombre, email y mensaje.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
        } else {
            $ok = true;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="app-base" content="<?= e(rtrim(base_url(), '/')) ?>" />
  <title>Contacto — style en to!</title>
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
          <span id="cart-count" class="min-w-6 h-6 px-2 rounded-full bg-black text-white text-xs font-semibold inline-flex items-center justify-center">0</span>
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
    <div class="grid lg:grid-cols-2 gap-10 items-start">
      <div data-reveal>
        <h1 class="text-4xl font-extrabold tracking-tight">Contacto</h1>
        <p class="mt-3 text-white/60 max-w-xl">¿Tienes una duda, pedido o colaboración? Escríbenos y te respondemos.</p>
        <div class="mt-8 border border-black/10 rounded-3xl p-6 bg-white">
          <div class="text-sm text-black/70">
            <div class="font-semibold">Email</div>
            <div>contacto@styleento.com</div>
            <div class="mt-5 font-semibold">Horario</div>
            <div>Lun–Vie, 10:00–18:00</div>
            <div class="mt-5 font-semibold">Ubicación</div>
            <div>Manizales, Colombia</div>
          </div>
        </div>
      </div>

      <section class="border border-black/10 rounded-3xl p-6 bg-white" data-reveal>
        <div class="text-xl font-extrabold tracking-tight text-black">Enviar mensaje</div>
        <?php if ($ok): ?>
          <div class="mt-6 border border-accent/40 bg-accent/10 rounded-2xl p-4 text-sm">Mensaje enviado. Te contactamos pronto.</div>
          <a href="<?= e(url('index.php')) ?>" class="mt-6 inline-flex px-7 py-3 rounded-full btn-primary font-semibold text-white">Volver al inicio</a>
        <?php else: ?>
          <?php if ($error !== ''): ?>
            <div class="mt-6 border border-danger/30 bg-danger/5 rounded-2xl p-4 text-sm"><?= e($error) ?></div>
          <?php endif; ?>

          <form class="mt-6 grid gap-4" method="post" action="<?= e(url('contacto.php')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-semibold text-black">Nombre</label>
                <input name="nombre" value="<?= e($user['nombre'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required />
              </div>
              <div>
                <label class="text-sm font-semibold text-black">Email</label>
                <input name="email" type="email" value="<?= e($user['email'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required />
              </div>
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Mensaje</label>
              <textarea name="mensaje" rows="5" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="Cuéntanos en qué podemos ayudarte..." required></textarea>
            </div>
            <button class="mt-2 px-7 py-3 rounded-full btn-primary font-semibold" type="submit">Enviar</button>
          </form>
        <?php endif; ?>
      </section>
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
        <div class="mt-3 space-y-2 text-sm text-white/60">
          <a class="link-underline" href="#">Instagram</a><br />
          <a class="link-underline" href="#">TikTok</a><br />
          <a class="link-underline" href="#">YouTube</a>
        </div>
      </div>
      <div>
        <div class="font-semibold">Contacto</div>
        <div class="mt-3 text-sm text-white/60">
          <div>admin@styleento.com</div>
          <div class="mt-1">+57 321 550 1870</div>
          <div class="mt-1">Ciudad Manizales</div>
        </div>
      </div>
    </div>
    <div class="border-t border-black/10 py-6 text-center text-xs text-white/60">© <?= date('Y') ?> styleento! — Todos los derechos reservados.</div>
  </footer>

  <script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
