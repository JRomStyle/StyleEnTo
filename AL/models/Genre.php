<?php

require_once __DIR__ . '/../core/DB.php';

class Genre {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public function getAll() {
        $sql = "SELECT id, name, description FROM genres ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT id, name, description FROM genres WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }

    public function getByName($name) {
        $sql = "SELECT id, name, description FROM genres WHERE name = ?";
        $stmt = $this->db->query($sql, [$name]);
        return $stmt->fetch();
    }

    public function create($name, $description = null) {
        $sql = "INSERT INTO genres (name, description) VALUES (?, ?)";
        $this->db->query($sql, [$name, $description]);
        return $this->db->lastInsertId();
    }

    public function update($id, $name, $description = null) {
        $sql = "UPDATE genres SET name = ?, description = ? WHERE id = ?";
        $this->db->query($sql, [$name, $description, $id]);
        return $this->getById($id);
    }

    public function delete($id) {
        $sql = "DELETE FROM genres WHERE id = ?";
        $this->db->query($sql, [$id]);
        return ['success' => 'Género eliminado correctamente'];
    }
}
