<?php
namespace App\Controllers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\MailService;
class OrderController extends Controller {
    public function my(): void {
        $user = auth_user();
        if (!$user) {
            redirect('auth/login');
        }
        $orders = Order::byUser((int)$user['id']);
        $this->render('orders/index', ['orders' => $orders]);
    }
    public function show(): void {
        $user = auth_user();
        if (!$user) {
            redirect('auth/login');
        }
        $id = (int)($_GET['id'] ?? 0);
        $order = $id > 0 ? Order::findForUser($id, (int)$user['id']) : null;
        if (!$order) {
            redirect('order/my');
        }
        $items = Order::items($id);
        $history = Order::historyPublic($id);
        $this->render('orders/show', ['order' => $order, 'items' => $items, 'history' => $history]);
    }
    public function checkout(): void {
        $user = auth_user();
        if (!$user) {
            redirect('auth/login');
        }
        if (is_post() && check_csrf()) {
            $cart = $_SESSION['cart'] ?? [];
            if (!$cart) {
                redirect('cart/index');
            }
            $items = [];
            $total = 0;
            foreach ($cart as $id => $qty) {
                $p = Product::find((int)$id);
                if ($p && $qty > 0) {
                    $subtotal = $p['precio'] * $qty;
                    $items[] = ['id' => $p['id'], 'nombre' => $p['nombre'], 'precio' => $p['precio'], 'qty' => $qty, 'subtotal' => $subtotal];
                    $total += $subtotal;
                }
            }
            $orderId = Order::create((int)$user['id'], (float)$total, 'pendiente');
            foreach ($items as $it) {
                OrderItem::create($orderId, (int)$it['id'], (int)$it['qty'], (float)$it['precio']);
                $ok = Product::decrementStock((int)$it['id'], (int)$it['qty']);
                if ($ok) {
                    Product::logInventoryMovement((int)$it['id'], 'venta', (int)$it['qty'], $orderId, null, null);
                }
            }
            $_SESSION['cart'] = [];
            $html = '<h1>Confirmación de pedido</h1><p>Pedido #' . $orderId . '</p><ul>';
            foreach ($items as $it) {
                $html .= '<li>' . htmlspecialchars($it['nombre']) . ' x' . $it['qty'] . ' - $' . number_format($it['subtotal'], 2) . '</li>';
            }
            $html .= '</ul><p>Total: $' . number_format($total, 2) . '</p>';
            MailService::send($user['email'], 'Confirmación de compra', $html);
            $this->render('checkout/success', ['orderId' => $orderId, 'total' => $total]);
            return;
        }
        $cart = $_SESSION['cart'] ?? [];
        $items = [];
        $total = 0;
        foreach ($cart as $id => $qty) {
            $p = Product::find((int)$id);
            if ($p && $qty > 0) {
                $p['cantidad'] = (int)$qty;
                $p['subtotal'] = $p['precio'] * $p['cantidad'];
                $items[] = $p;
                $total += $p['subtotal'];
            }
        }
        $this->render('checkout/index', ['items' => $items, 'total' => $total]);
    }
}
