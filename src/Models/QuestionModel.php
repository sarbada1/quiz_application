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

  
    public function questionFilter($selectedQuiz)
    {
        $sql = "SELECT questions.*, question_type.`type`, quizzes.title 
                FROM questions 
                JOIN quizzes ON quizzes.id = questions.quiz_id 
                JOIN question_type ON question_type.id = questions.question_type ";

        $params = [];

        if (!empty($selectedQuiz)) {
            $sql .= " WHERE quizzes.id = :quiz";
            $params[':quiz'] = $selectedQuiz;
        }

        $sql .= " ORDER BY questions.id ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching questions: " . $e->getMessage());
        }
    }
    public function getAll()
    {
        $sql = "SELECT questions.*, question_type.`type`, quizzes.title 
                FROM questions 
                JOIN quizzes ON quizzes.id = questions.quiz_id 
                JOIN question_type ON question_type.id = questions.question_type";

        $params = [];


        $sql .= " ORDER BY questions.id ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching questions: " . $e->getMessage());
        }
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

    public function getCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM questions");
        return $stmt->fetchColumn();
    }
}
