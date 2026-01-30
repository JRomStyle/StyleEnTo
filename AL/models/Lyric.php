<?php

require_once __DIR__ . '/../core/DB.php';

class Lyric {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO lyrics (user_id, title, content, price, is_exclusive, status) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['title'],
            $data['content'],
            $data['price'],
            $data['is_exclusive'] ?? false,
            $data['status'] ?? 'draft'
        ]);
        
        $lyricId = $this->db->lastInsertId();

        // Asociar géneros si se proporcionan
        if (isset($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genreId) {
                $this->addGenre($lyricId, $genreId);
            }
        }

        return $lyricId;
    }

    public function getById($id) {
        $sql = "SELECT l.*, u.username, u.full_name, u.profile_image FROM lyrics l JOIN users u ON l.user_id = u.id WHERE l.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $lyric = $stmt->fetch();

        if ($lyric) {
            // Obtener géneros
            $sql = "SELECT g.id, g.name FROM lyric_genres lg JOIN genres g ON lg.genre_id = g.id WHERE lg.lyric_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $lyric['genres'] = $stmt->fetchAll();
        }

        return $lyric;
    }

    public function getAll($filters = []) {
        $sql = "SELECT l.*, u.username, u.full_name, u.profile_image FROM lyrics l JOIN users u ON l.user_id = u.id WHERE l.status = 'published'";
        $params = [];

        // Aplicar filtros
        if (isset($filters['genre_id'])) {
            $sql .= " AND l.id IN (SELECT lyric_id FROM lyric_genres WHERE genre_id = ?)";
            $params[] = $filters['genre_id'];
        }

        if (isset($filters['is_exclusive'])) {
            $sql .= " AND l.is_exclusive = ?";
            $params[] = $filters['is_exclusive'];
        }

        if (isset($filters['min_price'])) {
            $sql .= " AND l.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $sql .= " AND l.price <= ?";
            $params[] = $filters['max_price'];
        }

        $sql .= " ORDER BY l.created_at DESC";
        $stmt = $this->db->query($sql, $params);
        $lyrics = $stmt->fetchAll();

        // Obtener géneros para cada letra
        foreach ($lyrics as &$lyric) {
            $sql = "SELECT g.id, g.name FROM lyric_genres lg JOIN genres g ON lg.genre_id = g.id WHERE lg.lyric_id = ?";
            $stmt = $this->db->query($sql, [$lyric['id']]);
            $lyric['genres'] = $stmt->fetchAll();
        }

        return $lyrics;
    }

    public function getByUserId($userId, $status = null) {
        $sql = "SELECT * FROM lyrics WHERE user_id = ?";
        $params = [$userId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, $params);
        $lyrics = $stmt->fetchAll();

        // Obtener géneros para cada letra
        foreach ($lyrics as &$lyric) {
            $sql = "SELECT g.id, g.name FROM lyric_genres lg JOIN genres g ON lg.genre_id = g.id WHERE lg.lyric_id = ?";
            $stmt = $this->db->query($sql, [$lyric['id']]);
            $lyric['genres'] = $stmt->fetchAll();
        }

        return $lyrics;
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['title'])) {
            $updateFields[] = "title = ?";
            $params[] = $data['title'];
        }

        if (isset($data['content'])) {
            $updateFields[] = "content = ?";
            $params[] = $data['content'];
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

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE lyrics SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);

        // Actualizar géneros si se proporcionan
        if (isset($data['genres']) && is_array($data['genres'])) {
            // Eliminar géneros existentes
            $sql = "DELETE FROM lyric_genres WHERE lyric_id = ?";
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
        $sql = "DELETE FROM lyric_genres WHERE lyric_id = ?";
        $this->db->query($sql, [$id]);
        
        // Eliminar letra
        $sql = "DELETE FROM lyrics WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return ['success' => 'Letra eliminada correctamente'];
    }

    public function addGenre($lyricId, $genreId) {
        $sql = "INSERT IGNORE INTO lyric_genres (lyric_id, genre_id) VALUES (?, ?)";
        $this->db->query($sql, [$lyricId, $genreId]);
    }

    public function removeGenre($lyricId, $genreId) {
        $sql = "DELETE FROM lyric_genres WHERE lyric_id = ? AND genre_id = ?";
        $this->db->query($sql, [$lyricId, $genreId]);
    }

    public function purchase($lyricId, $buyerId, $licenseId) {
        // Obtener letra
        $lyric = $this->getById($lyricId);
        if (!$lyric || $lyric['status'] !== 'published') {
            return ['error' => 'Letra no disponible para compra'];
        }

        // Verificar si es exclusiva y ya está vendida
        if ($lyric['is_exclusive'] && $lyric['status'] === 'sold') {
            return ['error' => 'Esta letra es exclusiva y ya ha sido vendida'];
        }

        // Crear venta
        $sql = "INSERT INTO sales (seller_id, buyer_id, item_type, item_id, license_id, price, currency, status, payment_method) VALUES (?, ?, 'lyric', ?, ?, ?, 'USD', 'completed', 'stripe')";
        $this->db->query($sql, [
            $lyric['user_id'],
            $buyerId,
            $lyricId,
            $licenseId,
            $lyric['price']
        ]);

        // Marcar como vendida si es exclusiva
        if ($lyric['is_exclusive']) {
            $sql = "UPDATE lyrics SET status = 'sold' WHERE id = ?";
            $this->db->query($sql, [$lyricId]);
        }

        return ['success' => 'Compra realizada correctamente', 'sale_id' => $this->db->lastInsertId()];
    }
}
