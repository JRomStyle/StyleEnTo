<?php

require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class ServiceController {
    private $serviceModel;
    private $authMiddleware;

    public function __construct() {
        $this->serviceModel = new Service();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function create() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            
            $serviceId = $this->serviceModel->create($data);
            $service = $this->serviceModel->getById($serviceId);
            
            http_response_code(201);
            echo json_encode($service);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getAll() {
        // Filtrar parámetros
        $filters = [];
        if (isset($_GET['category'])) $filters['category'] = $_GET['category'];
        if (isset($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
        if (isset($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
        
        $services = $this->serviceModel->getAll($filters);
        http_response_code(200);
        echo json_encode($services);
    }

    public function getById($id) {
        $service = $this->serviceModel->getById($id);
        
        if ($service) {
            http_response_code(200);
            echo json_encode($service);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Servicio no encontrado']);
        }
    }

    public function getByUserId() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $services = $this->serviceModel->getByUserId($userId, $status);
        
        http_response_code(200);
        echo json_encode($services);
    }

    public function update($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que el servicio pertenece al usuario
        $service = $this->serviceModel->getById($id);
        if (!$service || $service['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para modificar este servicio']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $updatedService = $this->serviceModel->update($id, $data);
            
            if (isset($updatedService['error'])) {
                http_response_code(400);
                echo json_encode($updatedService);
            } else {
                http_response_code(200);
                echo json_encode($updatedService);
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
        
        // Verificar que el servicio pertenece al usuario
        $service = $this->serviceModel->getById($id);
        if (!$service || $service['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar este servicio']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->serviceModel->delete($id);
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
            $result = $this->serviceModel->purchase($id, $buyerId);
            
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
