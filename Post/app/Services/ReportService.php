<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Core\Auth;

final class ReportService
{
    public function overview(): array
    {
        $pdo = DB::connection();
        $user = Auth::user();
        $branchId = $user['branch_id'] ?? null;
        if ($branchId && !Auth::can('branches.view')) {
            $salesToday = $pdo->prepare("SELECT COALESCE(SUM(total),0) as total FROM orders WHERE DATE(created_at) = CURDATE() AND branch_id = :branch_id");
            $salesToday->execute(['branch_id' => $branchId]);
            $ordersToday = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE() AND branch_id = :branch_id");
            $ordersToday->execute(['branch_id' => $branchId]);
            $pending = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE status IN ('pending','preparing') AND branch_id = :branch_id");
            $pending->execute(['branch_id' => $branchId]);
            $salesToday = $salesToday->fetch();
            $ordersToday = $ordersToday->fetch();
            $pending = $pending->fetch();
        } else {
            $salesToday = $pdo->query("SELECT COALESCE(SUM(total),0) as total FROM orders WHERE DATE(created_at) = CURDATE()")->fetch();
            $ordersToday = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()")->fetch();
            $pending = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status IN ('pending','preparing')")->fetch();
        }
        return [
            'salesToday' => (float)$salesToday['total'],
            'ordersToday' => (int)$ordersToday['total'],
            'pendingOrders' => (int)$pending['total']
        ];
    }

    public function reports(): array
    {
        $pdo = DB::connection();
        $user = Auth::user();
        $branchId = $user['branch_id'] ?? null;
        if ($branchId && !Auth::can('branches.view')) {
            $topProducts = $pdo->prepare('SELECT p.name, SUM(oi.quantity) as qty FROM order_items oi INNER JOIN products p ON p.id = oi.product_id INNER JOIN orders o ON o.id = oi.order_id WHERE o.branch_id = :branch_id GROUP BY p.id ORDER BY qty DESC LIMIT 5');
            $topProducts->execute(['branch_id' => $branchId]);
            $salesByBranch = $pdo->prepare('SELECT b.name, COALESCE(SUM(o.total),0) as total FROM branches b LEFT JOIN orders o ON o.branch_id = b.id WHERE b.id = :branch_id GROUP BY b.id ORDER BY total DESC');
            $salesByBranch->execute(['branch_id' => $branchId]);
            $topProducts = $topProducts->fetchAll();
            $salesByBranch = $salesByBranch->fetchAll();
        } else {
            $topProducts = $pdo->query('SELECT p.name, SUM(oi.quantity) as qty FROM order_items oi INNER JOIN products p ON p.id = oi.product_id GROUP BY p.id ORDER BY qty DESC LIMIT 5')->fetchAll();
            $salesByBranch = $pdo->query('SELECT b.name, COALESCE(SUM(o.total),0) as total FROM branches b LEFT JOIN orders o ON o.branch_id = b.id GROUP BY b.id ORDER BY total DESC')->fetchAll();
        }
        return [
            'topProducts' => $topProducts,
            'salesByBranch' => $salesByBranch
        ];
    }
}
