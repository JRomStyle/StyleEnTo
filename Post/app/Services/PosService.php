<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;

final class PosService
{
    public function getCatalog(): array
    {
        $categories = (new ProductRepository())->categoriesWithProducts();
        $orderTypes = ['mesa', 'llevar', 'delivery'];
        $paymentMethods = ['efectivo', 'tarjeta', 'transferencia', 'nequi', 'daviplata'];
        return [
            'categories' => $categories,
            'orderTypes' => $orderTypes,
            'paymentMethods' => $paymentMethods
        ];
    }

    public function createOrder(mixed $payload): array
    {
        $data = is_string($payload) ? json_decode($payload, true) : (array)$payload;
        $items = $data['items'] ?? [];
        $total = 0.0;
        foreach ($items as $item) {
            $total += ((float)$item['price']) * ((int)$item['quantity']);
        }
        $user = Auth::user();
        $branchId = $user['branch_id'] ?? ($data['branch_id'] ?? 1);
        if (!Auth::can('branches.view') && $user) {
            $branchId = $user['branch_id'];
        }
        $order = [
            'branch_id' => $branchId,
            'user_id' => $user['id'] ?? ($data['user_id'] ?? 1),
            'order_type' => $data['order_type'] ?? 'llevar',
            'status' => 'pending',
            'table_id' => $data['table_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'notes' => $data['notes'] ?? '',
            'total' => $total
        ];
        $payment = [
            'method' => $data['payment_method'] ?? 'efectivo',
            'amount' => $total
        ];
        $created = (new OrderRepository())->create($order, $items, $payment);
        (new InventoryService())->consumeFromOrder($items);
        return $created;
    }
}
