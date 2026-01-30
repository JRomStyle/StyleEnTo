<?php
require_once 'config/db.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ExpenseController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addExpense($company_id, $type, $category, $amount, $is_unnecessary, $date) {
        // Validate input
        if (empty($company_id) || empty($type) || empty($category) || empty($amount) || empty($date)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Insert expense
        $stmt = $this->conn->prepare("INSERT INTO expenses (company_id, type, category, amount, is_unnecessary, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdis", $company_id, $type, $category, $amount, $is_unnecessary, $date);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Gasto registrado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar el gasto'];
        }
    }

    public function getExpensesByCompanyId($company_id) {
        $stmt = $this->conn->prepare("SELECT * FROM expenses WHERE company_id = ? ORDER BY date DESC");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalExpensesByCompanyId($company_id) {
        $stmt = $this->conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE company_id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }
}
?>