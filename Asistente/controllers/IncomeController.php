<?php
require_once 'config/db.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class IncomeController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addIncome($company_id, $amount, $payment_method, $is_recurrent, $date) {
        // Validate input
        if (empty($company_id) || empty($amount) || empty($payment_method) || empty($date)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Insert income
        $stmt = $this->conn->prepare("INSERT INTO incomes (company_id, amount, payment_method, is_recurrent, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $company_id, $amount, $payment_method, $is_recurrent, $date);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Ingreso registrado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar el ingreso'];
        }
    }

    public function getIncomesByCompanyId($company_id) {
        $stmt = $this->conn->prepare("SELECT * FROM incomes WHERE company_id = ? ORDER BY date DESC");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalIncomesByCompanyId($company_id) {
        $stmt = $this->conn->prepare("SELECT SUM(amount) as total FROM incomes WHERE company_id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }
}
?>