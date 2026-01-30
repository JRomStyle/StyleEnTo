<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

$stmtBest = db()->query("SELECT id, nombre, precio, imagen_url FROM productos WHERE activo = 1 ORDER BY ventas DESC, id DESC LIMIT 4");
$best = $stmtBest->fetchAll();

$stmtNew = db()->query("SELECT id, nombre, precio, imagen_url FROM productos WHERE activo = 1 ORDER BY creado_en DESC, id DESC LIMIT 4");
$new = $stmtNew->fetchAll();

$user = current_user();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="app-base" content="<?= e(rtrim(base_url(), '/')) ?>" />
  <title>StyleEnTo!</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { accent: "#00ff85", danger: "#ff2d2d" } } } }
  </script>
  <link rel="stylesheet" href="<?= e(url('assets/css/tailwind.css')) ?>" />
</head>
<body class="bg-white text-white">
  <header class="sticky top-0 z-40 glass">
    <div class="border-b border-black/10">
      <div class="mx-auto max-w-7xl px-5 h-9 flex items-center justify-between text-xs">
        <div class="flex items-center gap-3">
          <span class="inline-flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
            Envío gratis en pedidos +50,00 COP
          </span>
        </div>
        <div class="flex items-center gap-4">
          <a class="link-underline" href="<?= e(url('contacto.php')) ?>">Ayuda</a>
          <a class="link-underline" href="<?= e(url('registro.php')) ?>">Únete</a>
          <?php if ($user): ?>
            <a class="link-underline" href="<?= e(url('logout.php')) ?>">Salir</a>
          <?php else: ?>
            <a class="link-underline" href="<?= e(url('login.php')) ?>">Iniciar sesión</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="border-b border-black/10">
      <div class="mx-auto max-w-7xl px-5 h-16 flex items-center justify-between gap-4">
        <a href="<?= e(url('index.php')) ?>" class="font-extrabold tracking-tight text-xl">
          style <span class="text-accent">en</span> to<span class="text-danger">!</span>
        </a>

        <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold">
          <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Hombre') ?>">Men/Hombre</a>
          <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Mujer') ?>">Women/Mujer</a>
          <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Unisex') ?>">Unisex</a>
          <a class="link-underline" href="<?= e(url('tienda.php') . '?ofertas=1') ?>">Ofertas</a>
        </nav>

        <div class="flex items-center gap-3">
          <form class="hidden md:block" method="get" action="<?= e(url('tienda.php')) ?>">
            <div class="relative">
              <input name="q" class="w-64 lg:w-80 rounded-full border border-black/10 bg-white/50 px-10 py-2 text-sm outline-none focus:border-black/30" placeholder="Buscar" />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 21l-4.3-4.3" />
                <circle cx="11" cy="11" r="7" />
              </svg>
            </div>
          </form>

          <a href="<?= e(url('carrito.php')) ?>" class="relative inline-flex items-center justify-center w-11 h-11 rounded-full border border-black/10 hover:border-black/20 transition" aria-label="Carrito">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 6h15l-1.5 9h-12z" />
              <path d="M6 6l-2-3H1" />
              <circle cx="9" cy="20" r="1" />
              <circle cx="18" cy="20" r="1" />
            </svg>
            <span id="cart-count" class="absolute -top-1 -right-1 min-w-6 h-6 px-2 rounded-full bg-black text-white text-xs font-semibold inline-flex items-center justify-center">0</span>
          </a>

          <?php if ($user && ($user['rol'] ?? '') === 'admin'): ?>
            <a href="<?= e(url('admin/dashboard.php')) ?>" class="hidden sm:inline-flex px-5 py-2 rounded-full btn-primary text-sm font-semibold">Admin</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <main>
    <section class="mx-auto max-w-7xl px-5 pt-6">
      <div class="relative overflow-hidden rounded-[2.75rem] border border-black/10">
        <div class="absolute inset-0">
          <img class="w-full h-[440px] md:h-[520px] object-cover opacity-90" src="<?= e(url('assets/img/herocitymode.jpg')) ?>" alt="" />
          <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
        </div>
        <div class="relative p-8 md:p-12 max-w-2xl" data-reveal>
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/15 bg-white/5 text-xs font-semibold tracking-wide uppercase text-white">
            Drop <span class="w-1.5 h-1.5 rounded-full bg-accent"></span> Underground 2026
          </div>
          <h1 class="mt-6 text-6xl md:text-7xl font-extrabold leading-[0.95] text-white">
            HIGH VIBE FREQUENCY
          </h1>
          <p class="mt-4 text-white/80 text-lg max-w-xl">
            Todo unico, energía de barrio y flow artista hecho para asfalto, noche y música.
          </p>
          <div class="mt-7 flex flex-wrap gap-3">
            <a href="<?= e(url('tienda.php') . '?nuevo=1') ?>" class="inline-flex px-8 py-3 rounded-full btn-accent font-extrabold">Comprar lo nuevo</a>
            <a href="<?= e(url('tienda.php')) ?>" class="inline-flex px-8 py-3 rounded-full btn-primary font-semibold">Explorar tienda</a>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-10">
      <div class="flex items-end justify-between gap-6">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight">Apartados</h2>
          <p class="mt-2 text-white/60">Elige tu mood: calzado, ropa o accesorios.</p>
        </div>
        <a href="<?= e(url('tienda.php')) ?>" class="hidden sm:inline-flex px-6 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold">Ver todo</a>
      </div>
      <div class="mt-6 grid md:grid-cols-3 gap-5">
        <a href="<?= e(url('tienda.php') . '?tipo=tenis') ?>" class="group relative overflow-hidden rounded-[2rem] border border-black/10 bg-white">
          <img class="w-full h-64 object-cover opacity-85 group-hover:scale-[1.02] transition duration-300" src="<?= e(url('assets/img/cattenis.jpg')) ?>" alt="" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
          <div class="absolute left-6 bottom-6">
            <div class="text-white/80 text-xs uppercase tracking-widest font-semibold">Calzado</div>
            <div class="text-white text-3xl font-extrabold">Tenis</div>
            <div class="mt-3 inline-flex px-6 py-2 rounded-full bg-white text-black text-sm font-semibold">Comprar</div>
          </div>
        </a>
        <a href="<?= e(url('tienda.php') . '?tipo=hoodies') ?>" class="group relative overflow-hidden rounded-[2rem] border border-black/10 bg-white">
          <img class="w-full h-64 object-cover opacity-85 group-hover:scale-[1.02] transition duration-300" src="<?= e(url('assets/img/cathoodies.jpg')) ?>" alt="" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
          <div class="absolute left-6 bottom-6">
            <div class="text-white/80 text-xs uppercase tracking-widest font-semibold">Ropa</div>
            <div class="text-white text-3xl font-extrabold">Hoodies</div>
            <div class="mt-3 inline-flex px-6 py-2 rounded-full bg-white text-black text-sm font-semibold">Comprar</div>
          </div>
        </a>
        <a href="<?= e(url('tienda.php') . '?tipo=gorras') ?>" class="group relative overflow-hidden rounded-[2rem] border border-black/10 bg-white">
          <img class="w-full h-64 object-cover opacity-85 group-hover:scale-[1.02] transition duration-300" src="<?= e(url('assets/img/catgorras.jpg')) ?>" alt="" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
          <div class="absolute left-6 bottom-6">
            <div class="text-white/80 text-xs uppercase tracking-widest font-semibold">Accesorios</div>
            <div class="text-white text-3xl font-extrabold">Gorras</div>
            <div class="mt-3 inline-flex px-6 py-2 rounded-full bg-white text-black text-sm font-semibold">Comprar</div>
          </div>
        </a>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14">
      <div class="flex items-end justify-between gap-6">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight">Lo más vendido</h2>
          <p class="mt-2 text-white/60">Selección rápida con piezas que siempre vuelan.</p>
        </div>
        <a href="<?= e(url('tienda.php')) ?>" class="hidden sm:inline-flex px-6 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold">Ver todo</a>
      </div>
      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php foreach ($best as $p): ?>
          <a href="<?= e(url('producto.php') . '?id=' . (int) $p['id']) ?>" class="group border border-black/10 rounded-2xl overflow-hidden bg-white hover:shadow-xl transition" data-reveal>
            <div class="aspect-[4/5] overflow-hidden">
              <img class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-300" src="<?= e(image_src((string) $p['imagen_url'])) ?>" alt="<?= e($p['nombre']) ?>" />
            </div>
            <div class="p-4">
              <div class="mt-1 text-black font-semibold truncate"><?= e($p['nombre']) ?></div>
              <div class="mt-1 text-black/70 font-medium">COP <?= number_format((float) $p['precio'], 2, ',', '.') ?></div>
              <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold">
                <span class="text-black link-underline">Ver detalle</span>
                <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14">
      <div class="flex items-end justify-between gap-6">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight">Nuevos lanzamientos</h2>
          <p class="mt-2 text-white/60">Lo último en drops: limpio, fuerte y listo para la calle.</p>
        </div>
        <a href="<?= e(url('tienda.php') . '?nuevo=1') ?>" class="hidden sm:inline-flex px-6 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold">Explorar</a>
      </div>
      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php foreach ($new as $p): ?>
          <div class="border border-black/10 rounded-2xl overflow-hidden bg-white" data-reveal>
            <a href="<?= e(url('producto.php') . '?id=' . (int) $p['id']) ?>" class="block group">
              <div class="aspect-[4/5] overflow-hidden">
                <img class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-300" src="<?= e(image_src((string) $p['imagen_url'])) ?>" alt="<?= e($p['nombre']) ?>" />
              </div>
              <div class="p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <div class="mt-1 text-black font-semibold truncate"><?= e($p['nombre']) ?></div>
                  <div class="mt-1 text-black/70 font-medium">COP <?= number_format((float) $p['precio'], 2, ',', '.') ?></div>
                </div>
                  <button type="button"
                  class="shrink-0 w-11 h-11 rounded-full border border-black/80 hover:border-black/80 transition flex items-center justify-center text-black"
                  data-add-to-cart
                  data-id="<?= (int) $p['id'] ?>"
                  data-name="<?= e($p['nombre']) ?>"
                  data-price="<?= e((string) $p['precio']) ?>"
                  data-image="<?= e(image_src((string) $p['imagen_url'])) ?>"
                  data-size="U"
                >
                  <span class="text-lg leading-none">+</span>
                </button>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 pb-16">
      <div class="relative overflow-hidden rounded-[2.5rem] border border-black/10 bg-black text-white">
        <div class="absolute inset-0 opacity-60">
          <img class="w-full h-full object-cover" src="<?= e(url('assets/img/banner-ofertas.jpg')) ?>" alt="Banner" />
        </div>
        <div class="relative p-10 md:p-14 grid md:grid-cols-2 gap-10 items-center">
          <div data-reveal>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/15 text-xs font-semibold tracking-wide uppercase">
              Oferta limitada <span class="w-1.5 h-1.5 rounded-full bg-accent"></span> -20%
            </div>
            <h3 class="mt-6 text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.02]">Corre por el drop.</h3>
            <p class="mt-4 text-white/80 text-lg">Piezas seleccionadas con descuento. Estética limpia, energía fuerte.</p>
          </div>
          <div class="flex md:justify-end" data-reveal>
            <a href="<?= e(url('tienda.php') . '?ofertas=1') ?>" class="inline-flex px-8 py-3 rounded-full btn-accent font-extrabold">Comprar ofertas</a>
          </div>
        </div>
      </div>
    </section>
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
