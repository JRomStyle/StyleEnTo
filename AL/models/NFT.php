<?php

require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../helpers/S3Storage.php';

class NFT {
    private $db;
    private $s3;

    public function __construct() {
        $this->db = DB::getInstance();
        $this->s3 = new S3Storage();
    }

    public function create($data) {
        // Subir imagen a S3 si es un archivo local
        $imageUrl = $data['image_url'];
        if (file_exists($imageUrl)) {
            $fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $destinationPath = 'nfts/' . uniqid() . '.' . $fileExtension;
            $s3Result = $this->s3->uploadFile($imageUrl, $destinationPath, 'image/jpeg');
            
            if ($s3Result['success']) {
                $imageUrl = $s3Result['public_url'];
                // Eliminar archivo local después de subir a S3
                unlink($data['image_url']);
            }
        }

        $sql = "INSERT INTO nfts (user_id, song_id, collection_id, title, description, image_url, blockchain, token_address, token_id, price, currency, is_for_sale) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['song_id'] ?? null,
            $data['collection_id'] ?? null,
            $data['title'],
            $data['description'] ?? null,
            $imageUrl,
            $data['blockchain'],
            $data['token_address'],
            $data['token_id'],
            $data['price'],
            $data['currency'] ?? 'ETH',
            $data['is_for_sale'] ?? true
        ]);
        
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT n.*, u.username as creator_username, u.profile_image as creator_image FROM nfts n JOIN users u ON n.user_id = u.id WHERE n.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getAll($filters = []) {
        $sql = "SELECT n.*, u.username as creator_username, u.profile_image as creator_image FROM nfts n JOIN users u ON n.user_id = u.id WHERE n.status = 'active'";
        $params = [];

        // Aplicar filtros
        if (isset($filters['is_for_sale'])) {
            $sql .= " AND n.is_for_sale = ?";
            $params[] = $filters['is_for_sale'];
        }

        if (isset($filters['blockchain'])) {
            $sql .= " AND n.blockchain = ?";
            $params[] = $filters['blockchain'];
        }

        if (isset($filters['min_price'])) {
            $sql .= " AND n.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $sql .= " AND n.price <= ?";
            $params[] = $filters['max_price'];
        }

        if (isset($filters['user_id'])) {
            $sql .= " AND n.user_id = ?";
            $params[] = $filters['user_id'];
        }

        $sql .= " ORDER BY n.created_at DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['title'])) {
            $updateFields[] = "title = ?";
            $params[] = $data['title'];
        }

        if (isset($data['description'])) {
            $updateFields[] = "description = ?";
            $params[] = $data['description'];
        }

        if (isset($data['image_url'])) {
            // Subir imagen a S3 si es un archivo local
            $imageUrl = $data['image_url'];
            if (file_exists($imageUrl)) {
                $fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
                $destinationPath = 'nfts/' . uniqid() . '.' . $fileExtension;
                $s3Result = $this->s3->uploadFile($imageUrl, $destinationPath, 'image/jpeg');
                
                if ($s3Result['success']) {
                    $imageUrl = $s3Result['public_url'];
                    // Eliminar archivo local después de subir a S3
                    unlink($data['image_url']);
                }
            }
            $updateFields[] = "image_url = ?";
            $params[] = $imageUrl;
        }

        if (isset($data['price'])) {
            $updateFields[] = "price = ?";
            $params[] = $data['price'];
        }

        if (isset($data['is_for_sale'])) {
            $updateFields[] = "is_for_sale = ?";
            $params[] = $data['is_for_sale'];
        }

        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE nfts SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);
        return $this->getById($id);
    }

    public function delete($id) {
        $sql = "DELETE FROM nfts WHERE id = ?";
        $this->db->query($sql, [$id]);
        return ['success' => 'NFT eliminado correctamente'];
    }

    public function purchase($id, $buyerId, $transactionHash) {
        // Obtener NFT
        $nft = $this->getById($id);
        if (!$nft || $nft['status'] !== 'active' || !$nft['is_for_sale']) {
            return ['error' => 'NFT no disponible para compra'];
        }

        // Crear registro de venta
        $sql = "INSERT INTO nft_sales (nft_id, seller_id, buyer_id, price, currency, transaction_hash) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $id,
            $nft['user_id'],
            $buyerId,
            $nft['price'],
            $nft['currency'],
            $transactionHash
        ]);

        // Actualizar estado del NFT
        $sql = "UPDATE nfts SET status = 'sold', is_for_sale = false WHERE id = ?";
        $this->db->query($sql, [$id]);

        return ['success' => 'NFT comprado correctamente', 'sale_id' => $this->db->lastInsertId()];
    }

    // Métodos para colecciones de NFTs
    public function createCollection($data) {
        $sql = "INSERT INTO nft_collections (user_id, name, description, image_url, blockchain, contract_address) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['name'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['blockchain'],
            $data['contract_address']
        ]);
        return $this->db->lastInsertId();
    }

    public function getCollectionById($id) {
        $sql = "SELECT * FROM nft_collections WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getNFTsByCollection($collectionId) {
        $sql = "SELECT n.*, u.username as creator_username, u.profile_image as creator_image FROM nfts n JOIN users u ON n.user_id = u.id WHERE n.collection_id = ? AND n.status = 'active' ORDER BY n.created_at DESC";
        $stmt = $this->db->query($sql, [$collectionId]);
        return $stmt->fetchAll();
    }
}
