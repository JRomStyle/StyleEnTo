<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

if (current_user()) {
    redirect(url('index.php'));
}

$error = '';
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            $error = 'Completa email y nueva contraseña.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
        } elseif ((function_exists('mb_strlen') ? mb_strlen($password) : strlen($password)) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres.';
        } else {
            $stmt = db()->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();
            if (!$row) {
                $error = 'No existe una cuenta con ese email.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = db()->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id');
                $upd->execute([':hash' => $hash, ':id' => (int) $row['id']]);
                $ok = true;
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
  <title>Recuperar contraseña — style en to!</title>
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
      <h1 class="mt-6 text-3xl font-extrabold tracking-tight">Recuperar contraseña</h1>
      <p class="mt-2 text-black/60">Restablecimiento simulado (sin envío de email).</p>

      <?php if ($ok): ?>
        <div class="mt-6 border border-accent/40 bg-accent/10 rounded-2xl p-4 text-sm">Contraseña actualizada. Ya puedes iniciar sesión.</div>
        <a class="mt-6 inline-flex px-7 py-3 rounded-full btn-primary font-semibold" href="<?= e(url('login.php')) ?>">Ir a login</a>
      <?php else: ?>
        <?php if ($error !== ''): ?>
          <div class="mt-6 border border-danger/30 bg-danger/5 rounded-2xl p-4 text-sm"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="mt-6 grid gap-4" method="post" action="<?= e(url('recuperar.php')) ?>">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
          <div>
            <label class="text-sm font-semibold">Email</label>
            <input name="email" type="email" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" required />
          </div>
          <div>
            <label class="text-sm font-semibold">Nueva contraseña</label>
            <input name="password" type="password" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" required />
          </div>
          <button class="mt-2 px-7 py-3 rounded-full btn-primary font-semibold" type="submit">Actualizar</button>
          <div class="mt-2 text-sm text-black/60 flex items-center justify-between">
            <a class="link-underline" href="<?= e(url('login.php')) ?>">Volver a login</a>
            <a class="link-underline" href="<?= e(url('index.php')) ?>">Inicio</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
