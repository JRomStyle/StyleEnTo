<?php

require_once __DIR__ . '/../core/DB.php';

class Royalty {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO royalties (user_id, song_id, platform_id, amount, currency, period_start, period_end, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['song_id'] ?? null,
            $data['platform_id'],
            $data['amount'],
            $data['currency'] ?? 'USD',
            $data['period_start'],
            $data['period_end'],
            $data['status'] ?? 'pending'
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT r.*, s.title as song_title, p.name as platform_name FROM royalties r 
                LEFT JOIN songs s ON r.song_id = s.id 
                JOIN platforms p ON r.platform_id = p.id 
                WHERE r.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getByUserId($userId, $filters = []) {
        $sql = "SELECT r.*, s.title as song_title, p.name as platform_name FROM royalties r 
                LEFT JOIN songs s ON r.song_id = s.id 
                JOIN platforms p ON r.platform_id = p.id 
                WHERE r.user_id = ?";
        $params = [$userId];

        // Aplicar filtros
        if (isset($filters['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['platform_id'])) {
            $sql .= " AND r.platform_id = ?";
            $params[] = $filters['platform_id'];
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $sql .= " AND r.period_start >= ? AND r.period_end <= ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY r.period_end DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE royalties SET status = ?, payment_date = CASE WHEN ? = 'paid' THEN CURRENT_TIMESTAMP ELSE NULL END WHERE id = ?";
        $this->db->query($sql, [$status, $status, $id]);
        return $this->getById($id);
    }

    public function getSummaryByUser($userId, $period = 'month') {
        // Calcular fecha de inicio según el período
        $startDate = null;
        switch ($period) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('-1 week'));
                break;
            case 'month':
                $startDate = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'year':
                $startDate = date('Y-m-d', strtotime('-1 year'));
                break;
        }

        $sql = "SELECT 
                    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_earned,
                    COUNT(*) as total_transactions,
                    SUM(amount) as total_pending
                FROM royalties 
                WHERE user_id = ? AND period_start >= ?";
        $stmt = $this->db->query($sql, [$userId, $startDate]);
        return $stmt->fetch();
    }

    public function getEarningsByPlatform($userId, $startDate, $endDate) {
        $sql = "SELECT p.name as platform, SUM(r.amount) as earnings 
                FROM royalties r 
                JOIN platforms p ON r.platform_id = p.id 
                WHERE r.user_id = ? AND r.period_start >= ? AND r.period_end <= ? AND r.status = 'paid' 
                GROUP BY p.id, p.name 
                ORDER BY earnings DESC";
        $stmt = $this->db->query($sql, [$userId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
}
