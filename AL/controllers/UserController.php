<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class UserController {
    private $userModel;
    private $authMiddleware;

    public function __construct() {
        $this->userModel = new User();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->userModel->register($data);
            
            if (isset($result['error'])) {
                http_response_code(400);
            } else {
                http_response_code(201);
            }
            
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->userModel->login($data['email'], $data['password']);
            
            if (isset($result['error'])) {
                http_response_code(401);
            } else {
                http_response_code(200);
            }
            
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getProfile($id) {
        $result = $this->userModel->getById($id);
        
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    }

    public function updateProfile() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->userModel->updateProfile($userId, $data);
            
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

    public function updatePassword() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->userModel->updatePassword($userId, $data['current_password'], $data['new_password']);
            
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

    public function addGenre() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->userModel->addGenre($userId, $data['genre_id']);
            
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

    public function removeGenre() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->userModel->removeGenre($userId, $data['genre_id']);
            
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

    public function getArtistsByGenre($genreName) {
        $result = $this->userModel->getArtistsByGenre($genreName);
        http_response_code(200);
        echo json_encode($result);
    }
}
