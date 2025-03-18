<?php

namespace MVC\Models;

use PDO;
use Exception;
use PDOException;

class MockTestAttemptModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'mock_test_attempts');
    }

    public function createAttempt($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO mock_test_attempts (user_id, set_id, total_marks, obtained_marks, correct_answers, wrong_answers, attempted_questions, total_questions)
            VALUES (:user_id, :set_id, :total_marks, :obtained_marks, :correct_answers, :wrong_answers, :attempted_questions, :total_questions)
        ");
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function updateAttempt($attemptId, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE mock_test_attempts
            SET total_marks = :total_marks, obtained_marks = :obtained_marks, correct_answers = :correct_answers, wrong_answers = :wrong_answers, attempted_questions = :attempted_questions, total_questions = :total_questions
            WHERE id = :attempt_id
        ");
        $data['attempt_id'] = $attemptId;
        $stmt->execute($data);
    }

    public function saveAnswer($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO mock_test_answers (attempt_id, question_id, answer_id)
            VALUES (:attempt_id, :question_id, :answer_id)
        ");
        $stmt->execute($data);
    }
    public function getAttemptReview($attemptId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM mock_test_answers
            WHERE attempt_id = :attempt_id
        ");
        $stmt->execute(['attempt_id' => $attemptId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttemptWithAnswers($attemptId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM mock_test_attempts
            WHERE id = :attempt_id
        ");
        $stmt->execute(['attempt_id' => $attemptId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserAttempts($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM mock_test_attempts
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function completeAttempt($attemptId, $score)
    {
        try {
            $sql = "UPDATE mock_test_attempts 
                    SET status = 'completed', 
                        end_time = NOW(),
                        score = :score 
                    WHERE id = :attempt_id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'attempt_id' => $attemptId,
                'score' => $score
            ]);
        } catch (PDOException $e) {
            error_log("Error completing attempt: " . $e->getMessage());
            return false;
        }
    }



    public function getOverallStats()
    {
        try {
            $sql = "SELECT 
                    AVG(score) as avg_score,
                    COUNT(*) as total_attempts,
                    (COUNT(CASE WHEN score >= 40 THEN 1 END) * 100.0 / COUNT(*)) as pass_rate
                    FROM mock_test_attempts
                    WHERE completion_status = 'completed'";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting overall stats: " . $e->getMessage());
            return [
                'avg_score' => 0,
                'total_attempts' => 0,
                'pass_rate' => 0
            ];
        }
    }
    public function getAllAttempts()
    {
        $stmt = $this->pdo->prepare("
            SELECT mta.*, u.name as student_name, q.name as quiz_name
            FROM mock_test_attempts mta
            JOIN users u ON mta.user_id = u.id
            JOIN quiz_sets qs ON mta.set_id = qs.id
            JOIN quizzes q ON qs.quiz_id = q.id
            ORDER BY mta.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function getAttemptAnswers($attemptId)
    {
        try {
            $sql = "SELECT mta.*, q.marks 
                    FROM mock_test_answers mta 
                    JOIN questions q ON mta.question_id = q.id 
                    WHERE mta.attempt_id = :attempt_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['attempt_id' => $attemptId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting attempt answers: " . $e->getMessage());
            return [];
        }
    }
    public function getUserHistory($userId)
    {
        try {
            $sql = "SELECT 
                    mta.id as attempt_id,
                    mt.set_name as name,
                    mta.start_time,
                    mta.end_time,
                    mta.total_marks,
                    mta.obtained_marks,
                    ((mta.obtained_marks / NULLIF(mta.total_marks, 0)) * 100) as score
                FROM mock_test_attempts mta
                JOIN quiz_sets mt ON mta.set_id = mt.id
                join quizzes as q on q.id=mt.quiz_id
                WHERE mta.user_id = :user_id and q.`type`='mock' order by mta.id desc";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return array_map(function ($attempt) {
                return [
                    'name' => $attempt['name'],
                    'score' => round($attempt['score'] ?? 0, 1),
                    'start_time' => $attempt['start_time'],
                    'end_time' => $attempt['end_time'],
                    'total_marks' => $attempt['total_marks'],
                    'obtained_marks' => $attempt['obtained_marks'],
                    'duration' => !empty($attempt['end_time']) && !empty($attempt['start_time']) ?
                        strtotime($attempt['end_time']) - strtotime($attempt['start_time']) : null
                ];
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("Error getting mock test history: " . $e->getMessage());
            return [];
        }
    }
    public function getReviewData($attemptId) 
    {
        try {
            $sql = "SELECT 
                    mta.question_id,
                    mta.answer_id as selected_answer_id,
                    q.question_text,
                    q.marks,
                    a.id as answer_id,
                    a.answer as answer_text,
                    a.isCorrect as is_correct,
                    a.reason,
                    CASE WHEN a.id = mta.answer_id THEN true ELSE false END as is_selected
                FROM mock_test_answers mta
                JOIN questions q ON mta.question_id = q.id 
                JOIN answers a ON q.id = a.question_id
                WHERE mta.attempt_id = :attempt_id
                ORDER BY mta.question_id, a.id";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['attempt_id' => $attemptId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Group by questions
            $questions = [];
            foreach ($rows as $row) {
                if (!isset($questions[$row['question_id']])) {
                    $questions[$row['question_id']] = [
                        'question_id' => $row['question_id'],
                        'question_text' => $row['question_text'],
                        'marks' => $row['marks'],
                        'selected_answer_id' => $row['selected_answer_id'],
                        'answers' => []
                    ];
                }
                
                $questions[$row['question_id']]['answers'][] = [
                    'id' => $row['answer_id'],
                    'answer_text' => $row['answer_text'],
                    'is_correct' => (bool)$row['is_correct'],
                    'is_selected' => (bool)$row['is_selected'],
                    'reason' => $row['reason']
                ];
            }
    
            return array_values($questions);
    
        } catch (PDOException $e) {
            error_log("Error getting review data: " . $e->getMessage());
            return false;
        }
    }
    public function getTestStats($mockTestId)
    {
        try {
            $sql = "SELECT 
                    COUNT(DISTINCT user_id) as attempt_count,
                    MAX(score) as highest_score
                    FROM mock_test_attempts 
                    WHERE mock_test_id = :mock_test_id
                    AND completion_status = 'completed'";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['mock_test_id' => $mockTestId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting test stats: " . $e->getMessage());
            return ['attempt_count' => 0, 'highest_score' => 0];
        }
    }
    public function getUserRank($userId, $mockTestId, $userScore)
    {
        try {
            $sql = "WITH user_rank AS (
    SELECT count(*)+1 as user_rank
    FROM mock_test_attempts mta
    WHERE mta.mock_test_id = :mock_test_id 
    AND mta.score > :score
    AND mta.completion_status = 'completed'
    group by mta.user_id
)
SELECT min(user_rank) 
FROM user_rank";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'mock_test_id' => $mockTestId,
                'score' => $userScore
            ]);
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error getting user rank: " . $e->getMessage());
            return 0;
        }
    }
}
