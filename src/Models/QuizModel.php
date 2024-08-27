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
    public function createQuiz($title,$slug, $description, $category_id,$user_id)
    {
        return $this->insert([
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'category_id' => $category_id,
            'user_id' => $user_id,
        ]);
    }
    public function updateQuiz($id, $title,$slug, $description, $category_id,$user_id)
    {
        return $this->update(
            [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'category_id' => $category_id,
                'user_id' => $user_id,
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
}
