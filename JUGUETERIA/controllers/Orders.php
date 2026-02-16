<?php
class Orders extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->orderModel = $this->model('Order');
    }

    public function index(){
        if(isAdmin()){
             $orders = $this->orderModel->getAllOrders();
             $data = ['orders' => $orders];
             $this->view('orders/admin_index', $data);
        } else {
             $orders = $this->orderModel->getOrdersByUserId($_SESSION['user_id']);
             $data = ['orders' => $orders];
             $this->view('orders/index', $data);
        }
    }

    public function checkout(){
        if(empty($_SESSION['cart'])){
            redirect('products/index');
        }

        // Calculate Total
        $total = 0;
        foreach($_SESSION['cart'] as $item){
            $total += $item['price'] * $item['quantity'];
        }

        if($this->orderModel->createOrder($_SESSION['user_id'], $total, $_SESSION['cart'])){
            // Clear Cart
            unset($_SESSION['cart']);
            flash('order_message', '¡Compra realizada con éxito!');
            redirect('orders/index');
        } else {
            die('Error al procesar la orden');
        }
    }
}
