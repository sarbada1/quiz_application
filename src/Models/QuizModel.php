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
        $sql = "SELECT 
                    q.id,
                    q.title,
                    q.slug,
                    q.description,
                    q.type,
                    q.total_marks,
                    q.duration,
                    q.status,
                    GROUP_CONCAT(DISTINCT t.name) as tags,
                    GROUP_CONCAT(DISTINCT c.name) as categories
                FROM quizzes q
                LEFT JOIN quiz_tags qt ON q.id = qt.quiz_id
                LEFT JOIN tags t ON qt.tag_id = t.id
                LEFT JOIN quiz_categories qc ON q.id = qc.quiz_id
                LEFT JOIN categories c ON qc.category_id = c.id
                GROUP BY q.id
                ORDER BY q.created_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching quizzes: " . $e->getMessage());
        }
    }
    public function getQuiz($type)
    {
        $sql = "   SELECT 
            q.id,
            q.title,
            q.slug,
            q.description,
            q.type,
            q.total_marks,
            q.duration,
            q.status,
            q.year,
            q.no_of_student,
            GROUP_CONCAT(DISTINCT t.name) as tags,
            GROUP_CONCAT(DISTINCT c.name) as categories,
            (SELECT COUNT(*) FROM quiz_sets WHERE quiz_id = q.id) as set_count,
            (SELECT COUNT(*) FROM previous_year_questions pyq WHERE pyq.quiz_id = q.id) AS question_count
        FROM quizzes q
        LEFT JOIN quiz_tags qt ON q.id = qt.quiz_id
        LEFT JOIN tags t ON qt.tag_id = t.id
        LEFT JOIN quiz_categories qc ON q.id = qc.quiz_id
        LEFT JOIN categories c ON qc.category_id = c.id
        WHERE q.type = :type
        GROUP BY q.id
        ORDER BY q.created_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['type' => $type]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching quizzes: " . $e->getMessage());
        }
    }
    public function getSetById($setId)
    {
        try {
            $sql = "SELECT * FROM quiz_sets WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $setId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting set by ID: " . $e->getMessage());
            throw new Exception("Error retrieving quiz set");
        }
    }
    public function getQuizCategories($quizId)
    {
        try {
            $sql = "SELECT qc.*, c.name 
                    FROM quiz_categories qc 
                    JOIN categories c ON qc.category_id = c.id 
                    WHERE qc.quiz_id = :quiz_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['quiz_id' => $quizId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getQuizCategories: " . $e->getMessage());
            throw new Exception("Error getting quiz categories");
        }
    }

    public function getCategoryAllocation($quizId, $categoryId)
{
    $sql = "SELECT qc.*, c.name 
            FROM quiz_categories qc
            JOIN categories c ON c.id = qc.category_id
            WHERE qc.quiz_id = :quiz_id AND qc.category_id = :category_id";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':quiz_id' => $quizId,
        ':category_id' => $categoryId
    ]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function getCategoryAllocations($quizId)
{
    $sql = "SELECT qc.*, c.name 
            FROM quiz_categories qc
            JOIN categories c ON c.id = qc.category_id
            WHERE qc.quiz_id = :quiz_id";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':quiz_id' => $quizId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $allocations = [];
    foreach ($results as $row) {
        $allocations[$row['category_id']] = $row;
    }
    
    return $allocations;
}

    public function saveMockConfiguration($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Clear existing configs
            $this->clearQuizCategories($data['quiz_id']);

            // Save new category configurations
            foreach ($data['categories'] as $categoryId => $config) {
                if (!empty($config['marks']) && !empty($config['questions'])) {
                    $sql = "INSERT INTO quiz_categories 
                            (quiz_id, category_id, marks_allocated, number_of_questions) 
                            VALUES (:quiz_id, :category_id, :marks, :questions)";

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        'quiz_id' => $data['quiz_id'],
                        'category_id' => $categoryId,
                        'marks' => $config['marks'],
                        'questions' => $config['questions']
                    ]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function getQuizQuestions($quizId, $questionCount = null)
    {
        try {
            $sql = "SELECT 
                    q.id, 
                    q.question_text,
                    GROUP_CONCAT(a.id ORDER BY a.id) as answer_ids,
                    GROUP_CONCAT(a.answer ORDER BY a.id) as answers,
                    GROUP_CONCAT(a.isCorrect ORDER BY a.id) as correct_answers
                FROM questions q
                JOIN quiz_categories qc ON q.category_id = qc.category_id
                JOIN answers a ON q.id = a.question_id
                WHERE qc.quiz_id = :quiz_id
                GROUP BY q.id
                ORDER BY RAND()";

            if ($questionCount) {
                $sql .= " LIMIT :limit";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':quiz_id', $quizId);

            if ($questionCount) {
                $stmt->bindParam(':limit', $questionCount, PDO::PARAM_INT);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                $answerIds = explode(',', $row['answer_ids']);
                $answerTexts = explode(',', $row['answers']);
                $isCorrect = explode(',', $row['correct_answers']);

                $answers = [];
                for ($i = 0; $i < count($answerIds); $i++) {
                    $answers[] = [
                        'id' => (int)$answerIds[$i],
                        'text' => $answerTexts[$i],
                        'is_correct' => (bool)$isCorrect[$i]
                    ];
                }

                return [
                    'id' => $row['id'],
                    'question_text' => $row['question_text'],
                    'answers' => $answers
                ];
            }, $results);
        } catch (PDOException $e) {
            error_log("Error getting quiz questions: " . $e->getMessage());
            throw new Exception("Error loading quiz questions");
        }
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
    public function getQuizSets($quizId)
    {
        $sql = "SELECT * FROM quiz_sets WHERE quiz_id = :quiz_id ORDER BY set_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createQuizSet($quizId)
    {
        try {
            // Start transaction
            $this->pdo->beginTransaction();

            // Get current max set number
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(MAX(set_number), 0) + 1 
                FROM quiz_sets 
                WHERE quiz_id = :quiz_id
            ");
            $stmt->execute(['quiz_id' => $quizId]);
            $nextSetNumber = $stmt->fetchColumn();

            // Insert new set
            $stmt = $this->pdo->prepare("
                INSERT INTO quiz_sets (quiz_id, set_number, status, created_at) 
                VALUES (:quiz_id, :set_number, 'draft', NOW())
            ");

            $stmt->execute([
                'quiz_id' => $quizId,
                'set_number' => $nextSetNumber
            ]);

            $setId = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return $setId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error creating quiz set: " . $e->getMessage());
            throw new Exception("Failed to create quiz set");
        }
    }

    public function createQuiz($data)
    {
        // Insert basic quiz data
        $sql = "INSERT INTO quizzes (title, slug, description, type, total_marks, duration, status,year) 
                VALUES (:title, :slug, :description, :type, :total_marks, :duration, :status,:year)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'type' => $data['type'],
            'total_marks' => $data['total_marks'],
            'duration' => $data['duration'],
            'status' => $data['status'] ?? 'draft',
            'year' => $data['year'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }
    public function createSet($quizId, $data)
    {
        try {
            $sql = "INSERT INTO quiz_sets (quiz_id, set_name, status, created_at) 
                    VALUES (:quiz_id, :set_name, :status, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'quiz_id' => $quizId,
                'set_name' => $data['set_name'],
                'status' => $data['status']
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error creating set: " . $e->getMessage());
        }
    }

    public function getSets($quizId)
    {
        $sql = "SELECT * FROM quiz_sets WHERE quiz_id = :quiz_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSet($setId)
    {
        $sql = "DELETE FROM quiz_sets WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $setId]);
    }

    public function publishSet($setId)
    {
        $sql = "UPDATE quiz_sets SET status = 'published' WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $setId]);
    }
    public function updateQuiz($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Update quiz basic info
            $sql = "UPDATE quizzes SET 
                    title = :title,
                    description = :description,
                    type = :type,
                    slug=:slug,
                    total_marks = :total_marks,
                    duration = :duration,
                    status = :status,
                    year = :year
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $data['id'],
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'type' => $data['type'],
                'total_marks' => $data['total_marks'],
                'duration' => $data['duration'],
                'status' => $data['status'],
                'year' => $data['year']
            ]);

            // Update categories
            $this->updateQuizCategories($data['id'], $data['categories']);

            // Update tags
            $this->updateQuizTags($data['id'], $data['tags']);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Error updating quiz: " . $e->getMessage());
        }
    }
    public function updateQuizYear($id, $year)
    {
        $stmt = $this->pdo->prepare("
            UPDATE quizzes 
            SET year = :year
            WHERE id = :id
        ");

        return $stmt->execute([
            ':year' => $year,
            ':id' => $id
        ]);
    }
    public function updateStudent($id, $no_of_student)
    {
        $stmt = $this->pdo->prepare("
            UPDATE quizzes 
            SET no_of_student = :no_of_student
            WHERE id = :id
        ");

        return $stmt->execute([
            ':no_of_student' => $no_of_student,
            ':id' => $id
        ]);
    }
    public function getQuizCategoryIds($quizId)
    {
        $sql = "SELECT category_id FROM quiz_categories WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');
    }

    public function getQuizTagIds($quizId)
    {
        $sql = "SELECT tag_id FROM quiz_tags WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tag_id');
    }
    private function updateQuizCategories($quizId, $categories)
    {
        // Clear existing categories
        $sql = "DELETE FROM quiz_categories WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);

        // Add new categories
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $this->addQuizCategory($quizId, [
                    'category_id' => $category['category_id'],
                    'marks_allocated' => $category['marks_allocated'] ?? 0,
                    'number_of_questions' => $category['number_of_questions'] ?? 0
                ]);
            }
        }
    }
    public function updateConfiguration($quizId, $data)
    {
        try {
            $this->pdo->beginTransaction();

            // Delete existing configuration
            $stmt = $this->pdo->prepare("DELETE FROM quiz_categories WHERE quiz_id = ?");
            $stmt->execute([$quizId]);

            // Insert new configuration
            $stmt = $this->pdo->prepare("
                INSERT INTO quiz_categories 
                (quiz_id, category_id, marks_allocated, number_of_questions) 
                VALUES (?, ?, ?, ?)
            ");

            foreach ($data['categories'] as $categoryId => $config) {
                $stmt->execute([
                    $quizId,
                    $categoryId,
                    $config['marks_allocated'],
                    $config['number_of_questions']
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Failed to update configuration: " . $e->getMessage());
        }
    }

    public function getRemainingMarks($quizId)
    {
        try {
            // Get total marks from quiz
            $stmt = $this->pdo->prepare("
                SELECT total_marks FROM quizzes WHERE id = ?
            ");
            $stmt->execute([$quizId]);
            $totalMarks = $stmt->fetchColumn();

            // Get sum of allocated marks
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(marks_allocated), 0) 
                FROM quiz_categories 
                WHERE quiz_id = ?
            ");
            $stmt->execute([$quizId]);
            $allocatedMarks = $stmt->fetchColumn();

            return $totalMarks - $allocatedMarks;
        } catch (PDOException $e) {
            throw new Exception("Failed to calculate remaining marks: " . $e->getMessage());
        }
    }
    private function updateQuizTags($quizId, $tagIds)
    {
        // Clear existing tags
        $sql = "DELETE FROM quiz_tags WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);

        // Add new tags
        if (!empty($tagIds)) {
            foreach ($tagIds as $tagId) {
                $this->addQuizTag($quizId, $tagId);
            }
        }
    }
    public function addQuizTag($quizId, $tagId)
    {
        $sql = "INSERT INTO quiz_tags (quiz_id, tag_id) VALUES (:quiz_id, :tag_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'quiz_id' => $quizId,
            'tag_id' => $tagId
        ]);
    }
    public function addQuizCategory($quizId, $data)
    {
        $sql = "INSERT INTO quiz_categories (quiz_id, category_id, marks_allocated, number_of_questions) 
                VALUES (:quiz_id, :category_id, :marks_allocated, :number_of_questions)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'quiz_id' => $quizId,
            'category_id' => $data['category_id'],
            'marks_allocated' => $data['marks_allocated'],
            'number_of_questions' => $data['number_of_questions']
        ]);
    }
    public function createQuizWithTags($data)
    {
        try {
            $sql = "INSERT INTO quizzes (
                title, slug, description, type, 
                total_marks, duration, status
            ) VALUES (
                :title, :slug, :description, :type,
                :total_marks, :duration, :status
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'type' => $data['type'],
                'total_marks' => $data['total_marks'],
                'duration' => $data['duration'],
                'status' => $data['status']
            ]);

            $quizId = $this->pdo->lastInsertId();

            // Add categories
            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $categoryId) {
                    $this->addQuizCategory($quizId, [
                        'category_id' => $categoryId,
                        'marks_allocated' => 0,
                        'number_of_questions' => 0
                    ]);
                }
            }

            // Add tags
            if (!empty($data['tags'])) {
                foreach ($data['tags'] as $tagId) {
                    $this->addQuizTag($quizId, $tagId);
                }
            }

            return $quizId;
        } catch (Exception $e) {
            throw new Exception("Error creating quiz: " . $e->getMessage());
        }
    }
    public function generateMockQuestions($quizId)
    {
        // Get quiz categories configuration
        $sql = "SELECT * FROM quiz_categories WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Clear existing questions
        $this->clearQuizQuestions($quizId);

        // Generate questions for each category
        foreach ($categories as $category) {
            $questions = $this->getRandomQuestions(
                $category['category_id'],
                $category['difficulty_level'],
                $category['number_of_questions']
            );

            foreach ($questions as $question) {
                $this->addQuizQuestion($quizId, $question['id']);
            }
        }
    }

    private function getRandomQuestions($categoryId, $difficulty, $count)
    {
        $sql = "SELECT * FROM questions 
                WHERE category_id = :category_id 
                AND difficulty_level = :difficulty
                ORDER BY RAND() 
                LIMIT :count";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':difficulty', $difficulty, PDO::PARAM_STR);
        $stmt->bindParam(':count', $count, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function clearQuizCategories($quizId)
    {
        $sql = "DELETE FROM quiz_categories WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['quiz_id' => $quizId]);
    }
    private function clearQuizQuestions($quizId)
    {
        $sql = "DELETE FROM quiz_questions WHERE quiz_id = :quiz_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['quiz_id' => $quizId]);
    }
    private function addQuizQuestion($quizId, $questionId)
    {
        $sql = "INSERT INTO quiz_questions (quiz_id, question_id) 
                VALUES (:quiz_id, :question_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'quiz_id' => $quizId,
            'question_id' => $questionId
        ]);
    }
    public function deleteQuiz($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Delete quiz_categories
            $sql = "DELETE FROM quiz_categories WHERE quiz_id = :quiz_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['quiz_id' => $id]);

            // Delete quiz_tags
            $sql = "DELETE FROM quiz_tags WHERE quiz_id = :quiz_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['quiz_id' => $id]);

            // Delete quiz_questions
            $sql = "DELETE FROM quiz_questions WHERE quiz_id = :quiz_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['quiz_id' => $id]);

            // Delete quiz
            $sql = "DELETE FROM quizzes WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error deleting quiz: " . $e->getMessage());
            return false;
        }
    }
    public function getQuizBySlug($slug)
    {
        $result = $this->get([['field' => 'slug', 'operator' => '=', 'value' => $slug]]);
        return $result[0] ?? null;
    }
    public function getQuizQuestionBySlug($slug)
    {
        $sql = "SELECT 
    q.*,
    c.name AS category_name,
    l.level AS difficulty_name,
    COUNT(DISTINCT qu.id) AS question_count
FROM quizzes q
JOIN quiz_categories qc ON q.id = qc.quiz_id
JOIN categories c ON qc.category_id = c.id
LEFT JOIN questions qu ON c.id = qu.category_id
LEFT JOIN level l ON qu.difficulty_level = l.id
WHERE q.slug = :slug
AND q.type = 'quiz'
GROUP BY q.id, c.id, l.id";

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
    public function getRealExamQuestions($tag)
    {
        $sql = "SELECT q.* FROM questions q
                JOIN question_tags qt ON q.id = qt.question_id
                JOIN tags t ON qt.tag_id = t.id
                WHERE t.name = :tag";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tag' => $tag]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCategoryAllocation($quizId, $categoryId, $numberQuestions, $marksAllocated)
{
    try {
        $sql = "UPDATE quiz_categories 
                SET number_of_questions = :num_questions, marks_allocated = :marks_allocated 
                WHERE quiz_id = :quiz_id AND category_id = :category_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':num_questions' => $numberQuestions,
            ':marks_allocated' => $marksAllocated,
            ':quiz_id' => $quizId,
            ':category_id' => $categoryId
        ]);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error updating category allocation: " . $e->getMessage());
        return false;
    }
}
}
