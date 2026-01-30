<?php

require_once __DIR__ . '/../models/Royalty.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class RoyaltyController {
    private $royaltyModel;
    private $authMiddleware;

    public function __construct() {
        $this->royaltyModel = new Royalty();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function getByUserId() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Filtrar parámetros
        $filters = [];
        if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
        if (isset($_GET['platform_id'])) $filters['platform_id'] = $_GET['platform_id'];
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $filters['start_date'] = $_GET['start_date'];
            $filters['end_date'] = $_GET['end_date'];
        }
        
        $royalties = $this->royaltyModel->getByUserId($userId, $filters);
        http_response_code(200);
        echo json_encode($royalties);
    }

    public function getById($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $royalty = $this->royaltyModel->getById($id);
        
        if ($royalty) {
            // Verificar que la regalía pertenece al usuario
            if ($royalty['user_id'] != $userId) {
                http_response_code(403);
                echo json_encode(['error' => 'No tienes permisos para ver esta información']);
                return;
            }
            
            http_response_code(200);
            echo json_encode($royalty);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Regalía no encontrada']);
        }
    }

    public function getSummary() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        $summary = $this->royaltyModel->getSummaryByUser($userId, $period);
        
        http_response_code(200);
        echo json_encode($summary);
    }

    public function getEarningsByPlatform() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-1 month'));
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        
        $earnings = $this->royaltyModel->getEarningsByPlatform($userId, $startDate, $endDate);
        
        http_response_code(200);
        echo json_encode($earnings);
    }
}
