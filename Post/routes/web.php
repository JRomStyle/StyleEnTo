<?php
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PosController;
use App\Controllers\KitchenController;
use App\Controllers\InventoryController;
use App\Controllers\ReportController;
use App\Controllers\BranchController;
use App\Controllers\UserController;

$router->get('/', [DashboardController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->get('/admin-reset', [AuthController::class, 'showAdminReset']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/admin-reset', [AuthController::class, 'resetAdmin']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/pos', [PosController::class, 'index']);
$router->post('/pos/order', [PosController::class, 'createOrder']);
$router->get('/kitchen', [KitchenController::class, 'index']);
$router->post('/kitchen/status', [KitchenController::class, 'updateStatus']);
$router->get('/inventory', [InventoryController::class, 'index']);
$router->post('/inventory/adjust', [InventoryController::class, 'adjust']);
$router->get('/reports', [ReportController::class, 'index']);
$router->get('/branches', [BranchController::class, 'index']);
$router->get('/users', [UserController::class, 'index']);
