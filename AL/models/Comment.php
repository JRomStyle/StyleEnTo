<?php

require_once __DIR__ . '/../core/DB.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO comments (user_id, post_id, parent_id, content) VALUES (?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['post_id'],
            $data['parent_id'] ?? null,
            $data['content']
        ]);
        
        $commentId = $this->db->lastInsertId();
        
        // Actualizar contador de comentarios en la publicación
        $sql = "UPDATE posts SET comments_count = comments_count + 1 WHERE id = ?";
        $this->db->query($sql, [$data['post_id']]);
        
        return $this->getById($commentId);
    }

    public function getById($id) {
        $sql = "SELECT c.*, u.username, u.full_name, u.profile_image FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getByPostId($postId, $limit = 20, $offset = 0) {
        $sql = "SELECT c.*, u.username, u.full_name, u.profile_image FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.post_id = ? AND c.parent_id IS NULL 
                ORDER BY c.created_at DESC 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$postId, $limit, $offset]);
        $comments = $stmt->fetchAll();
        
        // Obtener respuestas para cada comentario
        foreach ($comments as &$comment) {
            $sql = "SELECT c.*, u.username, u.full_name, u.profile_image FROM comments c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.parent_id = ? 
                    ORDER BY c.created_at ASC";
            $stmt = $this->db->query($sql, [$comment['id']]);
            $comment['replies'] = $stmt->fetchAll();
        }
        
        return $comments;
    }

    public function update($id, $content) {
        $sql = "UPDATE comments SET content = ? WHERE id = ?";
        $this->db->query($sql, [$content, $id]);
        return $this->getById($id);
    }

    public function delete($id) {
        // Obtener post_id para actualizar el contador
        $sql = "SELECT post_id, parent_id FROM comments WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $comment = $stmt->fetch();
        
        if ($comment) {
            // Eliminar comentario y respuestas
            $sql = "DELETE FROM comments WHERE id = ? OR parent_id = ?";
            $this->db->query($sql, [$id, $id]);
            
            // Actualizar contador de comentarios en la publicación
            $sql = "UPDATE posts SET comments_count = comments_count - 1 WHERE id = ? AND comments_count > 0";
            $this->db->query($sql, [$comment['post_id']]);
        }
        
        return ['success' => 'Comentario eliminado correctamente'];
    }
}
