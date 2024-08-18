<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class QuizModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'quizzes');
    }

    public function getAll()
    {
        $sql = "SELECT quizzes.*,categories.name
            from quizzes
    join categories on categories.id = quizzes.category_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching category hierarchy: " . $e->getMessage());
        }
        return $this->get([], null, null, 'title ASC');
    }

    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function createQuiz($title, $description, $category_id)
    {
        return $this->insert([
            'title' => $title,
            'description' => $description,
            'category_id' => $category_id,
        ]);
    }
    public function updateQuiz($id, $title, $description, $category_id)
    {
        return $this->update(
            [
                'title' => $title,
                'description' => $description,
                'category_id' => $category_id,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteQuiz($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}
