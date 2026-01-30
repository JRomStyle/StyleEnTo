<?php

require_once __DIR__ . '/../config/config.php';

class SpotifyAPI {
    private $config;
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $apiUrl;
    private $tokenUrl;
    private $accessToken;

    public function __construct() {
        $this->config = Config::getInstance();
        $spotifyConfig = $this->config->get('apis')['spotify'];
        $this->clientId = $spotifyConfig['client_id'];
        $this->clientSecret = $spotifyConfig['client_secret'];
        $this->redirectUri = $spotifyConfig['redirect_uri'];
        $this->apiUrl = $spotifyConfig['api_url'];
        $this->tokenUrl = $spotifyConfig['token_url'];
        $this->accessToken = $this->getAccessToken();
    }

    private function getAccessToken() {
        // En un entorno real, se almacenaría en caché con expiración
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    public function createPlaylist($userId, $name, $description) {
        $url = $this->apiUrl . 'users/' . $userId . '/playlists';
        return $this->makeRequest('POST', $url, [
            'name' => $name,
            'description' => $description,
            'public' => false
        ]);
    }

    public function addTrackToPlaylist($playlistId, $trackUri) {
        $url = $this->apiUrl . 'playlists/' . $playlistId . '/tracks';
        return $this->makeRequest('POST', $url, [
            'uris' => [$trackUri]
        ]);
    }

    public function uploadTrack($filePath, $metadata) {
        // Simulación de subida a Spotify
        // En un entorno real, se usarían endpoints específicos para distribuidores
        return [
            'success' => true,
            'track_id' => 'spotify:track:' . uniqid(),
            'message' => 'Track uploaded successfully to Spotify'
        ];
    }

    public function getTrackStatus($trackId) {
        // Simulación de estado de track en Spotify
        return [
            'status' => 'distributed',
            'message' => 'Track is available on Spotify',
            'url' => 'https://open.spotify.com/track/' . $trackId
        ];
    }

    private function makeRequest($method, $url, $body = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
