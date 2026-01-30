<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\BranchService;

final class BranchController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requirePermission('branches.view')) {
            return $redirect;
        }
        $branches = (new BranchService())->all();
        return $this->view('branches/index', ['branches' => $branches]);
    }
}
