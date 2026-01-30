<?php

require_once __DIR__ . '/../core/DB.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    // Gestión de usuarios
    public function getAllUsers($filters = []) {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles FROM users u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                GROUP BY u.id";
        $params = [];

        // Aplicar filtros
        if (isset($filters['status'])) {
            $sql .= " HAVING u.status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['role'])) {
            $sql .= " HAVING GROUP_CONCAT(r.name) LIKE ?";
            $params[] = "%" . $filters['role'] . "%";
        }

        $sql .= " ORDER BY u.created_at DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function getUserById($id) {
        $sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles FROM users u 
                LEFT JOIN user_roles ur ON u.id = ur.user_id 
                LEFT JOIN roles r ON ur.role_id = r.id 
                WHERE u.id = ? 
                GROUP BY u.id";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function updateUserStatus($id, $status) {
        $sql = "UPDATE users SET status = ? WHERE id = ?";
        $this->db->query($sql, [$status, $id]);
        return $this->getUserById($id);
    }

    public function verifyArtist($id, $isVerified) {
        $sql = "UPDATE users SET is_verified = ? WHERE id = ?";
        $this->db->query($sql, [$isVerified, $id]);
        return $this->getUserById($id);
    }

    // Moderación de contenido
    public function getPendingSongs() {
        $sql = "SELECT s.*, u.username, u.full_name FROM songs s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.status = 'pending' 
                ORDER BY s.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function updateSongStatus($id, $status) {
        $sql = "UPDATE songs SET status = ? WHERE id = ?";
        $this->db->query($sql, [$status, $id]);
        return true;
    }

    // Estadísticas globales
    public function getGlobalStats() {
        $stats = [];

        // Total de usuarios
        $sql = "SELECT COUNT(*) as total_users FROM users";
        $stmt = $this->db->query($sql);
        $stats['total_users'] = $stmt->fetch()['total_users'];

        // Total de artistas verificados
        $sql = "SELECT COUNT(*) as verified_artists FROM users WHERE is_verified = true";
        $stmt = $this->db->query($sql);
        $stats['verified_artists'] = $stmt->fetch()['verified_artists'];

        // Total de canciones
        $sql = "SELECT COUNT(*) as total_songs FROM songs";
        $stmt = $this->db->query($sql);
        $stats['total_songs'] = $stmt->fetch()['total_songs'];

        // Total de ventas
        $sql = "SELECT COUNT(*) as total_sales FROM sales WHERE status = 'completed'";
        $stmt = $this->db->query($sql);
        $stats['total_sales'] = $stmt->fetch()['total_sales'];

        // Ingresos totales
        $sql = "SELECT SUM(amount) as total_revenue FROM sales WHERE status = 'completed'";
        $stmt = $this->db->query($sql);
        $stats['total_revenue'] = $stmt->fetch()['total_revenue'] ?? 0;

        // Usuarios registrados en los últimos 30 días
        $sql = "SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->db->query($sql);
        $stats['new_users'] = $stmt->fetch()['new_users'];

        return $stats;
    }

    // Reportes financieros
    public function getFinancialReport($startDate, $endDate) {
        $sql = "SELECT 
                    DATE(s.created_at) as date,
                    COUNT(*) as sales_count,
                    SUM(s.amount) as total_amount
                FROM sales s 
                WHERE s.created_at >= ? AND s.created_at <= ? AND s.status = 'completed' 
                GROUP BY DATE(s.created_at) 
                ORDER BY date";
        $stmt = $this->db->query($sql, [$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    // Logs de seguridad
    public function getAuditLogs($limit = 50) {
        $sql = "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
}
