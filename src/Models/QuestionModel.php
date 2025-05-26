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
    public function getQuestionsGroupedPaginated($page = 1, $perPage = 10, $selectedCategory = null, $selectedTag = null)
    {
        try {
            $params = [];
            $where = [];

            $sql = "SELECT DISTINCT
                    q.id,
                    q.question_text,
                    q.difficulty_level,
                    q.marks,
                    c.name as category_name,
                    GROUP_CONCAT(DISTINCT t.name) as tags
                    FROM questions q
                    LEFT JOIN categories c ON q.category_id = c.id
                    LEFT JOIN question_tags qt ON q.id = qt.question_id
                    LEFT JOIN tags t ON qt.tag_id = t.id";

            if ($selectedCategory) {
                $where[] = "q.category_id = :category_id";
                $params[':category_id'] = $selectedCategory;
            }

            if ($selectedTag) {
                $where[] = "qt.tag_id = :tag_id";
                $params[':tag_id'] = $selectedTag;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }

            $countSql = "SELECT COUNT(DISTINCT q.id) as total FROM questions q";
            if ($selectedTag) {
                $countSql .= " LEFT JOIN question_tags qt ON q.id = qt.question_id";
            }
            if (!empty($where)) {
                $countSql .= " WHERE " . implode(" AND ", $where);
            }

            $stmt = $this->pdo->prepare($countSql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $sql .= " GROUP BY q.id ORDER BY q.id DESC LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'questions' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'pages' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            error_log("Error in getQuestionsGroupedPaginated: " . $e->getMessage());
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

    public function updateQuestion($id, $question_text, $question_type, $difficulty_level, $marks, $category_id, $year = null)
    {
        $sql = "
            UPDATE questions 
            SET question_text = :question_text,
                question_type = :question_type,
                difficulty_level = :difficulty_level,
                marks = :marks,
                year = :year,
                category_id = :category_id
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':question_text' => $question_text,
            ':question_type' => $question_type,
            ':difficulty_level' => $difficulty_level,
            ':marks' => $marks,
            ':year' => $year,
            ':category_id' => $category_id,
            ':id' => $id
        ]);
    }
    public function getQuestionsByMockTest($mockTestId)
    {
        $sql = "SELECT q.* FROM questions q 
                JOIN mock_test_questions mtq ON q.id = mtq.question_id 
                WHERE mtq.mock_test_id = :mockTestId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['mockTestId' => $mockTestId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function getAnswersByQuestionId($questionId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM answers 
            WHERE question_id = :question_id
        ");

        $stmt->execute([':question_id' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getQuestionsByQuizAndYear($quizId, $year)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM questions 
            WHERE quiz_id = :quiz_id AND year = :year
        ");

        $stmt->execute([
            ':quiz_id' => $quizId,
            ':year' => $year
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getQuestionsByQuizCategories($quizId)
    {
        $sql = "
            SELECT q.*, qc.marks_allocated, qc.number_of_questions, c.name as category_name
            FROM quiz_categories qc
            JOIN questions q ON q.category_id = qc.category_id
            JOIN categories c ON c.id = q.category_id
            WHERE qc.quiz_id = :quiz_id
            ORDER BY q.id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':quiz_id' => $quizId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($questions as $question) {
            $categoryId = $question['category_id'];
            if (!isset($result[$categoryId])) {
                $result[$categoryId] = [
                    'questions' => [],
                    'marks_allocated' => $question['marks_allocated'],
                    'number_of_questions' => $question['number_of_questions']
                ];
            }
            if (count($result[$categoryId]['questions']) < $question['number_of_questions']) {
                $result[$categoryId]['questions'][] = $question;
            }
        }

        return array_merge(...array_column($result, 'questions'));
    }
    public function getFilteredQuestionsByCategoryAndQuiz($quizId, $page, $questionsPerPage, $categoryId = null)
    {
        try {
            $offset = ($page - 1) * $questionsPerPage;

            $sql = "SELECT q.*, c.name as category_name
                   from questions q 
                   join categories c on c.id=q.category_id 
                   join quiz_categories qc on qc.category_id=c.id 
                   join quizzes qz on qz.id=qc.quiz_id
                   where qc.quiz_id=:quiz_id";
            $params = [':quiz_id' => $quizId];
            if ($categoryId) {
                $sql .= " AND q.category_id = :category_id";
                $params[':category_id'] = $categoryId;
            }

            $sql .= " LIMIT :offset, :limit";

            $stmt = $this->pdo->prepare($sql);

            // Bind non-integer parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            // Bind integer parameters with proper type
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $questionsPerPage, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getFilteredQuestionsByCategoryAndQuiz: " . $e->getMessage());
            throw new Exception("Database query error");
        }
    }

    public function getTotalQuestionsByCategoryAndQuiz($quizId, $categoryId = null)
    {
        $sql = "SELECT count(*) as total from questions q join categories c on c.id=q.category_id join quiz_categories qc on qc.category_id=c.id join quizzes qz on qz.id=qc.quiz_id where qc.quiz_id=:quiz_id
";

        $params = [':quiz_id' => $quizId];
        if ($categoryId) {
            $sql .= " AND qc.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $stmt = $this->pdo->prepare($sql);


        // Bind integer parameters with proper type

        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function getPreviousYearQuestions($quizId)
    {
        $sql = "
            SELECT pyq.*, pya.answer, pya.isCorrect
            FROM previous_year_questions pyq
            LEFT JOIN previous_year_answers pya ON pyq.id = pya.question_id
            WHERE pyq.quiz_id = :quiz_id;
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':quiz_id' => $quizId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($questions as $question) {
            $questionId = $question['id'];
            if (!isset($result[$questionId])) {
                $result[$questionId] = [
                    'question_text' => $question['question_text'],
                    'id' => $question['id'],
                    'answers' => [],
                ];
            }
            $result[$questionId]['answers'][] = [
                'answer_text' => $question['answer'],
                'is_correct' => $question['isCorrect'],
            ];
        }

        return $result;
    }

    public function getFilteredQuestionsByCategory($categoryId = null)
    {
        $sql = "SELECT q.*, c.name as category_name
            FROM questions q
            LEFT JOIN categories c ON q.category_id = c.id";

        $params = [];
        if ($categoryId) {
            $sql .= " WHERE q.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function createQuestion($question_text, $difficulty_level, $marks, $category_id, $question_type, $year = null)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO questions (question_text, difficulty_level, marks, category_id, question_type, year) 
            VALUES (:question_text, :difficulty_level, :marks, :category_id, :question_type, :year)
        ");

        $stmt->execute([
            ':question_text' => $question_text,
            ':difficulty_level' => $difficulty_level,
            ':marks' => $marks,
            ':category_id' => $category_id,
            ':question_type' => $question_type,
            ':year' => $year
        ]);

        return $this->pdo->lastInsertId();
    }

    public function assignToQuiz($quizId, $questionId, $order)
    {
        $sql = "INSERT INTO quiz_questions (quiz_id, question_id, question_order) 
                VALUES (:quiz_id, :question_id, :order)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'quiz_id' => $quizId,
            'question_id' => $questionId,
            'order' => $order
        ]);
    }
    public function getRandomQuestionsByCategory($categoryId, $limit)
    {
        try {
            $sql = "SELECT q.* 
                    FROM questions q
                    WHERE q.category_id = :category_id 
                    ORDER BY RAND() 
                    LIMIT :limit";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting random questions: " . $e->getMessage());
            throw new Exception("Error getting questions from category");
        }
    }
    public function addQuestionTags($questionId, $tagIds)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO question_tags (question_id, tag_id) 
            VALUES (:question_id, :tag_id)
        ");

        foreach ($tagIds as $tagId) {
            $stmt->execute([
                ':question_id' => $questionId,
                ':tag_id' => $tagId
            ]);
        }
    }

    public function deleteQuestionTags($questionId)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM question_tags 
            WHERE question_id = :question_id
        ");
        return $stmt->execute([':question_id' => $questionId]);
    }

    public function getQuestionTags($questionId)
    {
        $stmt = $this->pdo->prepare("
            SELECT tag_id FROM question_tags 
            WHERE question_id = :question_id
        ");
        $stmt->execute([':question_id' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function getQuestionById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM questions 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get questions grouped by tag with optional filters
     */
    public function getQuestionsGroupedByTag($tagId = null, $categoryId = null)
    {
        try {
            // Build base query to get questions with tag and category info
            $sql = "SELECT q.id, q.question_text, q.category_id, 
                       t.id as tag_id, t.name as tag_name, t.slug as tag_slug,
                       c.name as category_name
                FROM questions q
                JOIN question_tags qt ON q.id = qt.question_id
                JOIN tags t ON qt.tag_id = t.id
                LEFT JOIN categories c ON q.category_id = c.id
                WHERE 1=1";

            $params = [];

            // Add filters if provided
            if ($tagId) {
                $sql .= " AND t.id = :tag_id";
                $params[':tag_id'] = $tagId;
            }

            if ($categoryId) {
                $sql .= " AND q.category_id = :category_id";
                $params[':category_id'] = $categoryId;
            }

            // Order by tag and question ID
            $sql .= " ORDER BY t.name, q.id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Organize questions by tag
            $questionsByTag = [];

            foreach ($questions as $question) {
                $tagId = $question['tag_id'];

                if (!isset($questionsByTag[$tagId])) {
                    $questionsByTag[$tagId] = [
                        'id' => $tagId,
                        'name' => $question['tag_name'],
                        'slug' => $question['tag_slug'],
                        'questions' => [],
                        'categories' => []
                    ];
                }

                // Add question to the tag's question list
                $questionsByTag[$tagId]['questions'][] = $question;

                // Track categories used within this tag
                $categoryId = $question['category_id'];
                $categoryName = $question['category_name'] ?? 'Uncategorized';

                // If we're tracking categories for statistics
                $categoryFound = false;
                foreach ($questionsByTag[$tagId]['categories'] as &$cat) {
                    if ($cat['id'] == $categoryId) {
                        $cat['count']++;
                        $categoryFound = true;
                        break;
                    }
                }

                if (!$categoryFound) {
                    $questionsByTag[$tagId]['categories'][] = [
                        'id' => $categoryId,
                        'name' => $categoryName,
                        'count' => 1
                    ];
                }
            }

            return $questionsByTag;
        } catch (PDOException $e) {
            error_log("Error in getQuestionsGroupedByTag: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update category for all questions with a specific tag
     */
    public function updateCategoryByTag($tagId, $categoryId)
    {
        try {
            $this->pdo->beginTransaction();

            // Update all questions that have this tag
            $sql = "UPDATE questions q
                JOIN question_tags qt ON q.id = qt.question_id
                SET q.category_id = :category_id
                WHERE qt.tag_id = :tag_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':category_id' => $categoryId,
                ':tag_id' => $tagId
            ]);

            $updatedCount = $stmt->rowCount();
            $this->pdo->commit();

            return $updatedCount;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error in updateCategoryByTag: " . $e->getMessage());
            throw new Exception("Database error while updating questions: " . $e->getMessage());
        }
    }



/**
 * Get questions by category and tag with answers - simplified approach
 */
public function getQuestionsByCategory($categoryId, $limit = 10, $tagId = null)
{
    try {
        // First, log the parameters for debugging
        error_log("DEBUG: Fetching questions - categoryId: $categoryId, limit: $limit, tagId: " . ($tagId ?? 'null'));

        // Get all child category IDs if this is a parent category
        $childCategories = [];
        $childQuery = "SELECT id FROM categories WHERE parent_id = $categoryId";
        $childResult = $this->pdo->query($childQuery);
        
        if ($childResult) {
            while ($row = $childResult->fetch(PDO::FETCH_ASSOC)) {
                $childCategories[] = $row['id'];
            }
        }
        
        // Log the child categories found
        error_log("DEBUG: Child categories: " . implode(', ', $childCategories ?: ['none']));

        // Include the main category ID
        $allCategoryIds = array_merge([$categoryId], $childCategories);
        $categoryIdList = implode(',', $allCategoryIds);
        
        // Build the query - simple approach without prepared statements
        if ($tagId !== null) {
            $sql = "SELECT DISTINCT q.* FROM questions q 
                   JOIN question_tags qt ON q.id = qt.question_id 
                   WHERE q.category_id IN ($categoryIdList) 
                   AND qt.tag_id = $tagId 
                   ORDER BY RAND() 
                   LIMIT $limit";
                   
            error_log("DEBUG: Using simple join query with tag filter: $tagId");
        } else {
            $sql = "SELECT q.* FROM questions q 
                  WHERE q.category_id IN ($categoryIdList) 
                  ORDER BY RAND() 
                  LIMIT $limit";
        }

        // Log the final query for debugging
        error_log("DEBUG: Final SQL: " . $sql);

        // Execute query directly
        $result = $this->pdo->query($sql);
        
        if (!$result) {
            error_log("DEBUG: Query failed: " . print_r($this->pdo->errorInfo(), true));
            return [];
        }

        // Fetch all records
        $questions = $result->fetchAll(PDO::FETCH_ASSOC);
        error_log("DEBUG: Found " . count($questions) . " questions");

        // For each question, get the answers with simple approach
        foreach ($questions as &$question) {
            $questionId = $question['id'];
            $answerSql = "SELECT a.id, a.answer as text, a.isCorrect as is_correct 
                    FROM answers a 
                    WHERE a.question_id = $questionId";

            $answerResult = $this->pdo->query($answerSql);
            
            if ($answerResult) {
                $question['answers'] = $answerResult->fetchAll(PDO::FETCH_ASSOC);
                error_log("DEBUG: Question ID: $questionId with " . count($question['answers']) . " answers");
            } else {
                $question['answers'] = [];
                error_log("DEBUG: Failed to get answers for question $questionId");
            }
        }

        return $questions;
    } catch (\Exception $e) {
        error_log("ERROR: Failed to get questions: " . $e->getMessage());
        error_log("ERROR: Stack trace: " . $e->getTraceAsString());
        return [];
    }
}

/**
 * Get the total number of questions for a category - simplified approach
 */
public function getQuestionCountForCategory($categoryId, $includeChildren = false, $tagId = null)
{
    try {
        // Log parameters for debugging
        error_log("Counting questions for categoryId: $categoryId, includeChildren: " .
            ($includeChildren ? "true" : "false") . ", tagId: " . ($tagId ?? 'null'));

        // Get the category IDs to include
        $categoryIdList = "$categoryId"; // Start with main category
        
        if ($includeChildren) {
            // Get child categories using simple query
            $childQuery = "SELECT id FROM categories WHERE parent_id = $categoryId";
            $childResult = $this->pdo->query($childQuery);
            
            if ($childResult) {
                $childIds = [];
                while ($row = $childResult->fetch(PDO::FETCH_ASSOC)) {
                    $childIds[] = $row['id'];
                }
                
                if (!empty($childIds)) {
                    $categoryIdList .= ',' . implode(',', $childIds);
                }
                
                error_log("DEBUG: Category list for counting: $categoryIdList");
            }
        }
        
        // Build the count query based on whether we need to filter by tag
        if ($tagId !== null) {
            $sql = "SELECT COUNT(DISTINCT q.id) as total 
                   FROM questions q 
                   JOIN question_tags qt ON q.id = qt.question_id 
                   WHERE q.category_id IN ($categoryIdList) 
                   AND qt.tag_id = $tagId";
        } else {
            $sql = "SELECT COUNT(DISTINCT q.id) as total 
                   FROM questions q 
                   WHERE q.category_id IN ($categoryIdList)";
        }

        // Log the query
        error_log("DEBUG: Count SQL: $sql");
        
        // Execute the query directly
        $result = $this->pdo->query($sql);
        
        if (!$result) {
            error_log("DEBUG: Count query failed: " . print_r($this->pdo->errorInfo(), true));
            return 0;
        }
        
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $count = (int)($row['total'] ?? 0);
        
        // Log the count
        error_log("DEBUG: Question count: $count");
        
        return $count;
    } catch (\Exception $e) {
        error_log("ERROR: Failed to count questions: " . $e->getMessage());
        error_log("ERROR: Stack trace: " . $e->getTraceAsString());
        return 0;
    }
}
    private function columnExists($table, $column)
    {
        try {
            $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error checking if column exists: " . $e->getMessage());
            return false;
        }
    }
}
