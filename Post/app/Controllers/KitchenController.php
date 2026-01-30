<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Security\Csrf;
use App\Services\KitchenService;
use App\Services\AuditService;

final class KitchenController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requirePermission('kitchen.view')) {
            return $redirect;
        }
        $orders = (new KitchenService())->queue();
        return $this->view('kitchen/index', ['orders' => $orders]);
    }

    public function updateStatus(Request $request)
    {
        if ($redirect = $this->requirePermission('kitchen.update')) {
            return $redirect;
        }
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['error' => 'Invalid CSRF'], 419);
        }
        $orderId = (int)$request->input('order_id');
        $status = (string)$request->input('status');
        (new KitchenService())->updateStatus($orderId, $status);
        (new AuditService())->log('order_status', null, ['order_id' => $orderId, 'status' => $status]);
        return $this->json(['ok' => true]);
    }
}
