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
    public function getTopCategories()
    {
        $sql = "SELECT * from categories where parent_id=0";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching category hierarchy: " . $e->getMessage());
        }
    }

    public function getCategoryById($id)
    {
        $result = $this->get([['field' => 'id', 'operator' => '=', 'value' => $id]]);
        return $result[0] ?? null;
    }

    public function createCategory($name, $slug, $parentId)
    {
        return $this->insert([
            'name' => $name,
            'parent_id' => $parentId,
            'slug' => $slug,
        ]);
    }

    public function updateCategory($id, $name, $slug, $parentId)
    {
        return $this->update(
            [
                'name' => $name,
                'slug' => $slug,
                'parent_id' => $parentId
            ],
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

    public function getCategoryBySlug($slug)
    {
        $result = $this->get([['field' => 'slug', 'operator' => '=', 'value' => $slug]]);
        return $result[0] ?? null;
    }

    public function getQuizzesByCategory($categoryId)
    {
        $sql = "SELECT q.id, q.title, q.description, q.slug 
                FROM quizzes q
                WHERE q.category_id = :category_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['category_id' => $categoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching quizzes by category: " . $e->getMessage());
        }
    }

    public function getParentCategoriesWithChildren() {
        $sql = "
            SELECT 
                c1.*, 
                (SELECT GROUP_CONCAT(c2.id, ':', c2.name, ':', c2.slug)
                 FROM categories c2 
                 WHERE c2.parent_id = c1.id) as children
            FROM categories c1
            WHERE c1.parent_id IS NULL
        ";
        
        try {
            $stmt = $this->pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as &$category) {
                if ($category['children']) {
                    $children = [];
                    foreach (explode(',', $category['children']) as $child) {
                        list($id, $name, $slug) = explode(':', $child);
                        $children[] = [
                            'id' => $id,
                            'name' => $name,
                            'slug' => $slug
                        ];
                    }
                    $category['children'] = $children;
                } else {
                    $category['children'] = [];
                }
            }
            
            return $results;
        } catch (\PDOException $e) {
            error_log("Error in getParentCategoriesWithChildren: " . $e->getMessage());
            return [];
        }
    }

    public function getAllCategoriesWithParent()
{
    $sql = "
        SELECT c1.id, c1.name, c2.name as parent_name
        FROM categories c1
        LEFT JOIN categories c2 ON c1.parent_id = c2.id
        ORDER BY c1.name
    ";

    try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error fetching categories: " . $e->getMessage());
    }
}
}
