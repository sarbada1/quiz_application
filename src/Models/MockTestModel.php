<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class MockTestModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'programmes_mock_test');
    }

    public function getAll()
    {
        $sql = "SELECT * FROM programmes_mock_test";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching mock tests: " . $e->getMessage());
        }
    }

    public function getByProgramId($id)
    {
        return $this->get([['field' => 'program_id', 'operator' => '=', 'value' => $id]]);
    }
    
    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function getBySlug($slug)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM programmes_mock_test WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getBySlug: " . $e->getMessage());
            return null;
        }
    }


    public function createMockTest($program_id, $name, $time,$slug)
    {
        return $this->insert([
            'program_id' => $program_id,
            'name' => $name,
            'slug' => $slug,
            'time' => $time,
        ]);
    }

    public function updateMockTest($id, $name, $time,$slug)
    {
        return $this->update(
            [
                'name' => $name,
                'slug' => $slug,
                'time' => $time,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteMockTest($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}