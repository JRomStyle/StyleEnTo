<?php
namespace App\Controllers;
use App\Models\Product;
use App\Models\Category;
class HomeController extends Controller {
    public function index(): void {
        $products = Product::getActive(12);
        $categories = Category::all();
        $this->render('home/index', ['products' => $products, 'categories' => $categories]);
    }
}
