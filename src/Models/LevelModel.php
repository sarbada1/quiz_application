<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class LevelModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'level');
    }

    public function getAll()
    {
        return $this->get([], null, null, 'level ASC');
    }

    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function createLevel($level)
    {
        return $this->insert([
            'level' => $level,
         
        ]);
    }
    public function updateLevel($id,$level)
    {
        return $this->update(
            [
                'level' => $level,            
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteLevel($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}
