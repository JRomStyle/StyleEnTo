<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = '127.0.0.1';
    $dbname = 'style_en_to';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header("Location: {$path}");
    exit;
}

function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
    if (preg_match('~^(.*)/admin$~', $dir, $m)) {
        $dir = $m[1];
    }
    $base = $dir === '' ? '' : $dir;
    return $base;
}

function url(string $path = ''): string
{
    $base = rtrim(base_url(), '/');
    $p = ltrim($path, '/');
    if ($p === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return $base === '' ? '/' . $p : $base . '/' . $p;
}

function image_src(string $value): string
{
    $v = trim($value);
    if ($v === '') {
        return url('assets/img/placeholder-product.jpg');
    }
    $v = str_replace('\\', '/', $v);
    if (preg_match('~^(https?:)?//~i', $v)) {
        return $v;
    }
    if (preg_match('~(?:^|/)(assets/img/[^?]+)$~i', $v, $m)) {
        return url($m[1]);
    }
    if (str_starts_with($v, 'assets/')) {
        return url($v);
    }
    if (str_starts_with($v, 'img/')) {
        return url('assets/' . ltrim($v, '/'));
    }

    static $imgMap = null;
    if ($imgMap === null) {
        $imgMap = [];
        $dir = __DIR__ . '/../assets/img/';
        if (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    continue;
                }
                $lower = strtolower($file);
                $base = strtolower(pathinfo($file, PATHINFO_FILENAME));
                $imgMap[$lower] = $file;
                if (!isset($imgMap[$base])) {
                    $imgMap[$base] = $file;
                }
            }
        }
    }

    $base = basename($v);
    $lower = strtolower($base);
    if (isset($imgMap[$lower])) {
        return url('assets/img/' . $imgMap[$lower]);
    }
    $baseLower = strtolower(pathinfo($base, PATHINFO_FILENAME));
    if (isset($imgMap[$baseLower])) {
        return url('assets/img/' . $imgMap[$baseLower]);
    }

    return url('assets/img/placeholder-product.jpg');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): bool
{
    $known = $_SESSION['csrf_token'] ?? '';
    if (!is_string($known) || $known === '' || !is_string($token) || $token === '') {
        return false;
    }
    return hash_equals($known, $token);
}

function current_user(): ?array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $id = $_SESSION['user_id'] ?? null;
    if (!is_int($id) && !ctype_digit((string) $id)) {
        $cache = null;
        return $cache;
    }

    $stmt = db()->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => (int) $id]);
    $user = $stmt->fetch() ?: null;

    $cache = $user;
    return $cache;
}

function require_login(): void
{
    if (!current_user()) {
        $next = urlencode($_SERVER['REQUEST_URI'] ?? url('index.php'));
        redirect(url('login.php') . "?next={$next}");
    }
}

function require_admin(): void
{
    $user = current_user();
    if (!$user || ($user['rol'] ?? '') !== 'admin') {
        redirect(url('index.php'));
    }
}
