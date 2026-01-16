<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

if (current_user()) {
    redirect(url('index.php'));
}

$error = '';
$next = isset($_GET['next']) ? (string) $_GET['next'] : url('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $next = isset($_POST['next']) ? (string) $_POST['next'] : $next;

        if ($email === '' || $password === '') {
            $error = 'Completa email y contraseña.';
        } else {
            $stmt = db()->prepare('SELECT id, password_hash, rol FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();

            $ok = false;
            if ($row) {
                $stored = (string) $row['password_hash'];
                if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
                    $ok = password_verify($password, $stored);
                } else {
                    $ok = hash_equals($stored, $password);
                    if ($ok) {
                        $new = password_hash($password, PASSWORD_DEFAULT);
                        $upd = db()->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id');
                        $upd->execute([':hash' => $new, ':id' => (int) $row['id']]);
                    }
                }
            }

            if (!$ok) {
                $error = 'Credenciales incorrectas.';
            } else {
                $_SESSION['user_id'] = (int) $row['id'];
                $base = base_url();
                if ($next === '' || !str_starts_with($next, $base)) {
                    $next = url('index.php');
                }
                redirect($next);
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
  <title>Login — style en to!</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { accent: "#00ff85", danger: "#ff2d2d" } } } }
  </script>
  <link rel="stylesheet" href="<?= e(url('assets/css/tailwind.css')) ?>" />
</head>
<body class="bg-white text-black">
  <main class="min-h-screen grid place-items-center px-5 py-12">
    <div class="w-full max-w-md border border-black/10 rounded-[2.5rem] p-8 bg-white shadow-2xl">
      <a href="<?= e(url('index.php')) ?>" class="font-extrabold tracking-tight text-lg">
        style <span class="text-accent">en</span> to<span class="text-danger">!</span>
      </a>
      <h1 class="mt-6 text-3xl font-extrabold tracking-tight">Iniciar sesión</h1>
      <p class="mt-2 text-black/60">Accede para administrar y finalizar compras.</p>

      <?php if ($error !== ''): ?>
        <div class="mt-6 border border-danger/30 bg-danger/5 rounded-2xl p-4 text-sm"><?= e($error) ?></div>
      <?php endif; ?>

      <form class="mt-6 grid gap-4" method="post" action="<?= e(url('login.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="next" value="<?= e($next) ?>" />

        <div>
          <label class="text-sm font-semibold">Email</label>
          <input name="email" type="email" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="admin@styleento.local" required />
        </div>
        <div>
          <label class="text-sm font-semibold">Contraseña</label>
          <input name="password" type="password" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="••••••••" required />
        </div>

        <button class="mt-2 px-7 py-3 rounded-full btn-primary font-semibold" type="submit">Entrar</button>

        <div class="mt-2 text-sm text-black/60 flex items-center justify-between">
          <a class="link-underline" href="<?= e(url('registro.php')) ?>">Crear cuenta</a>
          <a class="link-underline" href="<?= e(url('recuperar.php')) ?>">Recuperar contraseña</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>

