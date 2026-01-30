<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\InventoryRepository;
use App\Repositories\RecipeRepository;

final class InventoryService
{
    public function summary(): array
    {
        $items = (new InventoryRepository())->summary();
        $lowStock = array_filter($items, fn ($row) => (float)$row['quantity'] <= (float)$row['min_stock']);
        return [
            'items' => $items,
            'lowStock' => $lowStock
        ];
    }

    public function adjust(int $ingredientId, float $quantity, string $reason): void
    {
        (new InventoryRepository())->adjust($ingredientId, $quantity, $reason);
    }

    public function consumeFromOrder(array $items): void
    {
        $recipeRepo = new RecipeRepository();
        $inventoryRepo = new InventoryRepository();
        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $qty = (int)$item['quantity'];
            $ingredients = $recipeRepo->ingredientsForProduct($productId);
            foreach ($ingredients as $ingredient) {
                $consume = -1 * ((float)$ingredient['quantity']) * $qty;
                $inventoryRepo->adjust((int)$ingredient['ingredient_id'], $consume, 'venta');
            }
        }
    }
}
