<?php
require_once 'config/db.php';
require_once 'controllers/CompanyController.php';

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
$message = '';

// Handle company creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_company'])) {
    $name = $_POST['name'];
    $business_type = $_POST['business_type'];
    
    $result = $companyController->createCompany($user_id, $name, $business_type);
    $message = $result['message'];
}

// Handle status update
if (isset($_GET['action']) && $_GET['action'] == 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $company_id = $_GET['id'];
    $status = $_GET['status'];
    $companyController->updateCompanyStatus($company_id, $status);
    header('Location: companies.php');
    exit;
}

// Get user companies
$companies = $companyController->getCompaniesByUserId($user_id);

// Render the view
ob_start();
?>
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Mis Empresas</h2>
    
    <?php if ($message): ?>
        <div class="mb-4 p-2 rounded bg-green-50 text-success text-sm">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Create Company Form -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Crear Nueva Empresa</h3>
        <form method="POST" action="companies.php">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Negocio</label>
                    <input type="text" id="business_type" name="business_type" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
            <button type="submit" name="create_company" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 transition">
                Crear Empresa
            </button>
        </form>
    </div>
    
    <!-- Companies List -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Tus Empresas</h3>
        
        <?php if (empty($companies)): ?>
            <p class="text-gray-600">No tienes empresas creadas</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800"><?php echo $company['name']; ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo $company['business_type']; ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                        switch ($company['status']) {
                                            case 'active': echo 'bg-green-100 text-green-800'; break;
                                            case 'paused': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'at_risk': echo 'bg-red-100 text-red-800'; break;
                                        }
                                    ?>">
                                        <?php echo ucfirst($company['status']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if ($company['status'] != 'active'): ?>
                                            <a href="companies.php?action=update_status&id=<?php echo $company['id']; ?>&status=active" class="text-green-600 hover:text-green-900">Activar</a>
                                        <?php endif; ?>
                                        <?php if ($company['status'] != 'paused'): ?>
                                            <a href="companies.php?action=update_status&id=<?php echo $company['id']; ?>&status=paused" class="text-yellow-600 hover:text-yellow-900">Pausar</a>
                                        <?php endif; ?>
                                        <?php if ($company['status'] != 'at_risk'): ?>
                                            <a href="companies.php?action=update_status&id=<?php echo $company['id']; ?>&status=at_risk" class="text-red-600 hover:text-red-900">Marcar en Riesgo</a>
                                        <?php endif; ?>
                                    </div>
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