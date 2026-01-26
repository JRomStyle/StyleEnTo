<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;

final class InventoryRepository
{
    public function summary(): array
    {
        $ingredients = DB::connection()->query('SELECT i.id, i.name, i.unit, inv.quantity, inv.min_stock FROM ingredients i INNER JOIN inventory inv ON inv.ingredient_id = i.id ORDER BY i.name')->fetchAll();
        return $ingredients;
    }

    public function adjust(int $ingredientId, float $quantity, string $reason): void
    {
        $pdo = DB::connection();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE inventory SET quantity = quantity + :qty WHERE ingredient_id = :ingredient_id');
        $stmt->execute(['qty' => $quantity, 'ingredient_id' => $ingredientId]);
        $movement = $pdo->prepare('INSERT INTO stock_movements (ingredient_id, quantity, reason, created_at) VALUES (:ingredient_id, :quantity, :reason, NOW())');
        $movement->execute([
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity,
            'reason' => $reason
        ]);
        $pdo->commit();
    }
}
