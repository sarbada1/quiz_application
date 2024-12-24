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
    public function getQuestionsGroupedPaginated($page = 1, $perPage = 10, $selectedQuiz = null, $questionType = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT questions.*, question_type.`type`, quizzes.title,
                COUNT(*) OVER() as total_count 
                FROM questions 
                JOIN quizzes ON quizzes.id = questions.quiz_id 
                JOIN question_type ON question_type.id = questions.question_type
                WHERE 1=1";
        
        $params = [];
        if ($selectedQuiz) {
            $sql .= " AND quizzes.id = :quiz";
            $params[':quiz'] = $selectedQuiz;
        }
        if ($questionType) {
            $sql .= " AND question_type.id = :question_type";
            $params[':question_type'] = $questionType;
        }
        
        $sql .= " ORDER BY quizzes.title, questions.id 
                 LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            if ($selectedQuiz) {
                $stmt->bindValue(':quiz', $selectedQuiz, PDO::PARAM_INT);
            }
            if ($questionType) {
                $stmt->bindValue(':question_type', $questionType, PDO::PARAM_INT);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalCount = $results[0]['total_count'] ?? 0;
            
            return [
                'questions' => $results,
                'total' => $totalCount,
                'pages' => ceil($totalCount / $perPage)
            ];
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
    public function getQuestionTypes()
    {
        $sql = "SELECT id, type FROM question_type ORDER BY id ASC";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching question types: " . $e->getMessage());
        }
    }
        public function getFilteredQuestions($quizId = null, $questionType = null)
        {
            $query = "
            SELECT q.*, qt.type, qz.title as quiz_title
            FROM questions q
            JOIN question_type qt ON q.question_type = qt.id
            JOIN quizzes qz ON q.quiz_id = qz.id
            WHERE 1=1
        ";
    
            $params = [];
    
            if ($quizId) {
                $query .= " AND q.quiz_id = :quiz_id";
                $params['quiz_id'] = $quizId;
            }
    
            if ($questionType) {
                $query .= " AND q.question_type = :question_type";
                $params['question_type'] = $questionType;
            }
    
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}
