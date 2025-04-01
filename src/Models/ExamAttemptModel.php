<?php

namespace MVC\Models;

use PDO;
use Exception;
use PDOException;

class ExamAttemptModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'exam_attempts');
    }

    public function hasAttempted($userId, $examId)
    {
        $sql = "SELECT COUNT(*) FROM exam_attempts 
                WHERE user_id = :user_id AND exam_id = :exam_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':exam_id' => $examId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    public function createAttempt($data)
    {
        $sql = "INSERT INTO exam_attempts 
                (user_id, exam_id, score, correct_answers, wrong_answers, completed_at) 
                VALUES 
                (:user_id, :exam_id, :score, :correct_answers, :wrong_answers, :completed_at)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':exam_id' => $data['exam_id'] ?? $data['mock_test_id'], // Fallback for backward compatibility
            ':score' => $data['score'],
            ':correct_answers' => $data['correct_answers'],
            ':wrong_answers' => $data['wrong_answers'],
            ':completed_at' => $data['completed_at']
        ]);

        return $this->pdo->lastInsertId();
    }
    public function getAttemptsByExamId($examId)
    {
        $sql = "SELECT ea.*, u.username as student_name, u.email as student_email
            FROM exam_attempts ea 
            JOIN users u ON ea.user_id = u.id
            WHERE ea.exam_id = :exam_id
            ORDER BY ea.completed_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':exam_id' => $examId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function publishResults($attemptIds)
    {
        // Convert array to string for IN clause
        $placeholders = implode(',', array_fill(0, count($attemptIds), '?'));

        $sql = "UPDATE exam_attempts SET is_published = 1 WHERE id IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($attemptIds);
    }

    public function saveUserAnswer($attemptId, $questionId, $answerId)
    {
        $sql = "INSERT INTO user_answers (attempt_id, question_id, answer_id) 
                VALUES (:attempt_id, :question_id, :answer_id)";
                
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':attempt_id' => $attemptId,
            ':question_id' => $questionId,
            ':answer_id' => $answerId
        ]);
    }

    public function getLatestAttempt($userId, $examId)
    {
        $sql = "SELECT * FROM exam_attempts 
            WHERE user_id = :user_id AND exam_id = :exam_id
            ORDER BY completed_at DESC
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':exam_id' => $examId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUserAnswers($attemptId)
{
    $sql = "SELECT ua.question_id, ua.answer_id, 
            CASE WHEN a.isCorrect = 1 THEN 1 ELSE 0 END as is_correct
            FROM user_answers ua
            JOIN answers a ON ua.answer_id = a.id
            WHERE ua.attempt_id = :attempt_id";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':attempt_id' => $attemptId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function getAttemptById($attemptId)
{
    $sql = "SELECT * FROM exam_attempts WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $attemptId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
