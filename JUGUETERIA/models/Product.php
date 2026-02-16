<?php
class Product {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getProducts(){
        $this->db->query('SELECT *,
                        products.id as productId,
                        users.id as userId,
                        products.created_at as productCreated,
                        users.created_at as userCreated
                        FROM products
                        LEFT JOIN users ON products.user_id = users.id
                        ORDER BY products.created_at DESC
                        '); 
        // Wait, products table doesn't have user_id in my schema?
        // Let me check schema.
        // products: id, category_id, name, description, price, stock, image, status...
        // No user_id.
        
        $this->db->query('SELECT products.*, categories.name as category_name 
                          FROM products
                          JOIN categories ON products.category_id = categories.id
                          ORDER BY products.created_at DESC');

        return $this->db->resultSet();
    }

    public function addProduct($data){
        $this->db->query('INSERT INTO products (category_id, name, description, price, stock, image, status) VALUES(:category_id, :name, :description, :price, :stock, :image, :status)');
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':status', $data['status']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function updateProduct($data){
        $this->db->query('UPDATE products SET category_id = :category_id, name = :name, description = :description, price = :price, stock = :stock, image = :image, status = :status WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':stock', $data['stock']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':status', $data['status']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getProductById($id){
        $this->db->query('SELECT * FROM products WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    public function deleteProduct($id){
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getLowStockCount(){
        $this->db->query('SELECT COUNT(*) as count FROM products WHERE stock < 5');
        $row = $this->db->single();
        return $row->count;
    }
}
