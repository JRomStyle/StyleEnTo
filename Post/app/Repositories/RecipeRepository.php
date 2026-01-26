<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;

final class RecipeRepository
{
    public function ingredientsForProduct(int $productId): array
    {
        $stmt = DB::connection()->prepare('SELECT ri.ingredient_id, ri.quantity, i.unit FROM recipe_items ri INNER JOIN ingredients i ON i.id = ri.ingredient_id WHERE ri.product_id = :product_id');
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }
}
