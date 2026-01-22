<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

$user = current_user();
$ok = false;
$error = '';
$pedidoId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $cartRaw = (string) ($_POST['cart_json'] ?? '[]');
        $cart = json_decode($cartRaw, true);
        if (!is_array($cart) || count($cart) === 0) {
            $error = 'Tu carrito está vacío.';
        } else {
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $direccion = trim((string) ($_POST['direccion'] ?? ''));
            $ciudad = trim((string) ($_POST['ciudad'] ?? ''));
            $pais = trim((string) ($_POST['pais'] ?? ''));
            $telefono = trim((string) ($_POST['telefono'] ?? ''));

            if ($nombre === '' || $email === '' || $direccion === '' || $ciudad === '' || $pais === '' || $telefono === '') {
                $error = 'Completa tus datos para finalizar el checkout.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email inválido.';
            } else {
                $ids = [];
                foreach ($cart as $it) {
                    if (!is_array($it)) continue;
                    $ids[] = (int) ($it['id'] ?? 0);
                }
                $ids = array_values(array_filter(array_unique($ids)));
                if (count($ids) === 0) {
                    $error = 'Carrito inválido.';
                } else {
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = db()->prepare("SELECT id, nombre, precio FROM productos WHERE activo = 1 AND id IN ({$in})");
                    $stmt->execute($ids);
                    $rows = $stmt->fetchAll();
                    $byId = [];
                    foreach ($rows as $r) $byId[(int) $r['id']] = $r;

                    $items = [];
                    $total = 0.0;
                    foreach ($cart as $it) {
                        if (!is_array($it)) continue;
                        $pid = (int) ($it['id'] ?? 0);
                        $qty = (int) ($it['qty'] ?? 0);
                        $size = trim((string) ($it['size'] ?? 'U'));
                        if ($pid <= 0 || $qty <= 0 || !isset($byId[$pid])) continue;
                        $precio = (float) $byId[$pid]['precio'];
                        $subtotal = $precio * $qty;
                        $total += $subtotal;
                        $items[] = [
                            'producto_id' => $pid,
                            'nombre' => (string) $byId[$pid]['nombre'],
                            'talla' => $size === '' ? 'U' : $size,
                            'precio_unitario' => $precio,
                            'cantidad' => $qty,
                            'subtotal' => $subtotal,
                        ];
                    }

                    if (count($items) === 0) {
                        $error = 'No se pudieron validar los productos del carrito.';
                    } else {
                        db()->beginTransaction();
                        try {
                            $stmtP = db()->prepare('INSERT INTO pedidos (usuario_id, email, nombre, direccion, ciudad, pais, telefono, total, estado) VALUES (:uid, :email, :nombre, :direccion, :ciudad, :pais, :telefono, :total, :estado)');
                            $stmtP->execute([
                                ':uid' => $user ? (int) $user['id'] : null,
                                ':email' => $email,
                                ':nombre' => $nombre,
                                ':direccion' => $direccion,
                                ':ciudad' => $ciudad,
                                ':pais' => $pais,
                                ':telefono' => $telefono,
                                ':total' => $total,
                                ':estado' => 'pagado',
                            ]);
                            $pedidoId = (int) db()->lastInsertId();

                            $stmtI = db()->prepare('INSERT INTO pedido_items (pedido_id, producto_id, nombre, talla, precio_unitario, cantidad, subtotal) VALUES (:pedido_id, :producto_id, :nombre, :talla, :precio_unitario, :cantidad, :subtotal)');
                            $stmtV = db()->prepare('UPDATE productos SET ventas = ventas + :inc WHERE id = :id');
                            foreach ($items as $it) {
                                $stmtI->execute([
                                    ':pedido_id' => $pedidoId,
                                    ':producto_id' => (int) $it['producto_id'],
                                    ':nombre' => (string) $it['nombre'],
                                    ':talla' => (string) $it['talla'],
                                    ':precio_unitario' => (float) $it['precio_unitario'],
                                    ':cantidad' => (int) $it['cantidad'],
                                    ':subtotal' => (float) $it['subtotal'],
                                ]);
                                $stmtV->execute([':inc' => (int) $it['cantidad'], ':id' => (int) $it['producto_id']]);
                            }

                            db()->commit();
                            $ok = true;
                        } catch (Throwable $t) {
                            db()->rollBack();
                            $error = 'No se pudo crear el pedido. Intenta de nuevo.';
                        }
                    }
                }
            }
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
  <title>Checkout — style en to!</title>
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
      <div class="flex items-center gap-3">
        <a href="<?= e(url('carrito.php')) ?>" class="relative inline-flex items-center gap-2 px-4 py-2 rounded-full border border-black/10 hover:border-black/20 transition">
          <span class="text-sm font-medium">Carrito</span>
          <span id="cart-count" class="min-w-6 h-6 px-2 rounded-full bg-black text-black text-xs font-semibold inline-flex items-center justify-center">0</span>
        </a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-7xl px-5 py-10">
    <div class="flex items-end justify-between gap-6">
      <div>
        <h1 class="text-4xl font-extrabold tracking-tight">Checkout</h1>
        <div class="mt-2 text-black/60">Pago simulado (estado: pagado).</div>
      </div>
      <a href="<?= e(url('tienda.php')) ?>" class="hidden sm:inline-flex px-6 py-2 rounded-full border border-black/10 hover:border-black/20 transition font-semibold">Seguir comprando</a>
    </div>

    <?php if ($ok && $pedidoId): ?>
      <div class="mt-10 border border-black/10 rounded-3xl p-8 bg-white" data-reveal>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-black/10 text-xs font-semibold tracking-wide uppercase text-black">
          Confirmado <span class="w-1.5 h-1.5 rounded-full bg-accent"></span> Pedido #<?= (int) $pedidoId ?>
        </div>
        <div class="mt-5 text-3xl font-extrabold tracking-tight text-black">Listo. Tu pedido fue creado.</div>
        <div class="mt-2 text-black/70">Puedes ver pedidos en el panel admin si eres dueño.</div>
        <div class="mt-7 flex flex-wrap gap-3">
          <a href="<?= e(url('tienda.php')) ?>" class="inline-flex px-7 py-3 rounded-full btn-primary font-semibold text-white">Volver a la tienda</a>
          <a href="<?= e(url('index.php')) ?>" class="inline-flex px-7 py-3 rounded-full border border-black/10 hover:border-black/20 transition font-semibold text-black">Inicio</a>
        </div>
        <script>
          try { localStorage.removeItem("style_en_to_cart_v1"); } catch {}
        </script>
      </div>
    <?php else: ?>
      <?php if ($error !== ''): ?>
        <div class="mt-8 border border-danger/30 bg-danger/5 rounded-2xl p-4 text-sm text-black" data-reveal><?= e($error) ?></div>
      <?php endif; ?>

      <div class="mt-10 grid lg:grid-cols-[1fr_420px] gap-8 items-start">
        <section class="border border-black/10 rounded-3xl p-6 bg-white" data-reveal>
          <div class="text-xl text-black font-extrabold tracking-tight">Tus datos</div>
          <form id="checkout-form" class="mt-6 grid gap-4" method="post" action="<?= e(url('checkout.php')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
            <input type="hidden" id="cart_json" name="cart_json" value="[]" />

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-semibold text-black">Nombre</label>
                <input name="nombre" value="<?= e($user['nombre'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="Tu nombre" required />
              </div>
              <div>
                <label class="text-sm font-semibold text-black">Email</label>
                <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="tu@email.com" required />
              </div>
            </div>

            <div>
              <label class="text-sm font-semibold text-black">Dirección</label>
              <input name="direccion" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="Calle, número, piso" required />
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-semibold text-black">Ciudad</label>
                <input name="ciudad" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required />
              </div>
              <div>
                <label class="text-sm font-semibold text-black">País</label>
                <input name="pais" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" required />
              </div>
            </div>

            <div>
              <label class="text-sm font-semibold text-black">Teléfono</label>
              <input name="telefono" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30 text-black" placeholder="+34 ..." required />
            </div>

            <button class="mt-2 px-7 py-3 rounded-full btn-primary font-semibold text-white" type="submit">Pagar (simulado)</button>
          </form>
        </section>

        <aside class="border border-black/10 rounded-3xl p-6 bg-white" data-reveal>
          <div class="text-xl font-extrabold tracking-tight text-black">Resumen</div>
          <div id="checkout-summary" class="mt-6 text-black"></div>
          <div class="mt-6 flex items-center justify-between">
            <div class="text-black">Total</div>
            <div id="checkout-total" class="text-2xl font-extrabold text-black">COP 0,00</div>
          </div>
          <div class="mt-4 text-xs text-black/60">Al pagar, se crea el pedido y se marca como pagado.</div>
        </aside>
      </div>
    <?php endif; ?>
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

