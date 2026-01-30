<?php
require_once 'config/db.php';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class UserController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($name, $email, $password) {
        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registro exitoso'];
        } else {
            return ['success' => false, 'message' => 'Error al registrarse'];
        }
    }

    public function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }

        // Check user
        $stmt = $this->conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $name, $hashed_password);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Set session
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                return ['success' => true, 'message' => 'Inicio de sesión exitoso'];
            } else {
                return ['success' => false, 'message' => 'Contraseña incorrecta'];
            }
        } else {
            return ['success' => false, 'message' => 'El email no está registrado'];
        }
    }

    public function logout() {
        // Destroy session
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada exitosamente'];
    }
}
?>