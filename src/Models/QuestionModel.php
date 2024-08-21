<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class QuestionModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'questions');
    }

    public function getAll()
    {
        $sql = "SELECT questions.*,question_type.`type`,quizzes.title from questions join quizzes on quizzes.id=questions.quiz_id join question_type on question_type.id=questions.question_type";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching category hierarchy: " . $e->getMessage());
        }
        return $this->get([], null, null, 'questions.title ASC');
    }

    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function createQuestion($question_text, $quiz_id, $question_type)
    {
        return $this->insert([
            'question_text' => $question_text,
            'quiz_id' => $quiz_id,
            'question_type' => $question_type,
        ]);
    }
    public function updateQuestion($id, $question_text, $quiz_id, $question_type)
    {
        return $this->update(
            [
                'question_text' => $question_text,
                'quiz_id' => $quiz_id,
                'question_type' => $question_type,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteQuestion($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}
