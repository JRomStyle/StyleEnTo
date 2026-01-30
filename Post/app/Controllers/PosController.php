<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Security\Csrf;
use App\Services\PosService;
use App\Services\AuditService;

final class PosController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requirePermission('pos.view')) {
            return $redirect;
        }
        $data = (new PosService())->getCatalog();
        return $this->view('pos/index', $data);
    }

    public function createOrder(Request $request)
    {
        if ($redirect = $this->requirePermission('pos.create')) {
            return $redirect;
        }
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['error' => 'Invalid CSRF'], 419);
        }
        $payload = $request->input('payload');
        $order = (new PosService())->createOrder($payload);
        (new AuditService())->log('order_created', null, ['order_id' => $order['id']]);
        return $this->json(['order' => $order]);
    }
}
