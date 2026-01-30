<?php

require_once __DIR__ . '/../models/Song.php';
require_once __DIR__ . '/../models/Album.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class SongController {
    private $songModel;
    private $albumModel;
    private $authMiddleware;

    public function __construct() {
        $this->songModel = new Song();
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
            
            $songId = $this->songModel->create($data);
            $song = $this->songModel->getById($songId);
            
            http_response_code(201);
            echo json_encode($song);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getById($id) {
        $song = $this->songModel->getById($id);
        
        if ($song) {
            http_response_code(200);
            echo json_encode($song);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Canción no encontrada']);
        }
    }

    public function getByUserId() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $songs = $this->songModel->getByUserId($userId, $status);
        
        http_response_code(200);
        echo json_encode($songs);
    }

    public function update($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que la canción pertenece al usuario
        $song = $this->songModel->getById($id);
        if (!$song || $song['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para modificar esta canción']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $updatedSong = $this->songModel->update($id, $data);
            
            if (isset($updatedSong['error'])) {
                http_response_code(400);
                echo json_encode($updatedSong);
            } else {
                http_response_code(200);
                echo json_encode($updatedSong);
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
        
        // Verificar que la canción pertenece al usuario
        $song = $this->songModel->getById($id);
        if (!$song || $song['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar esta canción']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->songModel->delete($id);
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function distribute($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que la canción pertenece al usuario
        $song = $this->songModel->getById($id);
        if (!$song || $song['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para distribuir esta canción']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $platformIds = $data['platform_ids'] ?? [];
            
            if (empty($platformIds)) {
                http_response_code(400);
                echo json_encode(['error' => 'Debes seleccionar al menos una plataforma']);
                return;
            }
            
            $results = $this->songModel->distribute($id, $platformIds);
            http_response_code(200);
            echo json_encode(['results' => $results, 'message' => 'Distribución solicitada']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getDistributionStatus($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que la canción pertenece al usuario
        $song = $this->songModel->getById($id);
        if (!$song || $song['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para ver esta información']);
            return;
        }
        
        $status = $this->songModel->getDistributionStatus($id);
        http_response_code(200);
        echo json_encode($status);
    }
}
