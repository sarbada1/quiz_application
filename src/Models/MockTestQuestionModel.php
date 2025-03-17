<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class MockTestQuestionModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'programmes_mock_test_questions');
    }

    public function getByMockTestId($mockTestId)
    {
        return $this->get([['field' => 'programmes_mock_test_id', 'operator' => '=', 'value' => $mockTestId]]);
    }


    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }

    public function createQuestion($mockTestId, $question)
    {
        return $this->insert([
            'quiz_id' => $mockTestId,
            'qid' => $question,
        ]);
    }

    public function updateQuestion($id, $question)
    {
        return $this->update(
            [
                'qid' => $question,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteQuestion($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }

    public function deleteMockQuestion($mockTestId, $questionId)
    {
        // Define the conditions for deletion
        $conditions = [
            ['field' => 'quiz_id', 'operator' => '=', 'value' => $mockTestId],
            ['field' => 'qid', 'operator' => '=', 'value' => $questionId]
        ];

        // Call the delete method with the conditions
        return $this->delete($conditions);
    }
    public function getAnswers($questionId) {
        try {
            $sql = "SELECT DISTINCT id, answer, isCorrect, reason 
                    FROM answers 
                    WHERE question_id = :question_id";
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['question_id' => $questionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting answers: " . $e->getMessage());
            return [];
        }
    }
    public function getQuestionsWithAnswers($attemptId) 
    {
        try {
            $sql = "SELECT q.id, q.question_text, q.marks,
                          c.name as category_name,
                          mta.answer_id as user_answer_id,
                          CASE WHEN a.isCorrect = 1 THEN a.id END as correct_answer_id,
                          mta.is_correct,
                          a.answer as explanation
                   FROM mock_test_answers mta
                   JOIN questions q ON mta.question_id = q.id
                   JOIN categories c ON q.category_id = c.id
                   JOIN answers a ON q.id = a.question_id
                   WHERE mta.attempt_id = :attempt_id
                   ORDER BY c.name, q.id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['attempt_id' => $attemptId]);
            
            $questions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!isset($questions[$row['category_name']])) {
                    $questions[$row['category_name']] = [];
                }
                $questions[$row['category_name']][] = $row;
            }
            
            return $questions;
            
        } catch (PDOException $e) {
            error_log("Error getting questions: " . $e->getMessage());
            throw new Exception("Error retrieving test questions");
        }
    }
    public function getAnswerById($answerId) {
        try {
            $sql = "SELECT id, answer, isCorrect, reason 
                    FROM answers 
                    WHERE id = :answer_id";
                    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['answer_id' => $answerId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting answer by ID: " . $e->getMessage());
            throw new Exception("Error retrieving answer");
        }
    }
    public function getQuestionIdsByMockTestId($mockTestId)
    {
        $query = "SELECT qid FROM programmes_mock_test_questions WHERE quiz_id = :mockTestId";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['mockTestId' => $mockTestId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deleteQuestionByMockTestAndQuestionId($mockTestId, $questionId)
    {
        $query = "DELETE FROM programmes_mock_test_questions WHERE programmes_mock_test_id = :mockTestId AND qid = :questionId";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['mockTestId' => $mockTestId, 'questionId' => $questionId]);
    }
    public function deleteAllQuestionsForMockTest($mockTestId)
    {
        $query = "DELETE FROM programmes_mock_test_questions WHERE programmes_mock_test_id = :mockTestId";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['mockTestId' => $mockTestId]);
    }
    public function getQuestionsWithAnswersByMockTestId($mockTestId, $answeredQuestions = [])
    {
        $query = "
            SELECT 
                pmtq.id AS mock_test_question_id,
                q.id AS question_id,
                q.question_text,
                a.id AS answer_id,
                a.answer,
                a.isCorrect,
                a.reason
            FROM programmes_mock_test_questions pmtq
            JOIN questions q ON pmtq.qid = q.id
            JOIN answers a ON q.id = a.question_id
            WHERE pmtq.programmes_mock_test_id = :mockTestId
        ";
        
        // Exclude answered questions from the query
        if (!empty($answeredQuestions)) {
            $query .= " AND q.id NOT IN (" . implode(',', $answeredQuestions) . ")";
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['mockTestId' => $mockTestId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $questions = [];
        foreach ($results as $row) {
            // Group answers under each question
            if (!isset($questions[$row['question_id']])) {
                $questions[$row['question_id']] = [
                    'id' => $row['question_id'],
                    'question_text' => $row['question_text'],
                    'answers' => [],
                    'reason' => null,
                ];
            }
            // Add answers for each question
            $questions[$row['question_id']]['answers'][] = [
                'id' => $row['answer_id'],
                'answer' => $row['answer'],
                'isCorrect' => (bool)$row['isCorrect'],
            ];
    
            // Add the reason for the correct answer, if it exists
            if ((bool)$row['isCorrect']) {
                $questions[$row['question_id']]['reason'] = $row['reason'];
            }
        }
    
        return array_values($questions);  // Ensure the questions are returned as a proper indexed array
    }
    
    public function checkAnswer($answerId, $questionId)
{
    $query = "SELECT isCorrect FROM answers WHERE id = :answerId AND question_id = :questionId";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['answerId' => $answerId, 'questionId' => $questionId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return true if the answer is correct, false otherwise
    return $result ? (bool)$result['isCorrect'] : false;
}
public function getTotalQuestions($mockTestId)
{
    try {
        $query = "SELECT COUNT(DISTINCT qid) as total 
                 FROM programmes_mock_test_questions 
                 WHERE programmes_mock_test_id = :mockTestId";
                 
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['mockTestId' => $mockTestId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    } catch (PDOException $e) {
        // Log error if needed
        return 0; // Return 0 if there's an error
    }
}

public function getQuestionCountForCategory($mockTestId, $categoryId)
{
    $sql = "SELECT COUNT(*) as count
            FROM programmes_mock_test_questions mtq
            JOIN questions q ON q.id = mtq.qid
            WHERE mtq.quiz_id = :mock_test_id AND q.category_id = :category_id";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':mock_test_id' => $mockTestId,
        ':category_id' => $categoryId
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) $result['count'];
}
public function getQuestionCountByCategory($mockTestId) 
{
    $sql = "SELECT q.category_id, COUNT(*) as count
            FROM programmes_mock_test_questions mtq
            JOIN questions q ON q.id = mtq.qid
            WHERE mtq.quiz_id = :mock_test_id
            GROUP BY q.category_id";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':mock_test_id' => $mockTestId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $counts = [];
    foreach ($results as $row) {
        $counts[$row['category_id']] = (int) $row['count'];
    }
    
    return $counts;
}
}
