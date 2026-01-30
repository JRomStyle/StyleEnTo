<?php
function csrf_token(): string {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function check_csrf(): bool {
    return isset($_POST['csrf']) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
function redirect(string $route): void {
    header('Location: ' . (strpos($route, 'http') === 0 ? $route : ('?route=' . $route)));
    exit;
}
function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}
function login_user(array $user): void {
    $_SESSION['user'] = $user;
}
function logout_user(): void {
    unset($_SESSION['user']);
}
function require_role(string $role): void {
    $user = auth_user();
    if (!$user || ($user['rol'] ?? '') !== $role) {
        http_response_code(403);
        echo '403';
        exit;
    }
}
function json_response($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function product_image_url(array $product, int $w = 600, int $h = 400): string {
    $img = trim((string)($product['imagen'] ?? ''));
    if ($img !== '' && stripos($img, 'placehold.co') === false && stripos($img, 'via.placeholder') === false) {
        return $img;
    }
    $id = (int)($product['id'] ?? 0);
    $name = trim((string)($product['nombre'] ?? ''));
    $name = preg_replace('/\s+/u', ' ', $name) ?: 'toy';
    $parts = preg_split('/\s+/u', $name) ?: [];
    $parts = array_slice(array_filter($parts), 0, 3);
    $keywords = array_merge(['toy', 'kids'], $parts);
    $q = implode(',', array_map('rawurlencode', $keywords));
    return 'https://source.unsplash.com/' . $w . 'x' . $h . '/?' . $q . '&sig=' . max(1, $id);
}
