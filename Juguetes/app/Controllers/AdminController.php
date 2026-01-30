<?php
namespace App\Controllers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
class AdminController extends Controller {
    private function ensureAdmin(): void {
        $user = auth_user();
        if (!$user || ($user['rol'] ?? '') !== 'admin') {
            redirect('auth/login');
        }
    }
    public function dashboard(): void {
        $this->ensureAdmin();
        $orders = Order::all();
        $this->render('admin/dashboard', ['orders' => $orders]);
    }
    public function products(): void {
        $this->ensureAdmin();
        $products = Product::allActive();
        $this->render('admin/products/index', ['products' => $products]);
    }
    public function productCreate(): void {
        $this->ensureAdmin();
        $categories = Category::all();
        $error = null;
        if (is_post() && check_csrf()) {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = (float)($_POST['precio'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $edad = trim($_POST['edad_recomendada'] ?? '');
            $genero = trim($_POST['genero'] ?? 'unisex');
            if (!in_array($genero, ['niño', 'niña', 'unisex'], true)) {
                $genero = 'unisex';
            }
            $edadMinInput = isset($_POST['edad_min']) && trim((string)$_POST['edad_min']) !== '' ? (int)$_POST['edad_min'] : null;
            $edadMaxInput = isset($_POST['edad_max']) && trim((string)$_POST['edad_max']) !== '' ? (int)$_POST['edad_max'] : null;
            if ($edadMinInput !== null || $edadMaxInput !== null) {
                $edadMin = $edadMinInput;
                $edadMax = $edadMaxInput;
                if ($edadMin !== null && $edadMax !== null && $edadMin > $edadMax) {
                    [$edadMin, $edadMax] = [$edadMax, $edadMin];
                }
            } else {
                [$edadMin, $edadMax] = $this->parseEdad($edad);
            }
            $categoria_id = (int)($_POST['categoria_id'] ?? 0);
            $estado = trim($_POST['estado'] ?? 'activo');
            $imagen = '';
            if (!empty($_FILES['imagen']['name'])) {
                $dir = __DIR__ . '/../../uploads';
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                $name = time() . '_' . preg_replace('/[^a-z0-9\\.\\-_]/i', '_', $_FILES['imagen']['name']);
                $dest = $dir . '/' . $name;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                    $imagen = 'uploads/' . $name;
                }
            }
            if ($nombre !== '' && $precio > 0 && $categoria_id > 0) {
                try {
                    $ok = Product::create([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'stock' => $stock,
                        'edad_recomendada' => $edad,
                        'edad_min' => $edadMin,
                        'edad_max' => $edadMax,
                        'genero' => $genero,
                        'imagen' => $imagen,
                        'categoria_id' => $categoria_id,
                        'estado' => $estado,
                    ]);
                    if ($ok) {
                        redirect('admin/products');
                        return;
                    }
                    $error = 'No se pudo guardar el producto.';
                } catch (\Throwable $e) {
                    $error = 'No se pudo guardar el producto.';
                }
            } else {
                $error = 'Revisa nombre, precio y categoría.';
            }
        }
        $this->render('admin/products/create', ['categories' => $categories, 'error' => $error]);
    }
    public function productDelete(): void {
        $this->ensureAdmin();
        if (is_post() && check_csrf()) {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                Product::delete($id);
            }
        }
        redirect('admin/products');
    }
    public function productEdit(): void {
        $this->ensureAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $product = $id ? Product::find($id) : null;
        $categories = Category::all();
        $error = null;
        if (!$product) {
            redirect('admin/products');
        }
        if (is_post() && check_csrf()) {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = (float)($_POST['precio'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $edad = trim($_POST['edad_recomendada'] ?? '');
            $genero = trim($_POST['genero'] ?? 'unisex');
            if (!in_array($genero, ['niño', 'niña', 'unisex'], true)) {
                $genero = 'unisex';
            }
            $edadMinInput = isset($_POST['edad_min']) && trim((string)$_POST['edad_min']) !== '' ? (int)$_POST['edad_min'] : null;
            $edadMaxInput = isset($_POST['edad_max']) && trim((string)$_POST['edad_max']) !== '' ? (int)$_POST['edad_max'] : null;
            if ($edadMinInput !== null || $edadMaxInput !== null) {
                $edadMin = $edadMinInput;
                $edadMax = $edadMaxInput;
                if ($edadMin !== null && $edadMax !== null && $edadMin > $edadMax) {
                    [$edadMin, $edadMax] = [$edadMax, $edadMin];
                }
            } else {
                [$edadMin, $edadMax] = $this->parseEdad($edad);
            }
            $categoria_id = (int)($_POST['categoria_id'] ?? 0);
            $estado = trim($_POST['estado'] ?? 'activo');
            $imagen = $product['imagen'];
            if (!empty($_FILES['imagen']['name'])) {
                $dir = __DIR__ . '/../../uploads';
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                $name = time() . '_' . preg_replace('/[^a-z0-9\\.\\-_]/i', '_', $_FILES['imagen']['name']);
                $dest = $dir . '/' . $name;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                    $imagen = 'uploads/' . $name;
                }
            }
            if ($nombre !== '' && $precio > 0 && $categoria_id > 0) {
                try {
                    $ok = Product::update($id, [
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'stock' => $stock,
                        'edad_recomendada' => $edad,
                        'edad_min' => $edadMin,
                        'edad_max' => $edadMax,
                        'genero' => $genero,
                        'imagen' => $imagen,
                        'categoria_id' => $categoria_id,
                        'estado' => $estado,
                    ]);
                    if ($ok) {
                        redirect('admin/products');
                        return;
                    }
                    $error = 'No se pudo guardar el producto.';
                } catch (\Throwable $e) {
                    $error = 'No se pudo guardar el producto.';
                }
            } else {
                $error = 'Revisa nombre, precio y categoría.';
            }
        }
        $this->render('admin/products/edit', ['product' => $product, 'categories' => $categories, 'error' => $error]);
    }
    public function categories(): void {
        $this->ensureAdmin();
        $categories = Category::all();
        $this->render('admin/categories/index', ['categories' => $categories]);
    }
    public function categoryCreate(): void {
        $this->ensureAdmin();
        if (is_post() && check_csrf()) {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            if ($nombre !== '') {
                Category::create($nombre, $descripcion);
                redirect('admin/categories');
                return;
            }
        }
        $this->render('admin/categories/create', []);
    }
    public function categoryDelete(): void {
        $this->ensureAdmin();
        if (is_post() && check_csrf()) {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                Category::delete($id);
            }
        }
        redirect('admin/categories');
    }
    public function categoryEdit(): void {
        $this->ensureAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $category = $id ? Category::find($id) : null;
        if (!$category) {
            redirect('admin/categories');
        }
        if (is_post() && check_csrf()) {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            if ($nombre !== '') {
                Category::update($id, $nombre, $descripcion);
                redirect('admin/categories');
                return;
            }
        }
        $this->render('admin/categories/edit', ['category' => $category]);
    }
    public function productsByCategory(): void {
        $this->ensureAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $category = $id ? Category::find($id) : null;
        if (!$category) {
            redirect('admin/categories');
        }
        $categories = Category::all();
        $products = \App\Models\Product::byCategory($id);
        $this->render('admin/products/by_category', ['category' => $category, 'products' => $products, 'categories' => $categories]);
    }
    public function productMoveCategory(): void {
        $this->ensureAdmin();
        if (is_post() && check_csrf()) {
            $id = (int)($_POST['id'] ?? 0);
            $categoria_id = (int)($_POST['categoria_id'] ?? 0);
            if ($id > 0 && $categoria_id > 0) {
                \App\Models\Product::moveCategory($id, $categoria_id);
            }
        }
        $back = $_SERVER['HTTP_REFERER'] ?? '?route=admin/products';
        redirect($back);
    }
    public function orders(): void {
        $this->ensureAdmin();
        $orders = Order::all();
        $this->render('admin/orders/index', ['orders' => $orders]);
    }
    public function orderShow(): void {
        $this->ensureAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $order = $id > 0 ? Order::find($id) : null;
        if (!$order) {
            redirect('admin/orders');
        }
        $items = Order::items($id);
        $history = Order::history($id);
        $movements = Order::inventoryMovements($id);
        $this->render('admin/orders/show', ['order' => $order, 'items' => $items, 'history' => $history, 'movements' => $movements]);
    }
    public function orderUpdate(): void {
        $this->ensureAdmin();
        if (is_post() && check_csrf()) {
            $id = (int)($_POST['id'] ?? 0);
            $estado = trim($_POST['estado'] ?? '');
            $nota = trim($_POST['nota'] ?? '');
            if ($nota === '') {
                $nota = null;
            } else {
                $nota = function_exists('mb_substr') ? mb_substr($nota, 0, 255) : substr($nota, 0, 255);
            }
            if ($id > 0 && $estado !== '') {
                $user = auth_user();
                $adminId = $user ? (int)($user['id'] ?? 0) : null;
                if ($adminId === 0) {
                    $adminId = null;
                }
                Order::updateStatus($id, $estado, $adminId, $nota);
            }
            $redirect = trim($_POST['redirect'] ?? '');
            if ($redirect !== '' && strpos($redirect, 'admin/') === 0) {
                redirect($redirect);
            }
        }
        redirect('admin/orders');
    }
    private function parseEdad(string $edad): array {
        $edad = trim($edad);
        if ($edad === '') return [null, null];
        if (preg_match('/^(\d+)\+$/u', $edad, $m)) {
            return [(int)$m[1], 99];
        }
        if (preg_match('/^(\d+)\s*-\s*(\d+)$/u', $edad, $m)) {
            return [(int)$m[1], (int)$m[2]];
        }
        if (preg_match('/(\d+)/u', $edad, $m)) {
            $v = (int)$m[1];
            return [$v, $v];
        }
        return [null, null];
    }
}
