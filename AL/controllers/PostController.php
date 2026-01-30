<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Like.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class PostController {
    private $postModel;
    private $likeModel;
    private $commentModel;
    private $authMiddleware;

    public function __construct() {
        $this->postModel = new Post();
        $this->likeModel = new Like();
        $this->commentModel = new Comment();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function create() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            
            $postId = $this->postModel->create($data);
            $post = $this->postModel->getById($postId);
            
            http_response_code(201);
            echo json_encode($post);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getFeed() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $feed = $this->postModel->getFeed($userId, $limit, $offset);
        http_response_code(200);
        echo json_encode($feed);
    }

    public function getById($id) {
        $post = $this->postModel->getById($id);
        
        if ($post) {
            http_response_code(200);
            echo json_encode($post);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Publicación no encontrada']);
        }
    }

    public function getByUserId($userId) {
        $status = isset($_GET['status']) ? $_GET['status'] : 'published';
        $posts = $this->postModel->getByUserId($userId, $status);
        
        http_response_code(200);
        echo json_encode($posts);
    }

    public function update($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que la publicación pertenece al usuario
        $post = $this->postModel->getById($id);
        if (!$post || $post['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para modificar esta publicación']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $updatedPost = $this->postModel->update($id, $data);
            
            if (isset($updatedPost['error'])) {
                http_response_code(400);
                echo json_encode($updatedPost);
            } else {
                http_response_code(200);
                echo json_encode($updatedPost);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function delete($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que la publicación pertenece al usuario
        $post = $this->postModel->getById($id);
        if (!$post || $post['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar esta publicación']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->postModel->delete($id);
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function like($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->likeModel->like($userId, $id);
            
            if (isset($result['error'])) {
                http_response_code(400);
            } else {
                http_response_code(200);
            }
            
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function unlike($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->likeModel->unlike($userId, $id);
            
            if (isset($result['error'])) {
                http_response_code(400);
            } else {
                http_response_code(200);
            }
            
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getComments($id) {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        
        $comments = $this->commentModel->getByPostId($id, $limit, $offset);
        http_response_code(200);
        echo json_encode($comments);
    }

    public function addComment($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            $data['post_id'] = $id;
            
            $comment = $this->commentModel->create($data);
            http_response_code(201);
            echo json_encode($comment);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function deleteComment($postId, $commentId) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que el comentario pertenece al usuario
        $comment = $this->commentModel->getById($commentId);
        if (!$comment || $comment['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar este comentario']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->commentModel->delete($commentId);
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
