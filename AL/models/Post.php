<?php

require_once __DIR__ . '/../core/DB.php';

class Post {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO posts (user_id, content, media_type, media_url, status) VALUES (?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['content'],
            $data['media_type'] ?? 'text',
            $data['media_url'] ?? null,
            $data['status'] ?? 'published'
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT p.*, u.username, u.full_name, u.profile_image FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $post = $stmt->fetch();

        if ($post) {
            // Obtener likes count
            $sql = "SELECT COUNT(*) as likes_count FROM likes WHERE post_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $post['likes_count'] = $stmt->fetch()['likes_count'];

            // Obtener comments count
            $sql = "SELECT COUNT(*) as comments_count FROM comments WHERE post_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $post['comments_count'] = $stmt->fetch()['comments_count'];
        }

        return $post;
    }

    public function getFeed($userId, $limit = 20, $offset = 0) {
        // Obtener posts de usuarios que sigue el usuario
        $sql = "SELECT p.*, u.username, u.full_name, u.profile_image FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE (p.user_id = ? OR p.user_id IN (SELECT followed_id FROM followers WHERE follower_id = ?)) 
                AND p.status = 'published' 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$userId, $userId, $limit, $offset]);
        $posts = $stmt->fetchAll();

        // Obtener likes y comments count para cada post
        foreach ($posts as &$post) {
            // Likes count
            $sql = "SELECT COUNT(*) as likes_count FROM likes WHERE post_id = ?";
            $stmt = $this->db->query($sql, [$post['id']]);
            $post['likes_count'] = $stmt->fetch()['likes_count'];

            // Comments count
            $sql = "SELECT COUNT(*) as comments_count FROM comments WHERE post_id = ?";
            $stmt = $this->db->query($sql, [$post['id']]);
            $post['comments_count'] = $stmt->fetch()['comments_count'];

            // Verificar si el usuario actual le dio like
            $sql = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
            $stmt = $this->db->query($sql, [$post['id'], $userId]);
            $post['user_liked'] = $stmt->rowCount() > 0;
        }

        return $posts;
    }

    public function getByUserId($userId, $status = 'published') {
        $sql = "SELECT p.*, u.username, u.full_name, u.profile_image FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id = ? AND p.status = ? 
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql, [$userId, $status]);
        $posts = $stmt->fetchAll();

        // Obtener likes y comments count para cada post
        foreach ($posts as &$post) {
            // Likes count
            $sql = "SELECT COUNT(*) as likes_count FROM likes WHERE post_id = ?";
            $stmt = $this->db->query($sql, [$post['id']]);
            $post['likes_count'] = $stmt->fetch()['likes_count'];

            // Comments count
            $sql = "SELECT COUNT(*) as comments_count FROM comments WHERE post_id = ?";
            $stmt = $this->db->query($sql, [$post['id']]);
            $post['comments_count'] = $stmt->fetch()['comments_count'];
        }

        return $posts;
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['content'])) {
            $updateFields[] = "content = ?";
            $params[] = $data['content'];
        }

        if (isset($data['media_type'])) {
            $updateFields[] = "media_type = ?";
            $params[] = $data['media_type'];
        }

        if (isset($data['media_url'])) {
            $updateFields[] = "media_url = ?";
            $params[] = $data['media_url'];
        }

        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE posts SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);

        return $this->getById($id);
    }

    public function delete($id) {
        // Eliminar likes
        $sql = "DELETE FROM likes WHERE post_id = ?";
        $this->db->query($sql, [$id]);
        
        // Eliminar comments
        $sql = "DELETE FROM comments WHERE post_id = ?";
        $this->db->query($sql, [$id]);
        
        // Eliminar post
        $sql = "DELETE FROM posts WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return ['success' => 'Publicación eliminada correctamente'];
    }
}
