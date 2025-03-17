<?php

namespace MVC\Models;

use PDO;

class SubjectTestModel extends BaseModel 
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'subject_tests');
    }

    public function getByProgramId($id)
    {
        return $this->get([['field' => 'program_id', 'operator' => '=', 'value' => $id]]);
    }
    
    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }

    public function getBySlug($slug)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM subject_tests WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getBySlug: " . $e->getMessage());
            return null;
        }
    }

    public function createSubjectTest($program_id, $name, $description, $time, $slug)
    {
        return $this->insert([
            'program_id' => $program_id,
            'name' => $name,
            'description' => $description,
            'time' => $time,
            'slug' => $slug
        ]);
    }

    public function updateSubjectTest($id, $name, $description, $time, $slug)
    {
        return $this->update(
            [
                'name' => $name,
                'description' => $description,
                'time' => $time,
                'slug' => $slug
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteSubjectTest($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }

    public function getQuestionsWithAnswersBySubjectId($mockTestId, $answeredQuestions = [])
    {

        $query = "
    select 
            qu.id AS question_id,
                qu.question_text,
                a.id AS answer_id,
                a.answer,
                a.isCorrect,
                a.reason
    from quizzes q 
    join categories as c on c.id =q.category_id 
    join questions qu on qu.quiz_id=q.id 
    join answers a on a.question_id=qu.id 
    where q.category_id=:subjectId
        ";
        
        // Exclude answered questions from the query
        if (!empty($answeredQuestions)) {
            $query .= " AND qu.id NOT IN (" . implode(',', $answeredQuestions) . ")";
        }
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['subjectId' => $mockTestId]);
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
}