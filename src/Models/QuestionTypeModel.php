<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class QuestionTypeModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'question_type');
    }

    public function getAll()
    {
        return $this->get([], null, null, 'type ASC');
    }

    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function createQuestionType($type, $slug, $time_per_question)
    {
        return $this->insert([
            'type' => $type,
            'slug' => $slug,
            'time_per_question' => $time_per_question,
        ]);
    }
    public function updateQuestionType($id, $type, $slug, $time_per_question)
    {
        return $this->update(
            [
                'type' => $type,
                'slug' => $slug,
                'time_per_question' => $time_per_question,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteQuestionType($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}
