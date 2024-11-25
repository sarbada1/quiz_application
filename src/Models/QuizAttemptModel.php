<?php

namespace MVC\Models;

use PDO;

class QuizAttemptModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'quiz_attempts');
    }

    public function createAttempt($data)
    {
        try {
            $this->pdo->beginTransaction();
    
            error_log("Creating attempt with data: " . json_encode($data));
    
            $sql = "INSERT INTO quiz_attempts 
                    (user_id, quiz_id, total_questions, started_at) 
                    VALUES (:user_id, :quiz_id, :total_questions, :started_at)";
    
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'user_id' => $data['user_id'],
                'quiz_id' => $data['quiz_id'],
                'total_questions' => $data['total_questions'],
                'started_at' => $data['started_at']
            ]);
    
            if (!$result) {
                error_log("SQL Error: " . json_encode($stmt->errorInfo()));
                throw new \Exception("Failed to insert attempt");
            }
    
            $attemptId = $this->pdo->lastInsertId();
            
            $this->pdo->commit();
            error_log("Successfully created attempt with ID: $attemptId");
            
            return $attemptId;
    
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log("Error in createAttempt: " . $e->getMessage());
            return false;
        }
    }

    public function saveAnswer($attemptId, $questionId, $answerId, $isCorrect, $questionOrder)
    {
        try {
            $sql = "INSERT INTO quiz_answers 
                    (attempt_id, question_id, answer_id, is_correct, question_order) 
                    VALUES (:attempt_id, :question_id, :answer_id, :is_correct, :question_order)";
    
            $stmt = $this->pdo->prepare($sql);
            
            // Save answer regardless of correctness
            $result = $stmt->execute([
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect ? 1 : 0,
                'question_order' => $questionOrder
            ]);
    
            if (!$result) {
                error_log("Failed to save answer for question: $questionId");
                return false;
            }
    
            return true;
        } catch (\PDOException $e) {
            error_log("Error saving answer: " . $e->getMessage());
            return false;
        }
    }

    public function completeAttempt($attemptId, $data)
    {
        try {
            $sql = "UPDATE quiz_attempts 
                    SET correct_answers = :correct,
                        wrong_answers = :wrong,
                        score = :score,
                        completed_at = NOW()
                    WHERE id = :attempt_id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'attempt_id' => $attemptId,
                'correct' => $data['correct_answers'],
                'wrong' => $data['wrong_answers'],
                'score' => $data['score']
            ]);
        } catch (\PDOException $e) {
            error_log("Error completing attempt: " . $e->getMessage());
            return false;
        }
    }

    public function getAttemptDetails($attemptId)
    {
        try {
            $sql = "SELECT qa.*, q.question_text, a.answer as answer_text
                    FROM quiz_answers qa
                    JOIN questions q ON qa.question_id = q.id
                    JOIN answers a ON qa.answer_id = a.id
                    WHERE qa.attempt_id = :attempt_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['attempt_id' => $attemptId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting attempt details: " . $e->getMessage());
            return [];
        }
    }

    public function getUserAttempts($userId, $quizId = null)
    {
        try {
            $sql = "SELECT qa.*, q.title as quiz_title
                    FROM quiz_attempts qa
                    JOIN quizzes q ON qa.quiz_id = q.id
                    WHERE qa.user_id = :user_id";

            $params = ['user_id' => $userId];

            if ($quizId) {
                $sql .= " AND qa.quiz_id = :quiz_id";
                $params['quiz_id'] = $quizId;
            }

            $sql .= " ORDER BY qa.started_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting user attempts: " . $e->getMessage());
            return [];
        }
    }
    public function getReviewData($attemptId) 
    {
        try {
            $sql = "SELECT 
                    qa.question_id,
                    qa.answer_id as selected_answer_id,
                    qa.is_correct as question_correct,
                    q.question_text,
                    q.id as qid,
                    a.id as aid,
                    a.answer as answer_text,
                    a.isCorrect as is_correct,
                    a.reason
                FROM quiz_answers qa
                JOIN questions q ON qa.question_id = q.id
                JOIN answers a ON q.id = a.question_id
                WHERE qa.attempt_id = :attempt_id
                ORDER BY qa.question_order";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['attempt_id' => $attemptId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $questions = [];
            foreach ($rows as $row) {
                $qid = $row['qid'];
                if (!isset($questions[$qid])) {
                    $questions[$qid] = [
                        'question_id' => $row['question_id'],
                        'question_text' => $row['question_text'],
                        'selected_answer_id' => $row['selected_answer_id'],
                        'is_correct' => $row['question_correct'],
                        'answers' => [],
                        'reason' => $row['reason']
                    ];
                }
                $questions[$qid]['answers'][] = [
                    'id' => $row['aid'],
                    'answer_text' => $row['answer_text'],
                    'is_correct' => (bool)$row['is_correct']
                ];
            }
            
            return array_values($questions);
        } catch (\PDOException $e) {
            error_log("Error in getReviewData: " . $e->getMessage());
            return false;
        }
    }
    public function saveToHistory($data)
    {
        try {
            $sql = "INSERT INTO user_quiz_history 
                (user_id, quiz_id, attempt_id, score) 
                VALUES (:user_id, :quiz_id, :attempt_id, :score)";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            error_log("Error saving to history: " . $e->getMessage());
            return false;
        }
    }

    public function getUserHistory($userId)
    {
        try {
            $sql = "SELECT qa.*, q.title as quiz_title,
                    qa.correct_answers,
                    qa.wrong_answers,
                    qa.total_questions,
                    qa.score,
                    qa.completed_at as attempted_at,
                    qa.id as attempt_id
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                WHERE qa.user_id = :user_id
                AND qa.completed_at IS NOT NULL
                ORDER BY qa.completed_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting quiz history: " . $e->getMessage());
            return [];
        }
    }
}
