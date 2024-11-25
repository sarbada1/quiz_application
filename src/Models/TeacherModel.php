<?php
namespace MVC\Models;

class TeacherModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE usertype_id = 2");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}