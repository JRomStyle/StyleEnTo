<?php
require_once 'config/db.php';
require_once 'controllers/UserController.php';
require_once 'controllers/CompanyController.php';
require_once 'controllers/CashFlowController.php';

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
$cashFlowController = new CashFlowController($conn);

// Get user companies
$companies = $companyController->getCompaniesByUserId($user_id);

// Prepare company data with financial info
$companyData = [];
foreach ($companies as $company) {
    $cashFlow = $cashFlowController->calculateCashFlow($company['id']);
    $financialHealth = $cashFlowController->getFinancialHealth($company['id']);
    $alerts = $cashFlowController->generateAlerts($company['id']);
    $priorities = $cashFlowController->generateDailyPriorities($company['id']);
    
    $companyData[] = [
        'company' => $company,
        'cashFlow' => $cashFlow,
        'financialHealth' => $financialHealth,
        'alerts' => $alerts,
        'priorities' => $priorities
    ];
}

// Get main company (first one or with most recent activity)
$mainCompany = $companyData[0] ?? null;
$financialTip = $cashFlowController->generateFinancialTip();

// Render the view
ob_start();
?>
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Hola, <?php echo $_SESSION['user_name']; ?></h2>
    
    <?php if ($mainCompany): ?>
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">Empresa: <?php echo $mainCompany['company']['name']; ?></h3>
            
            <!-- Daily Priorities -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <h4 class="text-lg font-semibold mb-2 flex items-center">
                    <span class="mr-2">📌</span>
                    HOY DEBES HACER:
                </h4>
                <ul class="list-disc pl-6 space-y-1">
                    <?php foreach ($mainCompany['priorities'] as $priority): ?>
                        <li class="text-gray-700"><?php echo $priority; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Alerts -->
            <?php if (!empty($mainCompany['alerts'])): ?>
                <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                    <h4 class="text-lg font-semibold mb-2 flex items-center">
                        <span class="mr-2">🚨</span>
                        ALERTAS:
                    </h4>
                    <div class="space-y-2">
                        <?php foreach ($mainCompany['alerts'] as $alert): ?>
                            <div class="p-2 rounded border-l-4 <?php echo $alert['type'] == 'danger' ? 'border-danger bg-red-50' : 'border-warning bg-yellow-50'; ?>">
                                <p class="text-gray-700"><?php echo $alert['message']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Financial Tip -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <h4 class="text-lg font-semibold mb-2 flex items-center">
                    <span class="mr-2">💡</span>
                    CONSEJO CLAVE:
                </h4>
                <p class="text-gray-700"><?php echo $financialTip; ?></p>
            </div>
            
            <!-- Quick Status -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h4 class="text-lg font-semibold mb-2 flex items-center">
                    <span class="mr-2">📊</span>
                    ESTADO RÁPIDO:
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Caja:</span>
                        <span class="ml-2 font-medium text-<?php echo $mainCompany['financialHealth']['color']; ?>">
                            <?php echo $mainCompany['financialHealth']['status']; ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Ingresos vs Gastos:</span>
                        <span class="ml-2 font-medium <?php echo $mainCompany['cashFlow'] > 0 ? 'text-success' : ($mainCompany['cashFlow'] == 0 ? 'text-warning' : 'text-danger'); ?>">
                            <?php echo $mainCompany['cashFlow'] > 0 ? '+' : ($mainCompany['cashFlow'] == 0 ? '0' : '-'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <h3 class="text-xl font-semibold mb-2">No tienes empresas creadas</h3>
            <p class="mb-4 text-gray-600">Crea tu primera empresa para empezar a gestionar tus finanzas</p>
            <a href="companies.php" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 transition">
                Crear Empresa
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Companies List -->
    <?php if (count($companies) > 1): ?>
        <div class="mt-8">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">Tus Empresas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($companyData as $data): ?>
                    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-<?php echo $data['financialHealth']['color']; ?>">
                        <h4 class="font-semibold text-gray-800"><?php echo $data['company']['name']; ?></h4>
                        <p class="text-sm text-gray-500 mb-2">Tipo: <?php echo $data['company']['business_type']; ?></p>
                        <p class="text-sm text-gray-500 mb-2">Estado: <?php echo ucfirst($data['company']['status']); ?></p>
                        <p class="text-sm font-medium <?php echo $data['cashFlow'] > 0 ? 'text-success' : 'text-danger'; ?>">
                            Flujo de caja: $<?php echo number_format($data['cashFlow'], 2); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include 'views/template.php';
?>