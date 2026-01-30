<?php
namespace App\Controllers;
use App\Models\Product;
use App\Models\Category;
class ProductController extends Controller {
    public function index(): void {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sort = trim($_GET['sort'] ?? 'recent');
        $perPage = 12;
        $filters = [];
        $cat = (int)($_GET['cat'] ?? 0);
        if ($cat > 0) $filters['cat'] = $cat;
        $gender = trim($_GET['gender'] ?? '');
        if (in_array($gender, ['niño','niña','unisex'], true)) $filters['gender'] = $gender;
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') $filters['q'] = $q;
        $price = trim($_GET['price'] ?? '');
        if ($price !== '') {
            if ($price === '100+') {
                $filters['price_min'] = 100;
            } elseif (preg_match('/^(\d+)\-(\d+)$/', $price, $m)) {
                $filters['price_min'] = (int)$m[1];
                $filters['price_max'] = (int)$m[2];
            }
        }
        $age = trim($_GET['age'] ?? '');
        if ($age !== '') {
            if ($age === '13+') {
                $filters['age_min'] = 13;
                $filters['age_max'] = 99;
            } elseif (preg_match('/^(\d+)\-(\d+)$/', $age, $m)) {
                $filters['age_min'] = (int)$m[1];
                $filters['age_max'] = (int)$m[2];
            }
        }
        $products = Product::paginateActiveFiltered($page, $perPage, $sort, $filters);
        $categories = Category::all();
        $total = Product::countActiveFiltered($filters);
        $pages = max(1, (int)ceil($total / $perPage));
        $this->render('catalog/index', [
            'products' => $products,
            'categories' => $categories,
            'page' => $page,
            'pages' => $pages,
            'sort' => $sort,
            'total' => $total,
            'perPage' => $perPage,
            'cat' => $cat,
            'gender' => $gender,
            'age' => $age,
            'price' => $price,
            'q' => $q
        ]);
    }
    public function show(): void {
        $id = (int)($_GET['id'] ?? 0);
        $product = $id ? Product::find($id) : null;
        if (!$product) {
            http_response_code(404);
            echo '404';
            return;
        }
        $this->render('product/show', ['product' => $product]);
    }
    public function search(): void {
        $q = trim($_GET['q'] ?? '');
        $results = $q === '' ? [] : Product::search($q);
        json_response(['results' => $results]);
    }
}
