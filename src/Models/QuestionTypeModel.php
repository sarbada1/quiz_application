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
    public function createQuestionType($type)
    {
        return $this->insert([
            'type' => $type,
        ]);
    }
    public function updateQuestionType($id, $type)
    {
        return $this->update(
            [
                'type' => $type,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteQuestionType($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }

   

   
}