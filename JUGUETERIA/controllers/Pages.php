<?php
class Pages extends Controller {
    public function __construct(){
        $this->productModel = $this->model('Product');
    }

    public function index(){
        $products = $this->productModel->getProducts();
        // Limit to 6 for home page? Or just show all? The plan said "featured".
        // For now let's just slice the array or let the view handle it.
        // Let's just slice it here to 6.
        $recentProducts = array_slice($products, 0, 6);

        $data = [
            'title' => 'Juguetería Mágica',
            'description' => 'Los mejores juguetes para los niños',
            'products' => $recentProducts
        ];

        $this->view('pages/index', $data);
    }

    public function about(){
        $data = [
            'title' => 'About Us',
            'description' => 'App to share posts with other users'
        ];

        $this->view('pages/about', $data);
    }
}
