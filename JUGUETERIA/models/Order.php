<?php
class Order {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function createOrder($userId, $totalAmount, $cartItems){
        // Begin Transaction (Manual or implicit via DB class if updated, here we use simple queries)
        // Ideally DB class should support transactions. PDO does.
        // My DB class doesn't expose beginTransaction. 
        // I will just simple inserts.

        // 1. Insert Order
        $this->db->query('INSERT INTO orders (user_id, total_amount, status) VALUES(:user_id, :total_amount, :status)');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':total_amount', $totalAmount);
        $this->db->bind(':status', 'completed');
        
        if($this->db->execute()){
            // Get Last Insert ID
            // My DB class doesn't have lastInsertId method exposed?
            // "SELECT LAST_INSERT_ID()" 
            $this->db->query('SELECT LAST_INSERT_ID() as id');
            $row = $this->db->single();
            $orderId = $row->id;

            // 2. Insert Details & Reduce Stock
            foreach($cartItems as $item){
                $this->db->query('INSERT INTO order_details (order_id, product_id, quantity, price) VALUES(:order_id, :product_id, :quantity, :price)');
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->execute();

                // Reduce Stock
                $this->db->query('UPDATE products SET stock = stock - :qty WHERE id = :id');
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':id', $item['product_id']);
                $this->db->execute();
                
                // Inventory Log
                $this->db->query('INSERT INTO inventory_logs (product_id, user_id, type, quantity, note) VALUES(:product_id, :user_id, :type, :quantity, :note)');
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':type', 'sale');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':note', 'Venta Orden #' . $orderId);
                $this->db->execute();
            }

            return true;
        } else {
            return false;
        }
    }

    public function getOrdersByUserId($userId){
        $this->db->query('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    
    public function getAllOrders(){
        $this->db->query('SELECT orders.*, users.name as user_name FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC');
        return $this->db->resultSet();
    }

    public function getTotalSales(){
        $this->db->query('SELECT SUM(total_amount) as total FROM orders WHERE status = "completed"');
        $row = $this->db->single();
        return $row->total;
    }

    public function getPendingOrdersCount(){
        $this->db->query('SELECT COUNT(*) as count FROM orders WHERE status = "pending"');
        $row = $this->db->single();
        return $row->count;
    }
}
