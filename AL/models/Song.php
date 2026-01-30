<?php

require_once __DIR__ . '/../core/DB.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/S3Storage.php';
require_once __DIR__ . '/../helpers/SpotifyAPI.php';

class Song {
    private $db;
    private $config;
    private $s3;
    private $spotify;

    public function __construct() {
        $this->db = DB::getInstance();
        $this->config = Config::getInstance();
        $this->s3 = new S3Storage();
        $this->spotify = new SpotifyAPI();
    }

    public function create($data) {
        $filePath = $data['file_path'];
        
        // Si es un archivo local, subir a S3
        if (file_exists($filePath)) {
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $destinationPath = 'songs/' . uniqid() . '.' . $fileExtension;
            $s3Result = $this->s3->uploadFile($filePath, $destinationPath, 'audio/mpeg');
            
            if ($s3Result['success']) {
                $filePath = $s3Result['public_url'];
                // Eliminar archivo local después de subir a S3
                unlink($data['file_path']);
            }
        }
        
        $sql = "INSERT INTO songs (user_id, album_id, title, duration, file_path, isrc, lyrics, release_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['user_id'],
            $data['album_id'] ?? null,
            $data['title'],
            $data['duration'],
            $filePath,
            $data['isrc'] ?? null,
            $data['lyrics'] ?? null,
            $data['release_date'] ?? null,
            $data['status'] ?? 'draft'
        ]);
        
        $songId = $this->db->lastInsertId();

        // Asociar géneros si se proporcionan
        if (isset($data['genres']) && is_array($data['genres'])) {
            foreach ($data['genres'] as $genreId) {
                $this->addGenre($songId, $genreId);
            }
        }

        return $songId;
    }

    public function getById($id) {
        $sql = "SELECT * FROM songs WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $song = $stmt->fetch();

        if ($song) {
            // Obtener géneros
            $sql = "SELECT g.id, g.name FROM song_genres sg JOIN genres g ON sg.genre_id = g.id WHERE sg.song_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $song['genres'] = $stmt->fetchAll();

            // Obtener distribución
            $sql = "SELECT d.id, d.platform_id, p.name as platform_name, d.status, d.distribution_date FROM distributions d JOIN platforms p ON d.platform_id = p.id WHERE d.song_id = ?";
            $stmt = $this->db->query($sql, [$id]);
            $song['distributions'] = $stmt->fetchAll();
        }

        return $song;
    }

    public function getByUserId($userId, $status = null) {
        $sql = "SELECT * FROM songs WHERE user_id = ?";
        $params = [$userId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, $params);
        $songs = $stmt->fetchAll();

        // Agregar géneros a cada canción
        foreach ($songs as &$song) {
            $sql = "SELECT g.id, g.name FROM song_genres sg JOIN genres g ON sg.genre_id = g.id WHERE sg.song_id = ?";
            $stmt = $this->db->query($sql, [$song['id']]);
            $song['genres'] = $stmt->fetchAll();
        }

        return $songs;
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['title'])) {
            $updateFields[] = "title = ?";
            $params[] = $data['title'];
        }

        if (isset($data['album_id'])) {
            $updateFields[] = "album_id = ?";
            $params[] = $data['album_id'];
        }

        if (isset($data['duration'])) {
            $updateFields[] = "duration = ?";
            $params[] = $data['duration'];
        }

        if (isset($data['file_path'])) {
            $updateFields[] = "file_path = ?";
            $params[] = $data['file_path'];
        }

        if (isset($data['isrc'])) {
            $updateFields[] = "isrc = ?";
            $params[] = $data['isrc'];
        }

        if (isset($data['lyrics'])) {
            $updateFields[] = "lyrics = ?";
            $params[] = $data['lyrics'];
        }

        if (isset($data['release_date'])) {
            $updateFields[] = "release_date = ?";
            $params[] = $data['release_date'];
        }

        if (isset($data['status'])) {
            $updateFields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (empty($updateFields)) {
            return ['error' => 'No se proporcionaron campos para actualizar'];
        }

        $params[] = $id;
        $sql = "UPDATE songs SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $this->db->query($sql, $params);

        // Actualizar géneros si se proporcionan
        if (isset($data['genres']) && is_array($data['genres'])) {
            // Eliminar géneros existentes
            $sql = "DELETE FROM song_genres WHERE song_id = ?";
            $this->db->query($sql, [$id]);
            
            // Agregar nuevos géneros
            foreach ($data['genres'] as $genreId) {
                $this->addGenre($id, $genreId);
            }
        }

        return $this->getById($id);
    }

    public function delete($id) {
        // Eliminar relaciones de géneros
        $sql = "DELETE FROM song_genres WHERE song_id = ?";
        $this->db->query($sql, [$id]);
        
        // Eliminar distribuciones
        $sql = "DELETE FROM distributions WHERE song_id = ?";
        $this->db->query($sql, [$id]);
        
        // Eliminar canción
        $sql = "DELETE FROM songs WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        return ['success' => 'Canción eliminada correctamente'];
    }

    public function addGenre($songId, $genreId) {
        $sql = "INSERT IGNORE INTO song_genres (song_id, genre_id) VALUES (?, ?)";
        $this->db->query($sql, [$songId, $genreId]);
    }

    public function distribute($songId, $platformIds) {
        $results = [];
        $song = $this->getById($songId);
        
        foreach ($platformIds as $platformId) {
            // Verificar si ya está distribuida
            $sql = "SELECT id FROM distributions WHERE song_id = ? AND platform_id = ?";
            $stmt = $this->db->query($sql, [$songId, $platformId]);
            $existing = $stmt->fetch();
            
            if (!$existing) {
                // Insertar en distributions con status pending
                $sql = "INSERT INTO distributions (song_id, platform_id, status) VALUES (?, ?, 'pending')";
                $this->db->query($sql, [$songId, $platformId]);
                $distributionId = $this->db->lastInsertId();
                
                // Obtener nombre de la plataforma
                $sql = "SELECT name FROM platforms WHERE id = ?";
                $stmt = $this->db->query($sql, [$platformId]);
                $platform = $stmt->fetch();
                
                // Distribuir a la plataforma específica
                $platformResult = ['platform_id' => $platformId, 'platform_name' => $platform['name'], 'status' => 'pending', 'message' => 'Distribución solicitada'];
                
                if ($platform['name'] === 'Spotify') {
                    // Integrar con Spotify API
                    $spotifyResult = $this->spotify->uploadTrack($song['file_path'], [
                        'title' => $song['title'],
                        'artist' => $song['user_id'], // En un entorno real, se obtendría el nombre del artista
                        'album' => $song['album_id'] ? $song['album_id'] : 'Single'
                    ]);
                    
                    if ($spotifyResult['success']) {
                        // Actualizar status a distributed
                        $sql = "UPDATE distributions SET status = 'distributed', distribution_date = CURRENT_TIMESTAMP, message = ? WHERE id = ?";
                        $this->db->query($sql, [$spotifyResult['message'], $distributionId]);
                        $platformResult['status'] = 'distributed';
                        $platformResult['message'] = $spotifyResult['message'];
                        $platformResult['external_id'] = $spotifyResult['track_id'];
                    }
                }
                
                $results[] = $platformResult;
            } else {
                $results[] = ['platform_id' => $platformId, 'status' => 'already_exists', 'message' => 'Ya está distribuida en esta plataforma'];
            }
        }
        
        return $results;
    }

    public function getDistributionStatus($songId) {
        $sql = "SELECT d.id, d.platform_id, p.name as platform_name, d.status, d.distribution_date, d.message FROM distributions d JOIN platforms p ON d.platform_id = p.id WHERE d.song_id = ?";
        $stmt = $this->db->query($sql, [$songId]);
        return $stmt->fetchAll();
    }
}
