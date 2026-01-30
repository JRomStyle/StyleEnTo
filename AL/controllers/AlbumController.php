<?php

require_once __DIR__ . '/../models/Album.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class AlbumController {
    private $albumModel;
    private $authMiddleware;

    public function __construct() {
        $this->albumModel = new Album();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function create() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            
            $albumId = $this->albumModel->create($data);
            $album = $this->albumModel->getById($albumId);
            
            http_response_code(201);
            echo json_encode($album);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getById($id) {
        $album = $this->albumModel->getById($id);
        
        if ($album) {
            http_response_code(200);
            echo json_encode($album);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Álbum no encontrado']);
        }
    }

    public function getByUserId() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $albums = $this->albumModel->getByUserId($userId, $status);
        
        http_response_code(200);
        echo json_encode($albums);
    }

    public function update($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que el álbum pertenece al usuario
        $album = $this->albumModel->getById($id);
        if (!$album || $album['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para modificar este álbum']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $updatedAlbum = $this->albumModel->update($id, $data);
            
            if (isset($updatedAlbum['error'])) {
                http_response_code(400);
                echo json_encode($updatedAlbum);
            } else {
                http_response_code(200);
                echo json_encode($updatedAlbum);
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
        
        // Verificar que el álbum pertenece al usuario
        $album = $this->albumModel->getById($id);
        if (!$album || $album['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar este álbum']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->albumModel->delete($id);
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
