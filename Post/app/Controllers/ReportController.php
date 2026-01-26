<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\ReportService;

final class ReportController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requirePermission('reports.view')) {
            return $redirect;
        }
        $reports = (new ReportService())->reports();
        return $this->view('reports/index', ['reports' => $reports]);
    }
}
