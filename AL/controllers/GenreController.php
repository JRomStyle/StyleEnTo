<?php

require_once __DIR__ . '/../models/Genre.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

class GenreController {
    private $genreModel;
    private $authMiddleware;

    public function __construct() {
        $this->genreModel = new Genre();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function getAll() {
        $genres = $this->genreModel->getAll();
        http_response_code(200);
        echo json_encode($genres);
    }

    public function getById($id) {
        $genre = $this->genreModel->getById($id);
        if ($genre) {
            http_response_code(200);
            echo json_encode($genre);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Género no encontrado']);
        }
    }

    public function getByName($name) {
        $genre = $this->genreModel->getByName($name);
        if ($genre) {
            http_response_code(200);
            echo json_encode($genre);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Género no encontrado']);
        }
    }

    public function create() {
        // Solo administradores pueden crear géneros
        $this->authMiddleware->authenticate('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $this->genreModel->create($data['name'], $data['description'] ?? null);
            $genre = $this->genreModel->getById($id);
            
            http_response_code(201);
            echo json_encode($genre);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function update($id) {
        // Solo administradores pueden actualizar géneros
        $this->authMiddleware->authenticate('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $genre = $this->genreModel->update($id, $data['name'], $data['description'] ?? null);
            
            if ($genre) {
                http_response_code(200);
                echo json_encode($genre);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Género no encontrado']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }

    public function delete($id) {
        // Solo administradores pueden eliminar géneros
        $this->authMiddleware->authenticate('admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $result = $this->genreModel->delete($id);
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
