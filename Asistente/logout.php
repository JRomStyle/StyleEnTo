<?php
require_once 'config/db.php';
require_once 'controllers/UserController.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController($conn);
$userController->logout();

// Redirect to login page
header('Location: login.php');
exit;
?>