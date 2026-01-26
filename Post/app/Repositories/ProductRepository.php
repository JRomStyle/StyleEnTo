<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;

final class ProductRepository
{
    public function categoriesWithProducts(): array
    {
        $categories = DB::connection()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
        $products = DB::connection()->query('SELECT * FROM products WHERE active = 1 ORDER BY name')->fetchAll();
        $byCategory = [];
        foreach ($products as $product) {
            $byCategory[$product['category_id']][] = $product;
        }
        foreach ($categories as &$category) {
            $category['products'] = $byCategory[$category['id']] ?? [];
        }
        return $categories;
    }
}
