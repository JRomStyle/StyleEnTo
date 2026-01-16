<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$categoria = isset($_GET['categoria']) ? trim((string) $_GET['categoria']) : '';
$tipo = isset($_GET['tipo']) ? trim((string) $_GET['tipo']) : '';
$talla = isset($_GET['talla']) ? trim((string) $_GET['talla']) : '';
$min = isset($_GET['min']) ? (float) $_GET['min'] : null;
$max = isset($_GET['max']) ? (float) $_GET['max'] : null;
$ofertas = isset($_GET['ofertas']) ? (int) $_GET['ofertas'] : 0;
$nuevo = isset($_GET['nuevo']) ? (int) $_GET['nuevo'] : 0;
$sort = isset($_GET['sort']) ? trim((string) $_GET['sort']) : '';

$where = ['activo = 1'];
$params = [];

if (in_array($categoria, ['Hombre', 'Mujer', 'Unisex'], true)) {
    $where[] = 'categoria = :categoria';
    $params[':categoria'] = $categoria;
}
if ($q !== '') {
    $where[] = 'nombre LIKE :q';
    $params[':q'] = '%' . $q . '%';
}
if ($tipo !== '') {
    $where[] = 'tipo = :tipo';
    $params[':tipo'] = $tipo;
}
if ($talla !== '') {
    $where[] = 'FIND_IN_SET(:talla, REPLACE(tallas_csv, " ", "")) > 0';
    $params[':talla'] = $talla;
}
if ($min !== null && $min >= 0) {
    $where[] = 'precio >= :min';
    $params[':min'] = $min;
}
if ($max !== null && $max > 0) {
    $where[] = 'precio <= :max';
    $params[':max'] = $max;
}

$allowedSort = ['destacados', 'vendidos', 'nuevo', 'precio_asc', 'precio_desc'];
if (!in_array($sort, $allowedSort, true)) {
    $sort = '';
}

if ($sort === 'destacados') {
    $sort = 'vendidos';
}

if ($sort === '') {
    if ($ofertas === 1) {
        $sort = 'precio_asc';
    } elseif ($nuevo === 1) {
        $sort = 'nuevo';
    } else {
        $sort = 'vendidos';
    }
}

if ($ofertas === 1) {
    $where[] = 'precio <= 49.90';
}

$order = match ($sort) {
    'precio_asc' => 'ORDER BY precio ASC, id DESC',
    'precio_desc' => 'ORDER BY precio DESC, id DESC',
    'nuevo' => 'ORDER BY creado_en DESC, id DESC',
    default => 'ORDER BY ventas DESC, id DESC',
};

$sql = 'SELECT id, nombre, precio, imagen_url, categoria, tipo, tallas_csv, ventas, creado_en FROM productos WHERE ' . implode(' AND ', $where) . " {$order}";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

$tipos = db()->query('SELECT DISTINCT tipo FROM productos WHERE activo = 1 ORDER BY tipo ASC')->fetchAll();
$user = current_user();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="app-base" content="<?= e(rtrim(base_url(), '/')) ?>" />
  <title>Tienda — style en to!</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { accent: "#00ff85", danger: "#ff2d2d" } } } }
  </script>
<link rel="stylesheet" href="<?= e(url('assets/css/tailwind.css')) ?>" />
</head>
<body class="bg-white text-black">
  <header class="sticky top-0 z-40 glass">
    <div class="border-b border-black/10">
      <div class="mx-auto max-w-7xl px-5 h-9 flex items-center justify-between text-xs">
        <span class="inline-flex items-center gap-2">
          <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
          Members: envío gratis en pedidos +50€
        </span>
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
          <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Hombre') ?>">Men</a>
          <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Mujer') ?>">Women</a>
          <a class="link-underline" href="<?= e(url('tienda.php') . '?categoria=Unisex') ?>">Unisex</a>
          <a class="link-underline" href="<?= e(url('tienda.php') . '?ofertas=1') ?>">Ofertas</a>
        </nav>

        <div class="flex items-center gap-3">
          <form class="hidden md:block" method="get" action="<?= e(url('tienda.php')) ?>">
            <input type="hidden" name="categoria" value="<?= e($categoria) ?>" />
            <input type="hidden" name="tipo" value="<?= e($tipo) ?>" />
            <input type="hidden" name="talla" value="<?= e($talla) ?>" />
            <input type="hidden" name="min" value="<?= $min !== null ? e((string) $min) : '' ?>" />
            <input type="hidden" name="max" value="<?= $max !== null ? e((string) $max) : '' ?>" />
            <input type="hidden" name="ofertas" value="<?= $ofertas === 1 ? '1' : '' ?>" />
            <input type="hidden" name="nuevo" value="<?= $nuevo === 1 ? '1' : '' ?>" />
            <input type="hidden" name="sort" value="<?= e($sort) ?>" />
            <div class="relative">
              <input name="q" value="<?= e($q) ?>" class="w-64 lg:w-80 rounded-full border border-black/10 bg-white/50 px-10 py-2 text-sm outline-none focus:border-black/30" placeholder="Buscar" />
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

  <main class="mx-auto max-w-7xl px-5 py-8">
    <div class="flex flex-col gap-6">
      <div class="flex items-end justify-between gap-6">
        <div>
          <h1 class="text-5xl font-extrabold tracking-tight leading-none">Tienda</h1>
          <div class="mt-2 text-black/60"><?= count($productos) ?> resultados</div>
        </div>
        <div class="flex items-center gap-3">
          <button type="button" data-toggle-filters class="inline-flex items-center gap-2 px-5 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold text-sm">
            <span>Ocultar filtros</span>
            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 6h16" />
              <path d="M7 12h10" />
              <path d="M10 18h4" />
            </svg>
          </button>

          <form method="get" action="<?= e(url('tienda.php')) ?>">
            <input type="hidden" name="q" value="<?= e($q) ?>" />
            <input type="hidden" name="categoria" value="<?= e($categoria) ?>" />
            <input type="hidden" name="tipo" value="<?= e($tipo) ?>" />
            <input type="hidden" name="talla" value="<?= e($talla) ?>" />
            <input type="hidden" name="min" value="<?= $min !== null ? e((string) $min) : '' ?>" />
            <input type="hidden" name="max" value="<?= $max !== null ? e((string) $max) : '' ?>" />
            <input type="hidden" name="ofertas" value="<?= $ofertas === 1 ? '1' : '' ?>" />
            <input type="hidden" name="nuevo" value="<?= $nuevo === 1 ? '1' : '' ?>" />
            <select name="sort" class="rounded-full border border-black/10 bg-white/50 px-5 py-2 text-sm outline-none focus:border-black/30" onchange="this.form.submit()">
              <option value="vendidos" <?= $sort === 'vendidos' ? 'selected' : '' ?>>Ordenar por: Más vendidos</option>
              <option value="nuevo" <?= $sort === 'nuevo' ? 'selected' : '' ?>>Lo nuevo</option>
              <option value="precio_asc" <?= $sort === 'precio_asc' ? 'selected' : '' ?>>Precio: más bajo</option>
              <option value="precio_desc" <?= $sort === 'precio_desc' ? 'selected' : '' ?>>Precio: más alto</option>
            </select>
          </form>
        </div>
      </div>

      <div id="filters-overlay" class="hidden fixed inset-0 z-50">
        <button type="button" data-close-filters class="absolute inset-0 bg-black/60"></button>
        <aside id="filters" class="absolute left-0 top-0 h-full w-[340px] max-w-[88vw] overflow-auto bg-white border-r border-black/10 p-6">
          <div class="flex items-center justify-between gap-4">
            <div class="text-2xl font-extrabold tracking-tight">Filtros</div>
            <button type="button" data-close-filters class="w-11 h-11 rounded-full border border-black/10 hover:border-black/20 transition inline-flex items-center justify-center" aria-label="Cerrar">
              <span class="text-xl leading-none">×</span>
            </button>
          </div>

          <form class="mt-6 space-y-4" method="get" action="<?= e(url('tienda.php')) ?>">
            <input type="hidden" name="q" value="<?= e($q) ?>" />
            <input type="hidden" name="sort" value="<?= e($sort) ?>" />
            <div>
              <label class="text-sm font-semibold">Categoría</label>
              <select name="categoria" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30">
                <option value="">Todas</option>
                <option value="Hombre" <?= $categoria === 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                <option value="Mujer" <?= $categoria === 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                <option value="Unisex" <?= $categoria === 'Unisex' ? 'selected' : '' ?>>Unisex</option>
              </select>
            </div>
            <div>
              <label class="text-sm font-semibold">Tipo</label>
              <select name="tipo" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30">
                <option value="">Todos</option>
                <?php foreach ($tipos as $t): ?>
                  <?php $v = (string) ($t['tipo'] ?? ''); ?>
                  <option value="<?= e($v) ?>" <?= $tipo === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="text-sm font-semibold">Talla</label>
              <input name="talla" value="<?= e($talla) ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="Ej: M o 42" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-sm font-semibold">Precio mín</label>
                <input name="min" value="<?= $min !== null ? e((string) $min) : '' ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" inputmode="decimal" placeholder="0" />
              </div>
              <div>
                <label class="text-sm font-semibold">Precio máx</label>
                <input name="max" value="<?= $max !== null ? e((string) $max) : '' ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" inputmode="decimal" placeholder="120" />
              </div>
            </div>
            <div class="flex items-center gap-3">
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="ofertas" value="1" <?= $ofertas === 1 ? 'checked' : '' ?> />
                Ofertas
              </label>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="nuevo" value="1" <?= $nuevo === 1 ? 'checked' : '' ?> />
                Nuevo
              </label>
            </div>
            <div class="pt-2 flex gap-3">
              <button class="px-6 py-3 rounded-full btn-primary font-semibold" type="submit">Aplicar</button>
              <a class="px-6 py-3 rounded-full border border-black/10 hover:border-black/20 transition font-semibold" href="<?= e(url('tienda.php')) ?>">Reset</a>
            </div>
          </form>
        </div>
      </div>

      <div class="flex gap-8 items-start">
        <aside class="hidden lg:block w-72 shrink-0" data-desktop-filters>
          <div class="border border-black/10 rounded-[2rem] p-5 bg-white">
            <div class="text-xl font-extrabold tracking-tight">Filtros</div>
            <form class="mt-5 space-y-4" method="get" action="<?= e(url('tienda.php')) ?>">
              <input type="hidden" name="q" value="<?= e($q) ?>" />
              <input type="hidden" name="sort" value="<?= e($sort) ?>" />
              <div>
                <label class="text-sm font-semibold">Categoría</label>
                <select name="categoria" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30">
                  <option value="">Todas</option>
                  <option value="Hombre" <?= $categoria === 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                  <option value="Mujer" <?= $categoria === 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                  <option value="Unisex" <?= $categoria === 'Unisex' ? 'selected' : '' ?>>Unisex</option>
                </select>
              </div>
              <div>
                <label class="text-sm font-semibold">Tipo</label>
                <select name="tipo" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30">
                  <option value="">Todos</option>
                  <?php foreach ($tipos as $t): ?>
                    <?php $v = (string) ($t['tipo'] ?? ''); ?>
                    <option value="<?= e($v) ?>" <?= $tipo === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="text-sm font-semibold">Talla</label>
                <input name="talla" value="<?= e($talla) ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="Ej: M o 42" />
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="text-sm font-semibold">Precio mín</label>
                  <input name="min" value="<?= $min !== null ? e((string) $min) : '' ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" inputmode="decimal" placeholder="0" />
                </div>
                <div>
                  <label class="text-sm font-semibold">Precio máx</label>
                  <input name="max" value="<?= $max !== null ? e((string) $max) : '' ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" inputmode="decimal" placeholder="120" />
                </div>
              </div>
              <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm">
                  <input type="checkbox" name="ofertas" value="1" <?= $ofertas === 1 ? 'checked' : '' ?> />
                  Ofertas
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                  <input type="checkbox" name="nuevo" value="1" <?= $nuevo === 1 ? 'checked' : '' ?> />
                  Nuevo
                </label>
              </div>
              <div class="pt-2 flex gap-3">
                <button class="px-6 py-3 rounded-full btn-primary font-semibold" type="submit">Aplicar</button>
                <a class="px-6 py-3 rounded-full border border-black/10 hover:border-black/20 transition font-semibold" href="<?= e(url('tienda.php')) ?>">Reset</a>
              </div>
            </form>
          </div>
        </aside>

        <section class="flex-1 min-w-0">
          <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
          <?php foreach ($productos as $p): ?>
            <?php
              $isOffer = $ofertas === 1 || (float) $p['precio'] <= 49.90;
              $created = isset($p['creado_en']) ? strtotime((string) $p['creado_en']) : 0;
              $isNew = $nuevo === 1 || ($created > 0 && $created >= strtotime('-30 days'));
              $label = $isNew ? 'Lo nuevo' : ($isOffer ? 'Oferta' : '');
            ?>
            <a href="<?= e(url('producto.php') . '?id=' . (int) $p['id']) ?>" class="group block" data-reveal>
              <div class="relative overflow-hidden rounded-[2rem] border border-black/10 bg-white">
                <div class="aspect-[4/5] overflow-hidden">
                  <img class="w-full h-full object-cover opacity-95 group-hover:scale-[1.02] transition duration-300" src="<?= e(url('assets/img/' . ltrim((string) $p['imagen_url'], '/'))) ?>" alt="<?= e($p['nombre']) ?>" />
                </div>
                <div class="absolute inset-x-0 top-0 p-4 flex items-center justify-between gap-3">
                  <?php if ($label !== ''): ?>
                    <span class="inline-flex px-3 py-1 rounded-full bg-black/70 text-white text-xs font-semibold border border-white/10"><?= e($label) ?></span>
                  <?php else: ?>
                    <span></span>
                  <?php endif; ?>
                  <button type="button"
                    class="w-11 h-11 rounded-full border border-black/10 bg-white/70 hover:border-black/20 transition inline-flex items-center justify-center"
                    data-add-to-cart
                    data-id="<?= (int) $p['id'] ?>"
                    data-name="<?= e($p['nombre']) ?>"
                    data-price="<?= e((string) $p['precio']) ?>"
                    data-image="<?= e(url('assets/img/' . ltrim((string) $p['imagen_url'], '/'))) ?>"
                    data-size="U"
                    aria-label="Agregar al carrito"
                  >
                    <span class="text-lg leading-none">+</span>
                  </button>
                </div>
                <div class="p-5">
                  <div class="font-semibold truncate"><?= e($p['nombre']) ?></div>
                  <div class="mt-1 text-sm text-black/60"><?= e($p['categoria']) ?> · <?= e($p['tipo']) ?></div>
                  <div class="mt-2 font-semibold">€<?= number_format((float) $p['precio'], 2) ?></div>
                  <div class="mt-4 text-xs text-black/60 truncate">Tallas: <?= e($p['tallas_csv']) ?></div>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
          <?php if (count($productos) === 0): ?>
            <div class="border border-black/10 rounded-2xl p-8 bg-white">
              <div class="text-xl font-semibold">Sin resultados</div>
              <div class="mt-2 text-black/60">Ajusta filtros o prueba otra categoría.</div>
            </div>
          <?php endif; ?>
          </div>
        </section>
      </div>
    </div>
  </main>

  <footer class="border-t border-black/10">
    <div class="mx-auto max-w-7xl px-5 py-10 text-sm text-black/60 flex flex-col sm:flex-row items-center justify-between gap-3">
      <div>© <?= date('Y') ?> style en to!</div>
      <div class="flex items-center gap-5">
        <a class="link-underline" href="<?= e(url('contacto.php')) ?>">Contacto</a>
        <a class="link-underline" href="<?= e(url('tienda.php') . '?ofertas=1') ?>">Ofertas</a>
      </div>
    </div>
  </footer>

  <script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
