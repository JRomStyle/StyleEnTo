<?php

require_once __DIR__ . '/../models/Beat.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class BeatController {
    private $beatModel;
    private $authMiddleware;

    public function __construct() {
        $this->beatModel = new Beat();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function create() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            
            $beatId = $this->beatModel->create($data);
            $beat = $this->beatModel->getById($beatId);
            
            http_response_code(201);
            echo json_encode($beat);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getAll() {
        // Filtrar parámetros
        $filters = [];
        if (isset($_GET['genre_id'])) $filters['genre_id'] = $_GET['genre_id'];
        if (isset($_GET['is_exclusive'])) $filters['is_exclusive'] = $_GET['is_exclusive'] === 'true';
        if (isset($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
        if (isset($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
        
        $beats = $this->beatModel->getAll($filters);
        http_response_code(200);
        echo json_encode($beats);
    }

    public function getById($id) {
        $beat = $this->beatModel->getById($id);
        
        if ($beat) {
            http_response_code(200);
            echo json_encode($beat);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Beat no encontrado']);
        }
    }

    public function getByUserId() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $beats = $this->beatModel->getByUserId($userId, $status);
        
        http_response_code(200);
        echo json_encode($beats);
    }

    public function update($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que el beat pertenece al usuario
        $beat = $this->beatModel->getById($id);
        if (!$beat || $beat['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para modificar este beat']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $updatedBeat = $this->beatModel->update($id, $data);
            
            if (isset($updatedBeat['error'])) {
                http_response_code(400);
                echo json_encode($updatedBeat);
            } else {
                http_response_code(200);
                echo json_encode($updatedBeat);
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
        
        // Verificar que el beat pertenece al usuario
        $beat = $this->beatModel->getById($id);
        if (!$beat || $beat['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar este beat']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->beatModel->delete($id);
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function purchase($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $buyerId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $licenseId = $data['license_id'] ?? null;
            
            if (!$licenseId) {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere un ID de licencia']);
                return;
            }
            
            $result = $this->beatModel->purchase($id, $buyerId, $licenseId);
            
            if (isset($result['error'])) {
                http_response_code(400);
                echo json_encode($result);
            } else {
                http_response_code(200);
                echo json_encode($result);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
