<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../config/db.php';
require_admin();

function slugify(string $text): string
{
    $lower = function_exists('mb_strtolower') ? mb_strtolower($text) : strtolower($text);
    $text = trim($lower);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text) ?? '';
    $text = trim($text, '-');
    $text = preg_replace('~[^-\w]+~', '', $text) ?? '';
    return $text !== '' ? $text : 'producto';
}

$flash = '';
$error = '';

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int) $_GET['delete'] : 0;

if ($deleteId > 0 && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $del = db()->prepare('DELETE FROM productos WHERE id = :id');
        $del->execute([':id' => $deleteId]);
        $flash = 'Producto eliminado.';
        $deleteId = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $precio = (float) ($_POST['precio'] ?? 0);
        $categoria = (string) ($_POST['categoria'] ?? '');
        $tipo = trim((string) ($_POST['tipo'] ?? ''));
        $tallas = trim((string) ($_POST['tallas_csv'] ?? ''));
        $imagen = trim((string) ($_POST['imagen_url'] ?? ''));
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '' || $descripcion === '' || $tipo === '' || $tallas === '' || $imagen === '') {
            $error = 'Completa nombre, descripción, tipo, tallas e imagen.';
        } elseif (!in_array($categoria, ['Hombre', 'Mujer', 'Unisex'], true)) {
            $error = 'Categoría inválida.';
        } elseif ($precio <= 0) {
            $error = 'Precio inválido.';
        } else {
            $slugBase = slugify($nombre);
            $slug = $slugBase;
            $i = 1;
            while (true) {
                $stmt = db()->prepare('SELECT id FROM productos WHERE slug = :slug LIMIT 1');
                $stmt->execute([':slug' => $slug]);
                $row = $stmt->fetch();
                if (!$row || ($id > 0 && (int) $row['id'] === $id)) {
                    break;
                }
                $i++;
                $slug = $slugBase . '-' . $i;
            }

            if ($id > 0) {
                $upd = db()->prepare('UPDATE productos SET nombre = :nombre, slug = :slug, descripcion = :descripcion, precio = :precio, categoria = :categoria, tipo = :tipo, tallas_csv = :tallas, imagen_url = :imagen, activo = :activo WHERE id = :id');
                $upd->execute([
                    ':nombre' => $nombre,
                    ':slug' => $slug,
                    ':descripcion' => $descripcion,
                    ':precio' => $precio,
                    ':categoria' => $categoria,
                    ':tipo' => $tipo,
                    ':tallas' => $tallas,
                    ':imagen' => $imagen,
                    ':activo' => $activo,
                    ':id' => $id,
                ]);
                $flash = 'Producto actualizado.';
            } else {
                $ins = db()->prepare('INSERT INTO productos (nombre, slug, descripcion, precio, categoria, tipo, tallas_csv, imagen_url, activo) VALUES (:nombre, :slug, :descripcion, :precio, :categoria, :tipo, :tallas, :imagen, :activo)');
                $ins->execute([
                    ':nombre' => $nombre,
                    ':slug' => $slug,
                    ':descripcion' => $descripcion,
                    ':precio' => $precio,
                    ':categoria' => $categoria,
                    ':tipo' => $tipo,
                    ':tallas' => $tallas,
                    ':imagen' => $imagen,
                    ':activo' => $activo,
                ]);
                $flash = 'Producto creado.';
            }
            $editId = 0;
        }
    }
}

$edit = null;
if ($editId > 0) {
    $stmt = db()->prepare('SELECT * FROM productos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $edit = $stmt->fetch() ?: null;
}

$productos = db()->query('SELECT id, nombre, precio, categoria, tipo, activo, creado_en FROM productos ORDER BY id DESC')->fetchAll();
$tiposDisponibles = db()->query('SELECT DISTINCT tipo FROM productos ORDER BY tipo ASC')->fetchAll();

$imgDir = __DIR__ . '/../assets/img/';
$imagenes = [];
if (is_dir($imgDir)) {
    foreach (scandir($imgDir) as $file) {
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])) {
            $imagenes[] = $file;
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
  <title>Admin — Productos</title>
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
        <a class="link-underline" href="<?= e(url('admin/dashboard.php')) ?>">Dashboard</a>
        <a class="link-underline" href="<?= e(url('admin/productos.php')) ?>">Productos</a>
        <a class="link-underline" href="<?= e(url('tienda.php')) ?>">Ver tienda</a>
      </nav>
      <div class="flex items-center gap-3">
        <a href="<?= e(url('logout.php')) ?>" class="inline-flex px-5 py-2 rounded-full border border-black/10 hover:border-black/20 transition text-sm">Salir</a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-5 py-10">
    <div class="flex flex-col lg:flex-row gap-10">
      <section class="lg:w-[460px] shrink-0">
        <div class="border border-black/10 rounded-[2.5rem] p-6 bg-white" data-reveal>
          <div class="flex items-end justify-between gap-4">
            <div>
              <div class="mt-1 text-2xl font-extrabold tracking-tight text-black"><?= $edit ? 'Editar producto' : 'Nuevo producto' ?></div>
              <div class="mt-1 text-black/60 text-sm">Imágenes por URL (puedes usar Unsplash o tus assets).</div>
            </div>
            <?php if ($edit): ?>
              <a href="<?= e(url('admin/productos.php')) ?>" class="px-5 py-2 rounded-full border border-black/10 hover:border-black/20 transition text-sm font-semibold text-black">Nuevo</a>
            <?php endif; ?>
          </div>

          <?php if ($flash !== ''): ?>
            <div class="mt-6 border border-accent/40 bg-accent/10 rounded-2xl p-4 text-sm"><?= e($flash) ?></div>
          <?php endif; ?>
          <?php if ($error !== ''): ?>
            <div class="mt-6 border border-danger/30 bg-danger/5 rounded-2xl p-4 text-sm"><?= e($error) ?></div>
          <?php endif; ?>

          <form class="mt-6 grid gap-4" method="post" action="<?= e(url('admin/productos.php') . ($edit ? '?edit=' . (int) $edit['id'] : '')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
            <input type="hidden" name="action" value="save" />
            <input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>" />

            <div>
              <label class="text-sm font-semibold text-black">Nombre</label>
              <input name="nombre" value="<?= e((string) ($edit['nombre'] ?? '')) ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required />
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Precio (COP)</label>
              <input name="precio" value="<?= e((string) ($edit['precio'] ?? '')) ?>" inputmode="decimal" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required />
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Categoría</label>
              <?php $cat = (string) ($edit['categoria'] ?? 'Unisex'); ?>
              <select name="categoria" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required>
                <option value="Hombre" <?= $cat === 'Hombre' ? 'selected' : '' ?>>Hombre</option>
                <option value="Mujer" <?= $cat === 'Mujer' ? 'selected' : '' ?>>Mujer</option>
                <option value="Unisex" <?= $cat === 'Unisex' ? 'selected' : '' ?>>Unisex</option>
              </select>
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Tipo</label>
              <input name="tipo" list="list-tipos" value="<?= e((string) ($edit['tipo'] ?? '')) ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="hoodies, camisas, tenis..." required />
              <datalist id="list-tipos">
                <?php foreach ($tiposDisponibles as $t): ?>
                  <option value="<?= e((string) $t['tipo']) ?>"></option>
                <?php endforeach; ?>
              </datalist>
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Tallas (CSV)</label>
              <input name="tallas_csv" value="<?= e((string) ($edit['tallas_csv'] ?? '')) ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="XS,S,M..." required />
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Imagen (assets/img)</label>
              <input name="imagen_url" list="list-imagenes" value="<?= e((string) ($edit['imagen_url'] ?? '')) ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="Selecciona o escribe..." required />
              <datalist id="list-imagenes">
                <?php foreach ($imagenes as $img): ?>
                  <option value="<?= e($img) ?>"></option>
                <?php endforeach; ?>
              </datalist>
            </div>
            <div>
              <label class="text-sm font-semibold text-black">Descripción</label>
              <textarea name="descripcion" rows="5" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required><?= e((string) ($edit['descripcion'] ?? '')) ?></textarea>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-black">
              <input type="checkbox" name="activo" value="1" <?= !isset($edit['activo']) || (int) $edit['activo'] === 1 ? 'checked' : '' ?> />
              Activo
            </label>
            <button class="mt-2 px-7 py-3 rounded-full btn-primary font-semibold" type="submit"><?= $edit ? 'Guardar cambios' : 'Crear producto' ?></button>
          </form>
        </div>
      </section>

      <section class="flex-1">
        <div class="flex items-end justify-between gap-6">
          <div>
            <h1 class="text-4xl font-extrabold tracking-tight">Productos</h1>
            <div class="mt-2 text-white/60" ><?= count($productos) ?> en catálogo</div>
          </div>
        </div>

        <div class="mt-8 border border-black/10 rounded-[2.5rem] p-6 bg-white" data-reveal>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="text-left text-black">
                <tr>
                  <th class="py-3 pr-4">Producto</th>
                  <th class="py-3 pr-4">Categoría</th>
                  <th class="py-3 pr-4">Tipo</th>
                  <th class="py-3 pr-4">Precio</th>
                  <th class="py-3 pr-4">Estado</th>
                  <th class="py-3 pr-4">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/10">
                <?php foreach ($productos as $p): ?>
                  <tr>
                    <td class="py-3 pr-4">
                      <div class="font-semibold text-black"><?= e((string) $p['nombre']) ?></div>
                      <div class="text-xs text-black">#<?= (int) $p['id'] ?> · <?= e((string) $p['creado_en']) ?></div>
                    </td>
                    <td class="py-3 pr-4 text-black"><?= e((string) $p['categoria']) ?></td>
                    <td class="py-3 pr-4 text-black"><?= e((string) $p['tipo']) ?></td>
                    <td class="py-3 pr-4 font-semibold text-black">COP <?= number_format((float) $p['precio'], 2, ',', '.') ?></td>
                    <td class="py-3 pr-4">
                      <?php if ((int) $p['activo'] === 1): ?>
                        <span class="inline-flex px-3 py-1 rounded-full border border-black/10 text-black">Activo</span>
                      <?php else: ?>
                        <span class="inline-flex px-3 py-1 rounded-full border border-black/10 text-black">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td class="py-3 pr-4">
                      <div class="flex flex-wrap gap-2">
                        <a class="px-4 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold text-black" href="<?= e(url('admin/productos.php') . '?edit=' . (int) $p['id']) ?>">Editar</a>
                        <form method="post" action="<?= e(url('admin/productos.php') . '?delete=' . (int) $p['id']) ?>" onsubmit="return confirm('¿Eliminar este producto?');">
                          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
                          <input type="hidden" name="action" value="delete" />
                          <button class="px-4 py-2 rounded-full border border-danger/30 hover:border-danger/60 transition font-semibold text-danger" type="submit">Eliminar</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </main>

  <script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
