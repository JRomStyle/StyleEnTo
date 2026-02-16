<?php
require_once 'config/bootstrap.php';

// Init Core Library
$init = new Core;

// Get Database instance/connection
$db = new Database;

$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$email = 'admin@jugueteria.com';

echo "Resetting password for $email to '$password'...\n";

$db->query('UPDATE users SET password = :password WHERE email = :email');
$db->bind(':password', $hashed_password);
$db->bind(':email', $email);

if($db->execute()){
    echo "Password updated successfully!\n";
} else {
    echo "Failed to update password.\n";
}
