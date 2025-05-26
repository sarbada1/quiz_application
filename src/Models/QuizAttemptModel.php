<?php

namespace MVC\Models;

use PDO;
use Exception;
use PDOException;

class QuizAttemptModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'quiz_attempts');
    }
    public function createAttempt($data)
    {
        try {
            error_log("DEBUG: Attempt data: " . json_encode($data));

            $userId = $data['user_id'] ?? 0;
            $quizId = $data['quiz_id'] ?? null;
            $categoryId = $data['category_id'] ?? null;
            $totalQuestions = $data['total_questions'] ?? 0;

            if ($quizId === null && $categoryId === null) {
                $categoryId = 0;
            }

            $sql = "INSERT INTO quiz_attempts 
            (user_id, quiz_id, category_id, total_questions, start_time, status) 
            VALUES 
            ($userId, " .
                ($quizId === null ? "NULL" : $quizId) . ", " .
                ($categoryId === null ? "NULL" : $categoryId) . ", 
            $totalQuestions, 
            NOW(), 
            'started')";

            // Log the SQL for debugging
            error_log("DEBUG: SQL: $sql");

            // Execute the query
            $result = $this->pdo->exec($sql);

            if ($result === false) {
                // Get the error info
                $errorInfo = $this->pdo->errorInfo();
                error_log("DEBUG: SQL Error: " . json_encode($errorInfo));
                throw new Exception("Database error: " . $errorInfo[2]);
            }

            // Get the last insert ID
            $attemptId = $this->pdo->lastInsertId();

            if (!$attemptId) {
                error_log("DEBUG: No attempt ID was returned");
                // Try a more direct approach to get the ID
                $lastIdResult = $this->pdo->query("SELECT LAST_INSERT_ID() as last_id");
                if ($lastIdResult) {
                    $row = $lastIdResult->fetch(PDO::FETCH_ASSOC);
                    $attemptId = $row['last_id'] ?? 0;
                }
            }

            return $attemptId;
        } catch (Exception $e) {
            error_log("ERROR creating attempt: " . $e->getMessage());
            return 0; // Return 0 instead of throwing error so quiz can continue
        }
    }
    public function updateAttempt($attemptId, $data)
    {
        try {
            $updateData = [
                'completed_at' => date('Y-m-d H:i:s'),
                'score' => $data['score'],
                'status' => 'completed'
            ];

            return $this->update($updateData, [
                ['field' => 'id', 'operator' => '=', 'value' => $attemptId]
            ]);
        } catch (PDOException $e) {
            error_log("Error updating quiz attempt: " . $e->getMessage());
            throw new Exception("Failed to update quiz attempt");
        }
    }

    public function saveAnswer($attemptId, $questionId, $answerId, $isCorrect, $questionOrder)
    {
        try {
            // Get category_id for the question
            $stmt = $this->pdo->prepare("SELECT category_id FROM questions WHERE id = ?");
            $stmt->execute([$questionId]);
            $categoryId = $stmt->fetchColumn();

            // Calculate marks obtained
            $stmt = $this->pdo->prepare("
                SELECT marks_allocated/number_of_questions as marks_per_question 
                FROM quiz_categories 
                WHERE quiz_id = (SELECT quiz_id FROM quiz_attempts WHERE id = ?) 
                AND category_id = ?
            ");
            $stmt->execute([$attemptId, $categoryId]);
            $marksPerQuestion = $stmt->fetchColumn();

            $sql = "INSERT INTO quiz_attempt_answers (
                        attempt_id,
                        question_id,
                        category_id,
                        selected_option_id,
                        is_correct,
                        marks_obtained
                    ) VALUES (
                        :attempt_id,
                        :question_id,
                        :category_id,
                        :selected_option_id,
                        :is_correct,
                        :marks_obtained
                    )";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':attempt_id' => $attemptId,
                ':question_id' => $questionId,
                ':category_id' => $categoryId,
                ':selected_option_id' => $answerId,
                ':is_correct' => $isCorrect ? 1 : 0,
                ':marks_obtained' => $isCorrect ? $marksPerQuestion : 0
            ]);
        } catch (PDOException $e) {
            error_log("Error saving answer: " . $e->getMessage());
            return false;
        }
    }

    public function updateAttemptStats($attemptId)
    {
        $sql = "UPDATE quiz_attempts q 
                SET attempted_questions = (
                    SELECT COUNT(DISTINCT question_id) FROM quiz_attempt_answers WHERE attempt_id = :id
                ),
                correct_answers = (
                    SELECT COUNT(*) FROM quiz_attempt_answers WHERE attempt_id = :id AND is_correct = 1
                ),
                obtained_marks = (
                    SELECT COALESCE(SUM(marks_obtained), 0) FROM quiz_attempt_answers WHERE attempt_id = :id
                )
                WHERE q.id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $attemptId]);
    }


    public function completeAttempt($attemptId, $data)
    {
        try {
            $this->pdo->beginTransaction();

            // Get quiz total marks and obtained marks from categories
            $sql = "SELECT 
                    COALESCE(SUM(qaa.marks_obtained), 0) as obtained_marks,
                    (SELECT COALESCE(SUM(marks_allocated), 0) 
                     FROM quiz_categories qc 
                     WHERE qc.quiz_id = qa.quiz_id) as total_marks
                    FROM quiz_attempts qa
                    LEFT JOIN quiz_attempt_answers qaa ON qa.id = qaa.attempt_id
                    WHERE qa.id = ?
                    GROUP BY qa.id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$attemptId]);
            $marks = $stmt->fetch(PDO::FETCH_ASSOC);

            // Update attempt
            $sql = "UPDATE quiz_attempts SET 
                    obtained_marks = :obtained_marks,
                    total_marks = :total_marks,
                    correct_answers = :correct_answers,
                    attempted_questions = :attempted_questions,
                    end_time = CURRENT_TIMESTAMP,
                    status = 'completed'
                    WHERE id = :attempt_id";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':attempt_id' => $attemptId,
                ':obtained_marks' => $marks['obtained_marks'],
                ':total_marks' => $marks['total_marks'],
                ':correct_answers' => $data['correct_answers'],
                ':attempted_questions' => $data['correct_answers'] + $data['wrong_answers']
            ]);

            $this->pdo->commit();
            return $result;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error completing attempt: " . $e->getMessage());
            throw new Exception("Failed to complete attempt");
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
            $sql = "SELECT 
                    qa.id as attempt_id,
                    q.title,
                    q.type,
                    qa.start_time,
                    qa.end_time,
                    qa.total_marks,
                    qa.obtained_marks,
                    qa.total_questions,
                    qa.attempted_questions,
                    qa.correct_answers,
                    qa.status,
                    (SELECT COUNT(DISTINCT category_id) 
                     FROM quiz_attempt_answers 
                     WHERE attempt_id = qa.id) as categories_attempted
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.id
                WHERE qa.user_id = :user_id 
                AND qa.status IN ('completed', 'abandoned')
                ORDER BY qa.start_time DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format data
            return array_map(function ($attempt) {
                return [
                    'attempt_id' => $attempt['attempt_id'],
                    'title' => $attempt['title'],
                    'type' => $attempt['type'],
                    'start_time' => $attempt['start_time'],
                    'end_time' => $attempt['end_time'],
                    'duration' => strtotime($attempt['end_time']) - strtotime($attempt['start_time']),
                    'total_marks' => $attempt['total_marks'],
                    'obtained_marks' => $attempt['obtained_marks'],
                    'accuracy' => $attempt['total_questions'] > 0 ?
                        round(($attempt['correct_answers'] / $attempt['total_questions']) * 100, 2) : 0,
                    'total_questions' => $attempt['total_questions'],
                    'attempted_questions' => $attempt['attempted_questions'],
                    'correct_answers' => $attempt['correct_answers'],
                    'categories_attempted' => $attempt['categories_attempted'],
                    'status' => $attempt['status']
                ];
            }, $attempts);
        } catch (PDOException $e) {
            error_log("Error fetching user history: " . $e->getMessage());
            throw new Exception("Failed to fetch quiz history");
        }
    }
}
