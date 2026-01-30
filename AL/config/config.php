<?php

class Config {
    private $settings = [];
    private static $instance = null;

    private function __construct() {
        // Configuración de la base de datos
        $this->settings['db'] = [
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'dbname' => 'music_distribution',
            'charset' => 'utf8mb4'
        ];

        // Configuración de JWT
        $this->settings['jwt'] = [
            'secret' => 'your_secret_key_here',
            'expiration' => 3600 // 1 hora
        ];

        // Configuración de almacenamiento
        $this->settings['storage'] = [
            'local_path' => __DIR__ . '/../public/uploads/',
            'max_file_size' => 50 * 1024 * 1024 // 50MB
        ];

        // Configuración de APIs externas
        $this->settings['apis'] = [
            'spotify' => [
                'client_id' => 'your_spotify_client_id',
                'client_secret' => 'your_spotify_client_secret',
                'redirect_uri' => 'http://localhost/AL/api/spotify/callback',
                'api_url' => 'https://api.spotify.com/v1/',
                'token_url' => 'https://accounts.spotify.com/api/token'
            ],
            'stripe' => [
                'secret_key' => 'your_stripe_secret_key',
                'public_key' => 'your_stripe_public_key',
                'api_version' => '2023-10-16'
            ],
            's3' => [
                'access_key' => 'your_s3_access_key',
                'secret_key' => 'your_s3_secret_key',
                'bucket' => 'your_s3_bucket',
                'region' => 'us-east-1',
                'endpoint' => 'https://s3.amazonaws.com'
            ]
        ];
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function get($key) {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    public function set($key, $value) {
        $this->settings[$key] = $value;
    }
}
