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

    public function createCategory($name, $slug, $parentId, $category_type_id)
    {
        try {
            $sql = "INSERT INTO categories (name, slug, parent_id, category_type_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$name, $slug, $parentId ?: null, $category_type_id ?: null]);
        } catch (\PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function updateCategory($id, $name, $slug, $parentId, $category_type_id)
    {
        return $this->update(
            [
                'name' => $name,
                'slug' => $slug,
                'parent_id' => $parentId,
                'category_type_id' => $category_type_id
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



    public function getCategoryBySlug($slug)
    {
        $result = $this->get([['field' => 'slug', 'operator' => '=', 'value' => $slug]]);
        return $result[0] ?? null;
    }

    public function getQuizzesByCategory($categoryId)
    {
        $sql = "SELECT 
            q.id,
            q.title,
            q.type,
            q.status,
            q.slug,
            q.description,
            c.name AS category_name,
            l.level AS difficulty_name,
            COUNT(DISTINCT qu.id) AS question_count
        FROM quizzes q
        JOIN quiz_categories qc ON q.id = qc.quiz_id
        JOIN categories c ON qc.category_id = c.id
        LEFT JOIN questions qu ON c.id = qu.category_id
        LEFT JOIN level l ON qu.difficulty_level = l.id
        WHERE c.id = :categoryId and q.type!='real_exam'
        GROUP BY 
            q.id,
            q.title,
            q.type,
            q.status,
            c.name,
            l.level";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['categoryId' => $categoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching quizzes by category: " . $e->getMessage());
        }
    }

    public function getCategoryByQuiz($quizId)
    {
        $sql = "select c.id,c.name from quizzes q join quiz_categories qc on qc.quiz_id=q.id join categories c on c.id=qc.category_id where q.id=:quizId";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['quizId' => $quizId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching quizzes by category: " . $e->getMessage());
        }
    }

    public function getParentCategoriesWithChildren()
    {
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
    public function getSubjectsWithChapters($categoryId)
    {
        $sql = "WITH RECURSIVE category_tree AS (
        -- Get subjects (first level children)
        SELECT c.*, 0 as level
        FROM categories c 
        WHERE c.parent_id = :categoryId
        
        UNION ALL
        
        -- Get chapters (second level children)
        SELECT c2.*, ct.level + 1
        FROM categories c2
        INNER JOIN category_tree ct ON c2.parent_id = ct.id
        WHERE ct.level < 1
    )
    SELECT ct.*, 
        (SELECT COUNT(*) FROM questions q 
         JOIN quizzes qz ON q.quiz_id = qz.id 
         WHERE qz.category_id = ct.id) as question_count
    FROM category_tree ct
    ORDER BY level, name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['categoryId' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
