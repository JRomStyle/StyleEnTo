<?php
namespace App\Controllers;
use App\Models\Product;
class CartController extends Controller {
    private function cart(): array {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }
    private function saveCart(array $cart): void {
        $_SESSION['cart'] = $cart;
    }
    public function index(): void {
        $cart = $this->cart();
        $items = [];
        $total = 0;
        foreach ($cart as $id => $qty) {
            $p = Product::find((int)$id);
            if ($p) {
                $p['cantidad'] = (int)$qty;
                $p['subtotal'] = $p['precio'] * $p['cantidad'];
                $items[] = $p;
                $total += $p['subtotal'];
            }
        }
        $this->render('cart/index', ['items' => $items, 'total' => $total]);
    }
    public function add(): void {
        if (!is_post() || !check_csrf()) {
            json_response(['ok' => false], 400);
        }
        $id = (int)($_POST['id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);
        $product = $id ? Product::find($id) : null;
        if (!$product) {
            json_response(['ok' => false], 404);
        }
        $cart = $this->cart();
        $cart[$id] = ($cart[$id] ?? 0) + max(1, $qty);
        $this->saveCart($cart);
        $count = array_sum($cart);
        json_response(['ok' => true, 'count' => $count]);
    }
    public function update(): void {
        if (!is_post() || !check_csrf()) {
            if (!empty($_POST['redirect'])) {
                redirect('cart/index');
            }
            json_response(['ok' => false], 400);
        }
        $id = (int)($_POST['id'] ?? 0);
        $qty = max(0, (int)($_POST['qty'] ?? 0));
        $cart = $this->cart();
        if ($qty === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }
        $this->saveCart($cart);
        $count = array_sum($cart);
        if (!empty($_POST['redirect'])) {
            redirect('cart/index');
        }
        json_response(['ok' => true, 'count' => $count]);
    }
    public function clear(): void {
        if (is_post() && !check_csrf()) {
            redirect('cart/index');
        }
        $_SESSION['cart'] = [];
        redirect('cart/index');
    }
}
