<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\ReportService;

final class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }
        $stats = (new ReportService())->overview();
        return $this->view('dashboard/home', ['stats' => $stats]);
    }
}
