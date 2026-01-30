<?php

require_once __DIR__ . '/../core/DB.php';

class Beat {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO beats (user_id, title, description, file_path, duration, price, is_exclusive, status, bpm, musical_key) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['file_path'],
            $data['duration'],
            $data['price'],
            $data['is_exclusive'] ?? false,
            $data['status'] ?? 'draft',
            $data['bpm'] ?? null,
            $data['key'] ?? null
        ]);
        
        $beatId = $this->db->lastInsertId();

        // Asociar géneros si se proporcionan
        if (isset($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genreId) {
                $this->addGenre($beatId, $genreId);
            }
        }

        return $beatId;
    }

    public function getById($id) {
        $sql = "SELECT b.*, u.username, u.full_name, u.profile_image FROM beats b JOIN users u ON b.user_id = u.id WHERE b.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $beat = $stmt->fetch();

        if ($beat) {
            // Obtener géneros
            $sql = "SELECT g.id, g.name FROM beat_genres bg JOIN genres g ON bg.genre_id = g.id WHERE bg.beat_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $beat['genres'] = $stmt->fetchAll();
        }

        return $beat;
    }

    public function getAll($filters = []) {
        $sql = "SELECT b.*, u.username, u.full_name, u.profile_image FROM beats b JOIN users u ON b.user_id = u.id WHERE b.status = 'published'";
        $params = [];

        // Aplicar filtros
        if (isset($filters['genre_id'])) {
            $sql .= " AND b.id IN (SELECT beat_id FROM beat_genres WHERE genre_id = ?)";
            $params[] = $filters['genre_id'];
        }

        if (isset($filters['is_exclusive'])) {
            $sql .= " AND b.is_exclusive = ?";
            $params[] = $filters['is_exclusive'];
        }

        if (isset($filters['min_price'])) {
            $sql .= " AND b.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $sql .= " AND b.price <= ?";
            $params[] = $filters['max_price'];
        }

        $sql .= " ORDER BY b.created_at DESC";
        $stmt = $this->db->query($sql, $params);
        $beats = $stmt->fetchAll();

        // Obtener géneros para cada beat
        foreach ($beats as &$beat) {
            $sql = "SELECT g.id, g.name FROM beat_genres bg JOIN genres g ON bg.genre_id = g.id WHERE bg.beat_id = ?";
            $stmt = $this->db->query($sql, [$beat['id']]);
            $beat['genres'] = $stmt->fetchAll();
        }

        return $beats;
    }

    public function getByUserId($userId, $status = null) {
        $sql = "SELECT * FROM beats WHERE user_id = ?";
        $params = [$userId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, $params);
        $beats = $stmt->fetchAll();

        // Obtener géneros para cada beat
        foreach ($beats as &$beat) {
            $sql = "SELECT g.id, g.name FROM beat_genres bg JOIN genres g ON bg.genre_id = g.id WHERE bg.beat_id = ?";
            $stmt = $this->db->query($sql, [$beat['id']]);
            $beat['genres'] = $stmt->fetchAll();
        }

        return $beats;
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

        if (isset($data['file_path'])) {
            $updateFields[] = "file_path = ?";
            $params[] = $data['file_path'];
        }

        if (isset($data['duration'])) {
            $updateFields[] = "duration = ?";
            $params[] = $data['duration'];
        }

        if (isset($data['price'])) {
            $updateFields[] = "price = ?";
            $params[] = $data['price'];
        }

        if (isset($data['is_exclusive'])) {
            $updateFields[] = "is_exclusive = ?";
            $params[] = $data['is_exclusive'];
        }

        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (isset($data['bpm'])) {
            $updateFields[] = "bpm = ?";
            $params[] = $data['bpm'];
        }

        if (isset($data['key'])) {
            $updateFields[] = "musical_key = ?";
            $params[] = $data['key'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE beats SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);

        // Actualizar géneros si se proporcionan
        if (isset($data['genres']) && is_array($data['genres'])) {
            // Eliminar géneros existentes
            $sql = "DELETE FROM beat_genres WHERE beat_id = ?";
            $this->db->query($sql, [$id]);
            
            // Agregar nuevos géneros
            foreach ($data['genres'] as $genreId) {
                $this->addGenre($id, $genreId);
            }
        }

        return $this->getById($id);
    }

    public function delete($id) {
        // Eliminar relaciones de géneros
        $sql = "DELETE FROM beat_genres WHERE beat_id = ?";
        $this->db->query($sql, [$id]);
        
        // Eliminar beat
        $sql = "DELETE FROM beats WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return ['success' => 'Beat eliminado correctamente'];
    }

    public function addGenre($beatId, $genreId) {
        $sql = "INSERT IGNORE INTO beat_genres (beat_id, genre_id) VALUES (?, ?)";
        $this->db->query($sql, [$beatId, $genreId]);
    }

    public function removeGenre($beatId, $genreId) {
        $sql = "DELETE FROM beat_genres WHERE beat_id = ? AND genre_id = ?";
        $this->db->query($sql, [$beatId, $genreId]);
    }

    public function purchase($beatId, $buyerId, $licenseId) {
        // Obtener beat
        $beat = $this->getById($beatId);
        if (!$beat || $beat['status'] !== 'published') {
            return ['error' => 'Beat no disponible para compra'];
        }

        // Verificar si es exclusivo y ya está vendido
        if ($beat['is_exclusive'] && $beat['status'] === 'sold') {
            return ['error' => 'Este beat es exclusivo y ya ha sido vendido'];
        }

        // Crear venta
        $sql = "INSERT INTO sales (seller_id, buyer_id, item_type, item_id, license_id, price, currency, status, payment_method) VALUES (?, ?, 'beat', ?, ?, ?, 'USD', 'completed', 'stripe')";
        $this->db->query($sql, [
            $beat['user_id'],
            $buyerId,
            $beatId,
            $licenseId,
            $beat['price']
        ]);

        // Marcar como vendido si es exclusivo
        if ($beat['is_exclusive']) {
            $sql = "UPDATE beats SET status = 'sold' WHERE id = ?";
            $this->db->query($sql, [$beatId]);
        }

        return ['success' => 'Compra realizada correctamente', 'sale_id' => $this->db->lastInsertId()];
    }
}
