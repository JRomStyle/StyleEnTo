<?php

require_once __DIR__ . '/../models/NFT.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class NFTController {
    private $nftModel;
    private $authMiddleware;

    public function __construct() {
        $this->nftModel = new NFT();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function create() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            
            $nftId = $this->nftModel->create($data);
            $nft = $this->nftModel->getById($nftId);
            
            http_response_code(201);
            echo json_encode($nft);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getAll() {
        // Filtrar parámetros
        $filters = [];
        if (isset($_GET['is_for_sale'])) $filters['is_for_sale'] = $_GET['is_for_sale'] === 'true';
        if (isset($_GET['blockchain'])) $filters['blockchain'] = $_GET['blockchain'];
        if (isset($_GET['min_price'])) $filters['min_price'] = $_GET['min_price'];
        if (isset($_GET['max_price'])) $filters['max_price'] = $_GET['max_price'];
        if (isset($_GET['user_id'])) $filters['user_id'] = $_GET['user_id'];
        
        $nfts = $this->nftModel->getAll($filters);
        http_response_code(200);
        echo json_encode($nfts);
    }

    public function getById($id) {
        $nft = $this->nftModel->getById($id);
        
        if ($nft) {
            http_response_code(200);
            echo json_encode($nft);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'NFT no encontrado']);
        }
    }

    public function update($id) {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        // Verificar que el NFT pertenece al usuario
        $nft = $this->nftModel->getById($id);
        if (!$nft || $nft['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para modificar este NFT']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $updatedNft = $this->nftModel->update($id, $data);
            
            if (isset($updatedNft['error'])) {
                http_response_code(400);
                echo json_encode($updatedNft);
            } else {
                http_response_code(200);
                echo json_encode($updatedNft);
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
        
        // Verificar que el NFT pertenece al usuario
        $nft = $this->nftModel->getById($id);
        if (!$nft || $nft['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para eliminar este NFT']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->nftModel->delete($id);
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
            $transactionHash = $data['transaction_hash'] ?? null;
            
            if (!$transactionHash) {
                http_response_code(400);
                echo json_encode(['error' => 'Se requiere el hash de transacción']);
                return;
            }
            
            $result = $this->nftModel->purchase($id, $buyerId, $transactionHash);
            
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

    // Métodos para colecciones
    public function createCollection() {
        // Verificar autenticación
        $this->authMiddleware->authenticate();
        $userId = $_SESSION['user']['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $userId;
            
            $collectionId = $this->nftModel->createCollection($data);
            $collection = $this->nftModel->getCollectionById($collectionId);
            
            http_response_code(201);
            echo json_encode($collection);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function getCollectionById($id) {
        $collection = $this->nftModel->getCollectionById($id);
        
        if ($collection) {
            http_response_code(200);
            echo json_encode($collection);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Colección no encontrada']);
        }
    }

    public function getNFTsByCollection($id) {
        $nfts = $this->nftModel->getNFTsByCollection($id);
        http_response_code(200);
        echo json_encode($nfts);
    }
}
