<?php

require_once __DIR__ . '/../core/DB.php';

class Service {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO services (user_id, title, description, price, category, delivery_time, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['delivery_time'],
            $data['status'] ?? 'active'
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT s.*, u.username, u.full_name, u.profile_image FROM services s JOIN users u ON s.user_id = u.id WHERE s.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getAll($filters = []) {
        $sql = "SELECT s.*, u.username, u.full_name, u.profile_image FROM services s JOIN users u ON s.user_id = u.id WHERE s.status = 'active'";
        $params = [];

        // Aplicar filtros
        if (isset($filters['category'])) {
            $sql .= " AND s.category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['min_price'])) {
            $sql .= " AND s.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $sql .= " AND s.price <= ?";
            $params[] = $filters['max_price'];
        }

        $sql .= " ORDER BY s.created_at DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function getByUserId($userId, $status = null) {
        $sql = "SELECT * FROM services WHERE user_id = ?";
        $params = [$userId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['title'])) {
            $updateFields[] = "title = ?";
            $params[] = $data['title'];
        }

        if (isset($data['description'])) {
            $updateFields[] = "description = ?";
            $params[] = $data['description'];
        }

        if (isset($data['price'])) {
            $updateFields[] = "price = ?";
            $params[] = $data['price'];
        }

        if (isset($data['category'])) {
            $updateFields[] = "category = ?";
            $params[] = $data['category'];
        }

        if (isset($data['delivery_time'])) {
            $updateFields[] = "delivery_time = ?";
            $params[] = $data['delivery_time'];
        }

        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE services SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);

        return $this->getById($id);
    }

    public function delete($id) {
        $sql = "DELETE FROM services WHERE id = ?";
        $this->db->query($sql, [$id]);
        return ['success' => 'Servicio eliminado correctamente'];
    }

    public function purchase($serviceId, $buyerId) {
        // Obtener servicio
        $service = $this->getById($serviceId);
        if (!$service || $service['status'] !== 'active') {
            return ['error' => 'Servicio no disponible para compra'];
        }

        // Crear venta
        $sql = "INSERT INTO sales (seller_id, buyer_id, item_type, item_id, price, currency, status, payment_method) VALUES (?, ?, 'service', ?, ?, 'USD', 'completed', 'stripe')";
        $this->db->query($sql, [
            $service['user_id'],
            $buyerId,
            $serviceId,
            $service['price']
        ]);

        return ['success' => 'Compra realizada correctamente', 'sale_id' => $this->db->lastInsertId()];
    }
}
