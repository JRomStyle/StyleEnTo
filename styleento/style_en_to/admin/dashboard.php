<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../config/db.php';
require_admin();

$user = current_user();

$counts = [
    'productos' => (int) (db()->query('SELECT COUNT(*) c FROM productos')->fetch()['c'] ?? 0),
    'pedidos' => (int) (db()->query('SELECT COUNT(*) c FROM pedidos')->fetch()['c'] ?? 0),
    'ventas' => (float) (db()->query("SELECT COALESCE(SUM(total),0) s FROM pedidos WHERE estado IN ('pagado','enviado')")->fetch()['s'] ?? 0),
];

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $flash = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $pedidoId = (int) ($_POST['pedido_id'] ?? 0);
        $estado = (string) ($_POST['estado'] ?? '');
        if ($pedidoId > 0 && in_array($estado, ['pendiente', 'pagado', 'enviado', 'cancelado'], true)) {
            $upd = db()->prepare('UPDATE pedidos SET estado = :estado WHERE id = :id');
            $upd->execute([':estado' => $estado, ':id' => $pedidoId]);
            $flash = 'Pedido actualizado.';
        }
    }
}

$orders = db()->query('SELECT id, email, nombre, total, estado, creado_en FROM pedidos ORDER BY id DESC LIMIT 10')->fetchAll();

$itemsByOrder = [];
if (count($orders) > 0) {
    $ids = array_map(fn ($o) => (int) $o['id'], $orders);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT pedido_id, nombre, talla, cantidad, subtotal FROM pedido_items WHERE pedido_id IN ({$in}) ORDER BY id ASC");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $it) {
        $itemsByOrder[(int) $it['pedido_id']][] = $it;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="app-base" content="<?= e(rtrim(base_url(), '/')) ?>" />
  <title>Admin — Dashboard</title>
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
        <a class="link-underline" href="<?= e(url('admin/dashboard.php')) ?>">Dashboard</a>
        <a class="link-underline" href="<?= e(url('admin/productos.php')) ?>">Productos</a>
      </nav>
      <div class="flex items-center gap-3">
        <a href="<?= e(url('logout.php')) ?>" class="inline-flex px-5 py-2 rounded-full border border-black/10 hover:border-black/20 transition text-sm">Salir</a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-5 py-10">
    <div class="flex items-end justify-between gap-6">
      <div>
        <h1 class="text-4xl font-extrabold tracking-tight">Admin</h1>
        <div class="mt-2 text-black/60">Hola, <?= e((string) ($user['nombre'] ?? '')) ?>.</div>
      </div>
      <a href="<?= e(url('admin/productos.php')) ?>" class="hidden sm:inline-flex px-6 py-2 rounded-full btn-primary font-semibold">Gestionar productos</a>
    </div>

    <?php if ($flash !== ''): ?>
      <div class="mt-8 border border-black/10 rounded-2xl p-4 bg-white text-sm" data-reveal><?= e($flash) ?></div>
    <?php endif; ?>

    <section class="mt-10 grid md:grid-cols-3 gap-5">
      <div class="border border-black/10 rounded-3xl p-6 bg-white" data-reveal>
        <div class="text-sm text-black/60">Productos</div>
        <div class="mt-2 text-3xl font-extrabold"><?= (int) $counts['productos'] ?></div>
      </div>
      <div class="border border-black/10 rounded-3xl p-6 bg-white" data-reveal>
        <div class="text-sm text-black/60">Pedidos</div>
        <div class="mt-2 text-3xl font-extrabold"><?= (int) $counts['pedidos'] ?></div>
      </div>
      <div class="border border-black/10 rounded-3xl p-6 bg-white" data-reveal>
        <div class="text-sm text-black/60">Ventas (simulado)</div>
        <div class="mt-2 text-3xl font-extrabold">€<?= number_format((float) $counts['ventas'], 2) ?></div>
      </div>
    </section>

    <section class="mt-10 border border-black/10 rounded-[2.5rem] p-6 bg-white" data-reveal>
      <div class="flex items-end justify-between gap-6">
        <div>
          <div class="text-2xl font-extrabold tracking-tight">Pedidos recientes</div>
          <div class="mt-2 text-black/60 text-sm">Actualiza estado y revisa items.</div>
        </div>
      </div>

      <div class="mt-6 grid gap-4">
        <?php foreach ($orders as $o): ?>
          <?php $oid = (int) $o['id']; ?>
          <div class="border border-black/10 rounded-2xl p-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
              <div class="min-w-0">
                <div class="font-semibold">Pedido #<?= $oid ?></div>
                <div class="text-sm text-black/60"><?= e((string) $o['nombre']) ?> · <?= e((string) $o['email']) ?></div>
                <div class="text-sm text-black/60"><?= e((string) $o['creado_en']) ?></div>
              </div>
              <div class="flex items-center gap-4">
                <div class="font-extrabold">€<?= number_format((float) $o['total'], 2) ?></div>
                <form method="post" class="flex items-center gap-2">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
                  <input type="hidden" name="pedido_id" value="<?= $oid ?>" />
                  <select name="estado" class="rounded-full border border-black/10 px-4 py-2 outline-none focus:border-black/30">
                    <?php foreach (['pendiente','pagado','enviado','cancelado'] as $st): ?>
                      <option value="<?= e($st) ?>" <?= $o['estado'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="px-5 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold" type="submit">Guardar</button>
                </form>
              </div>
            </div>

            <?php $items = $itemsByOrder[$oid] ?? []; ?>
            <?php if (count($items) > 0): ?>
              <div class="mt-4 border-t border-black/10 pt-4 grid gap-2 text-sm">
                <?php foreach ($items as $it): ?>
                  <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                      <div class="font-medium truncate"><?= e((string) $it['nombre']) ?></div>
                      <div class="text-black/60">Talla <?= e((string) $it['talla']) ?> · x<?= (int) $it['cantidad'] ?></div>
                    </div>
                    <div class="font-semibold">€<?= number_format((float) $it['subtotal'], 2) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <?php if (count($orders) === 0): ?>
          <div class="border border-black/10 rounded-2xl p-6 text-black/60 text-sm">Aún no hay pedidos.</div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
