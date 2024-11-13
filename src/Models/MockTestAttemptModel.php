<?php
namespace MVC\Models;

use PDO;

class MockTestAttemptModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'mock_test_attempts');
    }

    public function createAttempt($data)
    {
        try {
            // Validate required fields
            $requiredFields = ['user_id', 'mock_test_id', 'total_questions', 'completion_status'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }
    
            $sql = "INSERT INTO mock_test_attempts (
                user_id, mock_test_id, total_questions, correct_answers, 
                wrong_answers, unattempted, score, time_taken, 
                completion_status, started_at
            ) VALUES (
                :user_id, :mock_test_id, :total_questions, :correct_answers,
                :wrong_answers, :unattempted, :score, :time_taken,
                :completion_status, NOW()
            )";
    
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($data);
            
            if (!$result) {
                throw new \Exception("Database error: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Database error in createAttempt: " . $e->getMessage());
            throw $e;
        }
    }
    public function completeAttempt($attemptId, $data)
    {
        try {
            $sql = "UPDATE mock_test_attempts SET 
                    correct_answers = :correct_answers,
                    wrong_answers = :wrong_answers,
                    unattempted = :unattempted,
                    score = :score,
                    time_taken = :time_taken,
                    completion_status = 'completed',
                    completed_at = CURRENT_TIMESTAMP
                    WHERE id = :attempt_id";
    
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'correct_answers' => $data['correct_answers'],
                'wrong_answers' => $data['wrong_answers'],
                'unattempted' => $data['unattempted'],
                'score' => $data['score'],
                'time_taken' => $data['time_taken'],
                'attempt_id' => $attemptId
            ]);
        } catch (\PDOException $e) {
            error_log("Database error in completeAttempt: " . $e->getMessage());
            throw $e;
        }
    }
    public function getUserAttempts($userId)
    {
        return $this->get([
            ['field' => 'user_id', 'operator' => '=', 'value' => $userId]
        ], 'started_at DESC');
    }
    public function getIncompleteAttempt($userId, $mockTestId)
    {
        $query = "SELECT * FROM mock_test_attempts 
                 WHERE user_id = :userId 
                 AND mock_test_id = :mockTestId 
                 AND completion_status = 'incomplete'
                 ORDER BY started_at DESC 
                 LIMIT 1";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'userId' => $userId,
            'mockTestId' => $mockTestId
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAnsweredQuestions($attemptId)
    {
        $query = "SELECT question_id 
                 FROM mock_test_answers 
                 WHERE attempt_id = :attemptId";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['attemptId' => $attemptId]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}