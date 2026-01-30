<?php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class AdminController {
    private $adminModel;
    private $authMiddleware;

    public function __construct() {
        $this->adminModel = new Admin();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function getAllUsers() {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        // Filtrar parámetros
        $filters = [];
        if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
        if (isset($_GET['role'])) $filters['role'] = $_GET['role'];
        
        $users = $this->adminModel->getAllUsers($filters);
        http_response_code(200);
        echo json_encode($users);
    }

    public function getUserById($id) {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        $user = $this->adminModel->getUserById($id);
        
        if ($user) {
            http_response_code(200);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    }

    public function updateUserStatus($id) {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $status = $data['status'] ?? null;
            
            if (!$status) {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere un estado válido']);
                return;
            }
            
            $user = $this->adminModel->updateUserStatus($id, $status);
            http_response_code(200);
            echo json_encode($user);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function verifyArtist($id) {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $isVerified = $data['is_verified'] ?? false;
            
            $user = $this->adminModel->verifyArtist($id, $isVerified);
            http_response_code(200);
            echo json_encode($user);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getPendingSongs() {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        $songs = $this->adminModel->getPendingSongs();
        http_response_code(200);
        echo json_encode($songs);
    }

    public function updateSongStatus($id) {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $status = $data['status'] ?? null;
            
            if (!$status) {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere un estado válido']);
                return;
            }
            
            $result = $this->adminModel->updateSongStatus($id, $status);
            http_response_code(200);
            echo json_encode(['success' => 'Estado de canción actualizado correctamente']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getGlobalStats() {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        $stats = $this->adminModel->getGlobalStats();
        http_response_code(200);
        echo json_encode($stats);
    }

    public function getFinancialReport() {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        
        $report = $this->adminModel->getFinancialReport($startDate, $endDate);
        http_response_code(200);
        echo json_encode($report);
    }

    public function getAuditLogs() {
        // Verificar autenticación y rol de administrador
        $this->authMiddleware->authenticate('admin');
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $logs = $this->adminModel->getAuditLogs($limit);
        http_response_code(200);
        echo json_encode($logs);
    }
}
