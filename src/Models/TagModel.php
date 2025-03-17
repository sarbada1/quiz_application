<?php

namespace MVC\Models;

use PDO;

class TagModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'tags');
    }

    public function getAllTags()
    {
        return $this->get([], null, null, 'name ASC');
    }
    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function createTag($name, $slug)
    {
        return $this->insert([
            'name' => $name,
            'slug' => $slug
        ]);
    }

    public function updateTag($id, $name, $slug)
    {
        return $this->update(
            ['name' => $name, 'slug' => $slug],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteTag($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }
}