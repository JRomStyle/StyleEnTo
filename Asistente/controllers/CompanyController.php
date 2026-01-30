<?php
require_once 'config/db.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CompanyController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createCompany($user_id, $name, $business_type) {
        // Validate input
        if (empty($name) || empty($business_type)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Insert company
        $stmt = $this->conn->prepare("INSERT INTO companies (user_id, name, business_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $name, $business_type);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Empresa creada exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al crear la empresa'];
        }
    }

    public function getCompaniesByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM companies WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateCompanyStatus($company_id, $status) {
        $stmt = $this->conn->prepare("UPDATE companies SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $company_id);
        return $stmt->execute();
    }
}
?>