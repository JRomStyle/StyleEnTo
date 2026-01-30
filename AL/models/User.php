<?php

require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../helpers/JWT.php';

class User {
    private $db;
    private $jwt;

    public function __construct() {
        $this->db = DB::getInstance();
        $this->jwt = new JWT();
    }

    public function register($data) {
        // Validar datos
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Todos los campos obligatorios deben ser completados'];
        }

        // Verificar si el usuario o email ya existen
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $this->db->query($sql, [$data['username'], $data['email']]);
        if ($stmt->rowCount() > 0) {
            return ['error' => 'El nombre de usuario o email ya está registrado'];
        }

        // Hash de la contraseña
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Verificar código de referido
        $referredBy = null;
        if (isset($data['referral_code']) && !empty($data['referral_code'])) {
            $sql = "SELECT id FROM users WHERE referral_code = ?";
            $stmt = $this->db->query($sql, [$data['referral_code']]);
            $referrer = $stmt->fetch();
            if ($referrer) {
                $referredBy = $referrer['id'];
            }
        }

        // Insertar usuario
        $sql = "INSERT INTO users (username, email, password, full_name, bio, country, language, referred_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['username'],
            $data['email'],
            $passwordHash,
            $data['full_name'] ?? null,
            $data['bio'] ?? null,
            $data['country'] ?? null,
            $data['language'] ?? 'es',
            $referredBy
        ]);

        $userId = $this->db->lastInsertId();

        // Asignar rol por defecto (artist)
        $sql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, (SELECT id FROM roles WHERE name = 'artist'))";
        $this->db->query($sql, [$userId]);

        // Crear registro de referido si aplica
        if ($referredBy) {
            $sql = "INSERT INTO referrals (referrer_id, referred_id, status, commission) VALUES (?, ?, 'pending', 10.00)";
            $this->db->query($sql, [$referredBy, $userId]);
        }

        // Obtener usuario creado
        return $this->getById($userId);
    }

    public function getReferralCode($userId) {
        $sql = "SELECT referral_code FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        $result = $stmt->fetch();
        return $result['referral_code'] ?? null;
    }

    public function getReferrals($userId) {
        $sql = "SELECT r.*, u.username as referred_username, u.email as referred_email, u.created_at as referred_created FROM referrals r JOIN users u ON r.referred_id = u.id WHERE r.referrer_id = ? ORDER BY r.created_at DESC";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }

    public function getReferralStats($userId) {
        $sql = "SELECT 
                    COUNT(*) as total_referrals,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_referrals,
                    SUM(commission) as total_earned
                FROM referrals WHERE referrer_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetch();
    }

    public function completeReferral($referralId) {
        $sql = "UPDATE referrals SET status = 'completed', completed_at = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$referralId]);
        return ['success' => 'Referral completed successfully'];
    }

    public function login($email, $password) {
        // Buscar usuario por email
        $sql = "SELECT id, username, email, password FROM users WHERE email = ? AND status = 'active'";
        $stmt = $this->db->query($sql, [$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['error' => 'Credenciales inválidas'];
        }

        // Generar token JWT
        $token = $this->jwt->generateToken($user);

        // Actualizar última conexión
        $sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql, [$user['id']]);

        return [
            'token' => $token,
            'user' => $this->getById($user['id'])
        ];
    }

    public function getById($id) {
        $sql = "SELECT id, username, email, full_name, bio, country, language, profile_image, is_verified, created_at, status FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $user = $stmt->fetch();

        if ($user) {
            // Obtener roles
            $sql = "SELECT r.name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $user['roles'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Obtener géneros
            $sql = "SELECT g.name FROM user_genres ug JOIN genres g ON ug.genre_id = g.id WHERE ug.user_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $user['genres'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $user;
    }

    public function updateProfile($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['full_name'])) {
            $updateFields[] = "full_name = ?";
            $params[] = $data['full_name'];
        }

        if (isset($data['bio'])) {
            $updateFields[] = "bio = ?";
            $params[] = $data['bio'];
        }

        if (isset($data['country'])) {
            $updateFields[] = "country = ?";
            $params[] = $data['country'];
        }

        if (isset($data['language'])) {
            $updateFields[] = "language = ?";
            $params[] = $data['language'];
        }

        if (isset($data['profile_image'])) {
            $updateFields[] = "profile_image = ?";
            $params[] = $data['profile_image'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);

        return $this->getById($id);
    }

    public function updatePassword($id, $currentPassword, $newPassword) {
        // Verificar contraseña actual
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['error' => 'La contraseña actual es incorrecta'];
        }

        // Actualizar contraseña
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $this->db->query($sql, [$passwordHash, $id]);

        return ['success' => 'Contraseña actualizada correctamente'];
    }

    public function addGenre($userId, $genreId) {
        $sql = "INSERT IGNORE INTO user_genres (user_id, genre_id) VALUES (?, ?)";
        $this->db->query($sql, [$userId, $genreId]);
        return ['success' => 'Género agregado correctamente'];
    }

    public function removeGenre($userId, $genreId) {
        $sql = "DELETE FROM user_genres WHERE user_id = ? AND genre_id = ?";
        $this->db->query($sql, [$userId, $genreId]);
        return ['success' => 'Género eliminado correctamente'];
    }

    public function getArtistsByGenre($genreName, $limit = 10) {
        $sql = "SELECT u.id, u.username, u.full_name, u.profile_image, u.is_verified 
                FROM users u 
                JOIN user_genres ug ON u.id = ug.user_id 
                JOIN genres g ON ug.genre_id = g.id 
                WHERE g.name = ? AND u.status = 'active' 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$genreName, $limit]);
        return $stmt->fetchAll();
    }
}
