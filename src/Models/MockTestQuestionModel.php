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
            'programmes_mock_test_id' => $mockTestId,
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
            ['field' => 'programmes_mock_test_id', 'operator' => '=', 'value' => $mockTestId],
            ['field' => 'qid', 'operator' => '=', 'value' => $questionId]
        ];

        // Call the delete method with the conditions
        return $this->delete($conditions);
    }



    public function getQuestionIdsByMockTestId($mockTestId)
    {
        $query = "SELECT qid FROM programmes_mock_test_questions WHERE programmes_mock_test_id = :mockTestId";
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

}
