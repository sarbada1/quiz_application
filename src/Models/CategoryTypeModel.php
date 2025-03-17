<?php

namespace MVC\Models;

use PDO;

class CategoryTypeModel extends BaseModel {
    public $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM category_types ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM category_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name) {
        $stmt = $this->pdo->prepare("INSERT INTO category_types (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function update($id, $name) {
        $stmt = $this->pdo->prepare("UPDATE category_types SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM category_types WHERE id = ?");
        return $stmt->execute([$id]);
    }
}