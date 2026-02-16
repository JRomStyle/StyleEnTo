<?php
class Admin extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        if(!isAdmin()){
            redirect('pages/index');
        }
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->userModel = $this->model('User');
    }

    public function dashboard(){
        $totalSales = $this->orderModel->getTotalSales();
        $pendingOrders = $this->orderModel->getPendingOrdersCount();
        $lowStock = $this->productModel->getLowStockCount();
        $userCount = $this->userModel->getUserCount();

        $data = [
            'title' => 'Admin Dashboard',
            'total_sales' => $totalSales,
            'pending_orders' => $pendingOrders,
            'low_stock' => $lowStock,
            'user_count' => $userCount
        ];
        
        $this->view('admin/dashboard', $data);
    }
}
