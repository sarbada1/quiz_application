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

    public function getQuestion($level, $num, $quiz_id)
    {
        $sql = "SELECT q.* 
                FROM questions q 
                JOIN quizzes qz ON qz.id = q.quiz_id 
                WHERE qz.difficulty_level = :level 
                AND q.quiz_id = :quiz_id 
                LIMIT $num";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':level', $level, PDO::PARAM_STR);
        $stmt->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function createQuiz($title, $slug, $description, $category_id, $user_id, $difficulty_level)
    {
        return $this->insert([
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'category_id' => $category_id,
            'user_id' => $user_id,
            'difficulty_level' => $difficulty_level,
        ]);
    }
    public function updateQuiz($id, $title, $slug, $description, $category_id, $user_id, $difficulty_level)
    {
        return $this->update(
            [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'category_id' => $category_id,
                'user_id' => $user_id,
                'difficulty_level' => $difficulty_level,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteQuiz($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
    public function getQuizBySlug($slug)
    {
        $result = $this->get([['field' => 'slug', 'operator' => '=', 'value' => $slug]]);
        return $result[0] ?? null;
    }
    public function getQuizQuestionBySlug($slug)
    {
        $sql = "SELECT q.*, c.name AS category_name, l.level AS difficulty_name, 
                       (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
                FROM quizzes q
                JOIN categories c ON q.category_id = c.id
                JOIN level l ON q.difficulty_level = l.id
                WHERE q.slug = :slug";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching quiz by slug: " . $e->getMessage());
        }
    }
    public function getQuestionsByQuizId($quizId)
    {
        $sql = "SELECT q.id, q.question_text, qt.type, qt.time_per_question, qt.slug as question_type_slug
                FROM questions q
                JOIN question_type qt ON q.question_type = qt.id
                WHERE q.quiz_id = :quiz_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['quiz_id' => $quizId]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch answers for each question
            foreach ($questions as &$question) {
                $answerSql = "SELECT id, answer, reason, isCorrect 
                              FROM answers 
                              WHERE question_id = :question_id";
                $answerStmt = $this->pdo->prepare($answerSql);
                $answerStmt->execute(['question_id' => $question['id']]);
                $question['answers'] = $answerStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $questions;
        } catch (PDOException $e) {
            // Log the error and return an empty array or throw an exception
            error_log("Error fetching questions for quiz ID $quizId: " . $e->getMessage());
            return [];
            // Alternatively: throw new \Exception("Error fetching questions: " . $e->getMessage());
        }
    }
    public function getBySlug($slug)
    {
        $sql = "SELECT q.*, c.name as category_name, l.level as difficulty_name,
                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as question_count
                FROM quizzes q
                LEFT JOIN categories c ON c.id = q.category_id
                LEFT JOIN level l ON l.id = q.difficulty_level
                WHERE q.slug = :slug";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRandomQuestions($quizId, $count)
    {
        $sql = "SELECT 
                    q.id,
                    q.question_text,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', a.id,
                            'text', a.answer,
                            'correct_answer', a.isCorrect,
                            'reason', COALESCE(a.reason, '')
                        )
                    ) as answers
                FROM questions q
                LEFT JOIN answers a ON q.id = a.question_id
                WHERE q.quiz_id = :quiz_id
                GROUP BY q.id
                ORDER BY RAND()
                LIMIT :count";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
            $stmt->bindValue(':count', $count, PDO::PARAM_INT);
            $stmt->execute();

            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Process answers
            foreach ($questions as &$question) {
                if ($question['answers']) {
                    $question['answers'] = json_decode($question['answers'], true);
                } else {
                    $question['answers'] = [];
                }
            }

            return $questions;
        } catch (\PDOException $e) {
            error_log("Error fetching random questions: " . $e->getMessage());
            return [];
        }
    }
    public function getCustomQuestions($categoryId, $levelId, $questionCount) 
    {
        try {
            $sql = "SELECT q.*, qz.category_id, qz.difficulty_level, 
                    a.id as answer_id, a.answer as text, a.isCorrect as correct_answer 
                    FROM questions q
                    JOIN quizzes qz ON q.quiz_id = qz.id
                    JOIN answers a ON q.id = a.question_id 
                    WHERE 1=1";
            
            $params = [];
    
            if ($categoryId) {
                $sql .= " AND qz.category_id = :category_id";
                $params['category_id'] = $categoryId;
            }
    
            if ($levelId) {
                $sql .= " AND qz.difficulty_level = :level_id";
                $params['level_id'] = $levelId;
            }
    
            // Randomize questions but keep answers grouped
            $sql .= " ORDER BY RAND()";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Group answers by question
            $questions = [];
            foreach ($rows as $row) {
                if (!isset($questions[$row['id']])) {
                    $questions[$row['id']] = [
                        'id' => $row['id'],
                        'question_text' => $row['question_text'],
                        'answers' => []
                    ];
                }
                $questions[$row['id']]['answers'][] = [
                    'id' => $row['answer_id'],
                    'text' => $row['text'],
                    'correct_answer' => $row['correct_answer']
                ];
            }
    
            return array_slice(array_values($questions), 0, $questionCount);
        } catch (\PDOException $e) {
            error_log("Error in getCustomQuestions: " . $e->getMessage());
            return [];
        }
    }
}
