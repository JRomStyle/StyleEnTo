<?php

require_once __DIR__ . '/../helpers/JWT.php';
require_once __DIR__ . '/../core/DB.php';

class AuthMiddleware {
    private $jwt;
    private $db;

    public function __construct() {
        $this->jwt = new JWT();
        $this->db = DB::getInstance();
    }

    public function authenticate($requiredRole = null) {
        // Verificar si hay un token en la cabecera Authorization
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (empty($authHeader) || !preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            $this->sendUnauthorizedResponse();
        }

        $token = $matches[1];
        $payload = $this->jwt->validateToken($token);

        if (!$payload) {
            $this->sendUnauthorizedResponse();
        }

        // Obtener los roles del usuario
        $userId = $payload['user_id'];
        $roles = $this->getUserRoles($userId);

        // Verificar si el usuario tiene el rol requerido
        if ($requiredRole && !in_array($requiredRole, $roles)) {
            $this->sendForbiddenResponse();
        }

        // Almacenar la información del usuario en la sesión o en una variable global
        $_SESSION['user'] = $payload;
        $_SESSION['user']['roles'] = $roles;

        return true;
    }

    private function getUserRoles($userId) {
        $sql = "SELECT r.name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $roles;
    }

    private function sendUnauthorizedResponse() {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'No autorizado. Token inválido o faltante.']);
        exit;
    }

    private function sendForbiddenResponse() {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Prohibido. No tienes los permisos necesarios.']);
        exit;
    }

    public function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public function getUser() {
        return $_SESSION['user'] ?? null;
    }

    public function hasRole($role) {
        return isset($_SESSION['user']['roles']) && in_array($role, $_SESSION['user']['roles']);
    }
}
