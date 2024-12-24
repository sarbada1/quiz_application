<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class ProgramModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'programmes');
    }

    public function getAll()
    {
        return $this->get([], null, null, 'id ASC');
    }

    public function getById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }
    public function getBySlug($slug)
    {
        $result = $this->get([['field' => 'slug', 'operator' => '=', 'value' => $slug]]);
        return $result[0] ?? null;
    }

    public function getWithCategory()
    {
        $sql = "
            SELECT p.*,c.name as cname from programmes as p join categories as c on c.id=p.category_id 
        ";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching category hierarchy: " . $e->getMessage());
        }
        return $this->get([], null, null, 'programmes.title ASC');
    }
    public function createProgram($categoryId, $name,$slug, $description = null)
    {
        return $this->insert([
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);
    }

    public function updateProgram($id, $categoryId, $name, $slug,$description = null)
    {
        return $this->update(
            [
                'category_id' => $categoryId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteProgram($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }

    public function getProgramsByCategory($categoryId)
    {
        return $this->get([['field' => 'category_id', 'operator' => '=', 'value' => $categoryId]], null, null, 'name ASC');
    }
}
