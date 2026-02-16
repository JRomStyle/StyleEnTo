<?php
class Cart extends Controller {
    public function __construct(){
        if(!isset($_SESSION['cart'])){
            $_SESSION['cart'] = [];
        }
        $this->productModel = $this->model('Product');
    }

    public function index(){
        $data = [
            'cart_items' => $_SESSION['cart']
        ];
        $this->view('cart/index', $data);
    }

    public function add($id){
        $product = $this->productModel->getProductById($id);
        
        if($product){
            $item = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1 // Default 1
            ];

            // Check if exists
            if(isset($_SESSION['cart'][$id])){
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                $_SESSION['cart'][$id] = $item;
            }

            flash('cart_message', 'Producto agregado al carrito');
        }
        
        // Return JSON if AJAX or Redirect
        // For simplicity, redirect
        redirect('products/index');
    }

    public function update($id, $qty){
        if(isset($_SESSION['cart'][$id])){
            if($qty > 0){
                $_SESSION['cart'][$id]['quantity'] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
        redirect('cart/index');
    }

    public function remove($id){
        if(isset($_SESSION['cart'][$id])){
            unset($_SESSION['cart'][$id]);
        }
        flash('cart_message', 'Producto eliminado', 'alert alert-danger');
        redirect('cart/index');
    }

    public function clear(){
        $_SESSION['cart'] = [];
        redirect('cart/index');
    }
}
