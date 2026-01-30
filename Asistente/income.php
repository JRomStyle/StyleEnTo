<?php
require_once 'config/db.php';
require_once 'controllers/CompanyController.php';
require_once 'controllers/IncomeController.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$companyController = new CompanyController($conn);
$incomeController = new IncomeController($conn);
$message = '';

// Get user companies
$companies = $companyController->getCompaniesByUserId($user_id);

// Handle income addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = $_POST['company_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $is_recurrent = isset($_POST['is_recurrent']) ? 1 : 0;
    $date = $_POST['date'];
    
    $result = $incomeController->addIncome($company_id, $amount, $payment_method, $is_recurrent, $date);
    $message = $result['message'];
}

// Get selected company incomes
$selected_company_id = isset($_GET['company_id']) ? $_GET['company_id'] : ($companies[0]['id'] ?? null);
$incomes = $selected_company_id ? $incomeController->getIncomesByCompanyId($selected_company_id) : [];

// Render the view
ob_start();
?>
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Gestión de Ingresos</h2>
    
    <?php if ($message): ?>
        <div class="mb-4 p-2 rounded bg-green-50 text-success text-sm">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Add Income Form -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Registrar Nuevo Ingreso</h3>
        <form method="POST" action="income.php">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                    <select id="company_id" name="company_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <?php foreach ($companies as $company): ?>
                            <option value="<?php echo $company['id']; ?>" <?php echo $selected_company_id == $company['id'] ? 'selected' : ''; ?>>
                                <?php echo $company['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Monto</label>
                    <input type="number" id="amount" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                    <input type="text" id="payment_method" name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" id="date" name="date" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" id="is_recurrent" name="is_recurrent" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_recurrent" class="ml-2 block text-sm text-gray-700">Recurrente</label>
                </div>
            </div>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 transition">
                Registrar Ingreso
            </button>
        </form>
    </div>
    
    <!-- Incomes List -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Ingresos Registrados</h3>
        
        <!-- Company Filter -->
        <div class="mb-4">
            <label for="filter_company" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Empresa</label>
            <select id="filter_company" onchange="window.location.href='income.php?company_id='+this.value" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo $company['id']; ?>" <?php echo $selected_company_id == $company['id'] ? 'selected' : ''; ?>>
                        <?php echo $company['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if (empty($incomes)): ?>
            <p class="text-gray-600">No hay ingresos registrados para esta empresa</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Método</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Recurrente</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($incomes as $income): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800"><?php echo $income['date']; ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-success">$<?php echo number_format($income['amount'], 2); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $income['payment_method']; ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $income['is_recurrent'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $income['is_recurrent'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'views/template.php';
?>