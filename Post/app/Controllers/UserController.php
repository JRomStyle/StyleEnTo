<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\UserService;

final class UserController extends BaseController
{
    public function index(Request $request)
    {
        if ($redirect = $this->requirePermission('users.view')) {
            return $redirect;
        }
        $users = (new UserService())->all();
        return $this->view('users/index', ['users' => $users]);
    }
}
