<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Core\Auth;

final class KitchenService
{
    public function queue(): array
    {
        $user = Auth::user();
        $branchId = $user['branch_id'] ?? null;
        return (new OrderRepository())->kitchenQueue($branchId ? (int)$branchId : null);
    }

    public function updateStatus(int $orderId, string $status): void
    {
        (new OrderRepository())->updateStatus($orderId, $status);
    }
}
