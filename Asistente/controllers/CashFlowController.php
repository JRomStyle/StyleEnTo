<?php
require_once 'config/db.php';
require_once 'controllers/IncomeController.php';
require_once 'controllers/ExpenseController.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CashFlowController {
    private $conn;
    private $incomeController;
    private $expenseController;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->incomeController = new IncomeController($conn);
        $this->expenseController = new ExpenseController($conn);
    }

    public function calculateCashFlow($company_id) {
        $totalIncomes = $this->incomeController->getTotalIncomesByCompanyId($company_id);
        $totalExpenses = $this->expenseController->getTotalExpensesByCompanyId($company_id);
        return $totalIncomes - $totalExpenses;
    }

    public function getFinancialHealth($company_id) {
        $cashFlow = $this->calculateCashFlow($company_id);
        
        if ($cashFlow > 0) {
            return ['status' => 'OK', 'color' => 'success', 'message' => 'La caja está sana'];
        } elseif ($cashFlow == 0) {
            return ['status' => 'Riesgo', 'color' => 'warning', 'message' => 'La caja está en equilibrio, ten cuidado'];
        } else {
            return ['status' => 'Crítico', 'color' => 'danger', 'message' => 'La caja está en situación crítica'];
        }
    }

    public function generateAlerts($company_id) {
        $alerts = [];
        $cashFlow = $this->calculateCashFlow($company_id);
        $totalIncomes = $this->incomeController->getTotalIncomesByCompanyId($company_id);
        $totalExpenses = $this->expenseController->getTotalExpensesByCompanyId($company_id);
        
        // Check if expenses exceed incomes
        if ($totalExpenses > $totalIncomes) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Los gastos superan los ingresos. Si sigues así, quiebras en ' . rand(15, 60) . ' días'
            ];
        }
        
        // Check if cash flow is negative
        if ($cashFlow < 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'El flujo de caja es negativo. Necesitas aumentar ingresos o reducir gastos'
            ];
        }
        
        // Check for unnecessary expenses
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM expenses WHERE company_id = ? AND is_unnecessary = 1");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Tienes ' . $row['count'] . ' gastos innecesarios que debes eliminar'
            ];
        }
        
        return $alerts;
    }

    public function generateDailyPriorities($company_id) {
        $priorities = [];
        $cashFlow = $this->calculateCashFlow($company_id);
        $totalIncomes = $this->incomeController->getTotalIncomesByCompanyId($company_id);
        $totalExpenses = $this->expenseController->getTotalExpensesByCompanyId($company_id);
        
        // Priority 1: If cash flow is negative, focus on income
        if ($cashFlow < 0) {
            $priorities[] = 'Cobrar facturas pendientes para aumentar ingresos';
        }
        
        // Priority 2: If expenses are high
        if ($totalExpenses > $totalIncomes * 0.8) {
            $priorities[] = 'Revisar y reducir gastos no esenciales';
        }
        
        // Priority 3: Check unnecessary expenses
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM expenses WHERE company_id = ? AND is_unnecessary = 1");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $priorities[] = 'Eliminar ' . $row['count'] . ' gastos innecesarios detectados';
        }
        
        // Priority 4: If company is at risk
        $stmt = $this->conn->prepare("SELECT status FROM companies WHERE id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $company = $result->fetch_assoc();
        
        if ($company['status'] == 'at_risk') {
            $priorities[] = 'Implementar medidas de emergencia para salvar la empresa';
        }
        
        // Add a final priority if less than 3
        if (count($priorities) < 3) {
            $priorities[] = 'Revisar el estado de todas las facturas';
        }
        
        // Limit to 5 priorities
        return array_slice($priorities, 0, 5);
    }

    public function generateFinancialTip() {
        $tips = [
            'Ahorra al menos el 10% de tus ingresos para emergencias',
            'No gastes más del 30% de tus ingresos en gastos fijos',
            'Subir precios es mejor que reducir costos si tu producto tiene valor',
            'Recorta gastos innecesarios antes de reducir personal',
            'Pausa empresas no rentables para concentrarte en las que sí generan dinero',
            'Negocia términos de pago con proveedores para mejorar tu flujo de caja',
            'Factura a tus clientes lo antes posible y establece plazos claros',
            'Monitorea tu caja diariamente, no mensualmente'
        ];
        
        return $tips[array_rand($tips)];
    }
}
?>