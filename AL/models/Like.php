<?php

require_once __DIR__ . '/../core/DB.php';

class Like {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function like($userId, $postId) {
        // Verificar si ya existe el like
        $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $this->db->query($sql, [$userId, $postId]);
        if ($stmt->rowCount() > 0) {
            return ['error' => 'Ya has dado like a esta publicación'];
        }

        // Insertar like
        $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $this->db->query($sql, [$userId, $postId]);

        // Actualizar contador de likes en la publicación
        $sql = "UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?";
        $this->db->query($sql, [$postId]);

        return ['success' => 'Like agregado correctamente'];
    }

    public function unlike($userId, $postId) {
        // Verificar si existe el like
        $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $this->db->query($sql, [$userId, $postId]);
        if ($stmt->rowCount() === 0) {
            return ['error' => 'No has dado like a esta publicación'];
        }

        // Eliminar like
        $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $this->db->query($sql, [$userId, $postId]);

        // Actualizar contador de likes en la publicación
        $sql = "UPDATE posts SET likes_count = likes_count - 1 WHERE id = ? AND likes_count > 0";
        $this->db->query($sql, [$postId]);

        return ['success' => 'Like eliminado correctamente'];
    }

    public function checkLike($userId, $postId) {
        $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $this->db->query($sql, [$userId, $postId]);
        return $stmt->rowCount() > 0;
    }

    public function getLikesByPost($postId, $limit = 10) {
        $sql = "SELECT u.id, u.username, u.full_name, u.profile_image, l.created_at FROM likes l 
                JOIN users u ON l.user_id = u.id 
                WHERE l.post_id = ? 
                ORDER BY l.created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$postId, $limit]);
        return $stmt->fetchAll();
    }
}
