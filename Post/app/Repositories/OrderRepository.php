<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;
use PDO;

final class OrderRepository
{
    public function create(array $order, array $items, array $payment): array
    {
        $pdo = DB::connection();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO orders (branch_id, user_id, order_type, status, table_id, customer_id, notes, total, created_at) VALUES (:branch_id, :user_id, :order_type, :status, :table_id, :customer_id, :notes, :total, NOW())');
        $stmt->execute([
            'branch_id' => $order['branch_id'],
            'user_id' => $order['user_id'],
            'order_type' => $order['order_type'],
            'status' => $order['status'],
            'table_id' => $order['table_id'],
            'customer_id' => $order['customer_id'],
            'notes' => $order['notes'],
            'total' => $order['total']
        ]);
        $orderId = (int)$pdo->lastInsertId();
        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price, extras, notes) VALUES (:order_id, :product_id, :quantity, :price, :extras, :notes)');
        foreach ($items as $item) {
            $itemStmt->execute([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'extras' => $item['extras'],
                'notes' => $item['notes']
            ]);
        }
        $paymentStmt = $pdo->prepare('INSERT INTO payments (order_id, method, amount, created_at) VALUES (:order_id, :method, :amount, NOW())');
        $paymentStmt->execute([
            'order_id' => $orderId,
            'method' => $payment['method'],
            'amount' => $payment['amount']
        ]);
        $pdo->commit();
        return $this->findById($orderId);
    }

    public function findById(int $id): array
    {
        $stmt = DB::connection()->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        if (!$order) {
            return [];
        }
        $items = DB::connection()->prepare('SELECT oi.*, p.name FROM order_items oi INNER JOIN products p ON p.id = oi.product_id WHERE oi.order_id = :order_id');
        $items->execute(['order_id' => $id]);
        $order['items'] = $items->fetchAll();
        return $order;
    }

    public function kitchenQueue(?int $branchId = null): array
    {
        $sql = "SELECT * FROM orders WHERE status IN ('pending','preparing')";
        $params = [];
        
        if ($branchId) {
            $sql .= " AND branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }
        
        $sql .= " ORDER BY created_at ASC";
        
        $stmt = DB::connection()->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $itemStmt = DB::connection()->prepare('SELECT oi.*, p.name FROM order_items oi INNER JOIN products p ON p.id = oi.product_id WHERE oi.order_id = :order_id');
            $itemStmt->execute(['order_id' => $order['id']]);
            $order['items'] = $itemStmt->fetchAll();
        }
        
        return $orders;
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $stmt = DB::connection()->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $orderId]);
    }
}
