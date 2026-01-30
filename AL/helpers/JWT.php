<?php

require_once __DIR__ . '/../config/config.php';

class JWT {
    private $secret;
    private $expiration;

    public function __construct() {
        $config = Config::getInstance();
        $jwtConfig = $config->get('jwt');
        $this->secret = $jwtConfig['secret'];
        $this->expiration = $jwtConfig['expiration'];
    }

    public function generateToken($userData) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $userData['id'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'exp' => time() + $this->expiration
        ]);

        $header = $this->base64UrlEncode($header);
        $payload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', "$header.$payload", $this->secret, true);
        $signature = $this->base64UrlEncode($signature);

        return "$header.$payload.$signature";
    }

    public function validateToken($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        $validSignature = hash_hmac('sha256', "$header.$payload", $this->secret, true);
        $validSignature = $this->base64UrlEncode($validSignature);

        if ($signature !== $validSignature) {
            return false;
        }

        $payload = json_decode($this->base64UrlDecode($payload), true);

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
