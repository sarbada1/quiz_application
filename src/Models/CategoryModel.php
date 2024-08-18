<?php

namespace MVC\Models;

use Exception;
use PDO;
use PDOException;

class CategoryModel extends BaseModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'categories');
    }

    public function getAllCategories()
    {
        return $this->get([], null, null, 'name ASC');
    }

    public function getCategoryById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }

    public function createCategory($name, $parentId)
    {
        return $this->insert([
            'name' => $name,
            'parent_id' => $parentId
        ]);
    }

    public function updateCategory($id, $name, $parentId)
    {
        return $this->update(
            ['name' => $name, 'parent_id' => $parentId],
            [['field' => 'id', 'operator' => '=', 'value' => $id]]
        );
    }

    public function deleteCategory($id)
    {
        return $this->delete([['field' => 'id', 'operator' => '=', 'value' => $id]]);
    }

    public function getCategoriesHierarchy()
    {
        $sql = "SELECT c.id, c.name, c.parent_id, IFNULL(pp.name, 'Top Category') as parent_name 
                FROM categories c 
                LEFT JOIN categories pp ON c.parent_id = pp.id
                ORDER BY c.parent_id, c.name";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching category hierarchy: " . $e->getMessage());
        }
    }

    private function buildHierarchy(array $categories, $parentId = 0)
    {
        $hierarchy = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildHierarchy($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $hierarchy[] = $category;
            }
        }
        return $hierarchy;
    }
}