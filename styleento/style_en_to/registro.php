<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

if (current_user()) {
    redirect(url('index.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Intenta de nuevo.';
    } else {
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($nombre === '' || $email === '' || $password === '') {
            $error = 'Completa todos los campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
        } elseif ((function_exists('mb_strlen') ? mb_strlen($password) : strlen($password)) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres.';
        } else {
            $stmt = db()->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $error = 'Ese email ya está registrado.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = db()->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (:nombre, :email, :hash, :rol)');
                $ins->execute([':nombre' => $nombre, ':email' => $email, ':hash' => $hash, ':rol' => 'cliente']);
                $_SESSION['user_id'] = (int) db()->lastInsertId();
                redirect(url('index.php'));
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
  <title>Registro — style en to!</title>
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
      <h1 class="mt-6 text-3xl font-extrabold tracking-tight">Crear cuenta</h1>
      <p class="mt-2 text-black/60">Un perfil para comprar más rápido y ver tus pedidos.</p>

      <?php if ($error !== ''): ?>
        <div class="mt-6 border border-danger/30 bg-danger/5 rounded-2xl p-4 text-sm"><?= e($error) ?></div>
      <?php endif; ?>

      <form class="mt-6 grid gap-4" method="post" action="<?= e(url('registro.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
        <div>
          <label class="text-sm font-semibold">Nombre</label>
          <input name="nombre" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="Tu nombre" required />
        </div>
        <div>
          <label class="text-sm font-semibold">Email</label>
          <input name="email" type="email" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="tu@email.com" required />
        </div>
        <div>
          <label class="text-sm font-semibold">Contraseña</label>
          <input name="password" type="password" class="mt-2 w-full rounded-2xl border border-black/10 px-4 py-3 outline-none focus:border-black/30" placeholder="Mínimo 6 caracteres" required />
        </div>

        <button class="mt-2 px-7 py-3 rounded-full btn-primary font-semibold" type="submit">Crear cuenta</button>

        <div class="mt-2 text-sm text-black/60 flex items-center justify-between">
          <a class="link-underline" href="<?= e(url('login.php')) ?>">Ya tengo cuenta</a>
          <a class="link-underline" href="<?= e(url('index.php')) ?>">Inicio</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
