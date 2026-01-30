<?php

require_once __DIR__ . '/../core/DB.php';

class Album {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO albums (user_id, title, cover_image, release_date, description, upc, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['title'],
            $data['cover_image'] ?? null,
            $data['release_date'] ?? null,
            $data['description'] ?? null,
            $data['upc'] ?? null,
            $data['status'] ?? 'draft'
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT * FROM albums WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getByUserId($userId, $status = null) {
        $sql = "SELECT * FROM albums WHERE user_id = ?";
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

        if (isset($data['cover_image'])) {
            $updateFields[] = "cover_image = ?";
            $params[] = $data['cover_image'];
        }

        if (isset($data['release_date'])) {
            $updateFields[] = "release_date = ?";
            $params[] = $data['release_date'];
        }

        if (isset($data['description'])) {
            $updateFields[] = "description = ?";
            $params[] = $data['description'];
        }

        if (isset($data['upc'])) {
            $updateFields[] = "upc = ?";
            $params[] = $data['upc'];
        }

        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE albums SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);
        return $this->getById($id);
    }

    public function delete($id) {
        $sql = "DELETE FROM albums WHERE id = ?";
        $this->db->query($sql, [$id]);
        return ['success' => 'Álbum eliminado correctamente'];
    }
}
