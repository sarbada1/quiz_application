<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class AnswerModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'answers');
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
    public function getByQuestionId($id)
    {
        return $this->get([['field' => 'question_id', 'operator' => '=', 'value' => $id]]);
    }
    
    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function createAnswer($question_id, $answer,$reason, $isCorrect)
    {
        return $this->insert([
            'question_id' => $question_id,
            'answer' => $answer,
            'reason' => $reason,
            'isCorrect' => $isCorrect,
        ]);
    }
    public function updateAnswer($id,$question_id, $answer,$reason, $isCorrect)
    {
        return $this->update(
            [
                'question_id' => $question_id,
                'answer' => $answer,
                'reason' => $reason,
                'isCorrect' => $isCorrect,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteAnswer($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}
