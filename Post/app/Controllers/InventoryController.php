<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Security\Csrf;
use App\Services\InventoryService;
use App\Services\AuditService;

final class InventoryController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requirePermission('inventory.view')) {
            return $redirect;
        }
        $data = (new InventoryService())->summary();
        return $this->view('inventory/index', $data);
    }

    public function adjust(Request $request)
    {
        if ($redirect = $this->requirePermission('inventory.adjust')) {
            return $redirect;
        }
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['error' => 'Invalid CSRF'], 419);
        }
        $ingredientId = (int)$request->input('ingredient_id');
        $quantity = (float)$request->input('quantity');
        $reason = (string)$request->input('reason');
        (new InventoryService())->adjust($ingredientId, $quantity, $reason);
        (new AuditService())->log('inventory_adjust', null, ['ingredient_id' => $ingredientId, 'quantity' => $quantity]);
        return $this->json(['ok' => true]);
    }
}
