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
    public function getCategoriesByQuizTags($quizId)
    {
        try {
            // This query finds categories associated with the quiz tags
            $sql = "SELECT DISTINCT c.id, c.name, c.slug, c.parent_id
                FROM categories c
                JOIN tag_categories tc ON c.id = tc.category_id
                JOIN quiz_tags qt ON tc.tag_id = qt.tag_id
                WHERE qt.quiz_id = :quiz_id and (c.parent_id = 0 OR c.parent_id IS NULL)
                ORDER BY c.name ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':quiz_id' => $quizId]);

            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Log the results for debugging
            error_log("Found " . count($categories) . " categories for quiz ID $quizId");

            return $categories;
        } catch (PDOException $e) {
            error_log("Error getting categories by quiz tags: " . $e->getMessage());
            return [];
        }
    }
    public function getCategoryWithChildren($categoryId)
{
    try {
        // Get the parent category
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $categoryId]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$parent) {
            return ['id' => $categoryId, 'name' => 'Unknown', 'children' => []];
        }
        
        // Get all children
        $sql = "SELECT * FROM categories WHERE parent_id = :parent_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':parent_id' => $categoryId]);
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return parent with children
        $parent['children'] = $children;
        return $parent;
    } catch (PDOException $e) {
        error_log("Error getting category with children: " . $e->getMessage());
        return ['id' => $categoryId, 'name' => 'Error', 'children' => []];
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
                        $parts = explode(':', $child);
                        $children[] = [
                            'id' => $parts[0] ?? null,
                            'name' => $parts[1] ?? null,
                            'slug' => $parts[2] ?? null
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
    // Replace the updateCategoryTagAssociations and getCategoriesByTagId methods



    /**
     * Get categories associated with any of the provided tag IDs
     */
    public function getCategoriesByTagIds($tagIds)
    {
        if (empty($tagIds)) {
            return [];
        }

        // Convert to array if string is passed
        if (!is_array($tagIds)) {
            $tagIds = [$tagIds];
        }

        // Create placeholders for SQL query
        $placeholders = rtrim(str_repeat('?,', count($tagIds)), ',');

        // Get categories directly linked to tags via junction table
        $sql = "SELECT DISTINCT c.* FROM categories c
            JOIN tag_categories tc ON c.id = tc.category_id
            WHERE tc.tag_id IN ($placeholders)
            ORDER BY c.parent_id, c.name";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($tagIds);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCategoriesByTagIds: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Get hierarchical categories for a specific tag including parent categories
     */


    public function getCategoriesHierarchyForTag($tagId)
    {
        try {
            // Get parent categories directly associated with this tag
            $sql = "SELECT DISTINCT c.id, c.name, c.slug, c.parent_id
                FROM categories c
                JOIN tag_categories tc ON c.id = tc.category_id
                WHERE tc.tag_id = :tag_id
                AND (c.parent_id = 0 OR c.parent_id IS NULL)"; // Only get top-level categories

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':tag_id' => $tagId]);
            $parentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("DEBUG: Found " . count($parentCategories) . " top-level categories from tag_categories for tag $tagId");

            // If no direct parent categories found, get child categories and their parents
            if (empty($parentCategories)) {
                $sql = "SELECT DISTINCT p.id, p.name, p.slug, p.parent_id
                   FROM categories p
                   JOIN categories c ON c.parent_id = p.id
                   JOIN tag_categories tc ON c.id = tc.category_id
                   WHERE tc.tag_id = :tag_id
                   AND (p.parent_id = 0 OR p.parent_id IS NULL)"; // Only get top-level parent categories

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':tag_id' => $tagId]);
                $parentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                error_log("DEBUG: Found " . count($parentCategories) . " parent categories of associated children for tag $tagId");
            }

            // Build the hierarchy
            $result = [];

            foreach ($parentCategories as $parent) {
                // Get child categories
                $childSql = "SELECT c.id, c.name, c.slug, c.parent_id
                       FROM categories c 
                       WHERE c.parent_id = :parent_id";

                $childStmt = $this->pdo->prepare($childSql);
                $childStmt->execute([':parent_id' => $parent['id']]);
                $children = $childStmt->fetchAll(PDO::FETCH_ASSOC);

                // Initialize the parent with empty children and count
                $parent['children'] = [];
                $parent['question_count'] = 0;
                $parent['total_questions'] = 0;

                // Get direct question count for parent
                $parentQuestionSql = "SELECT COUNT(DISTINCT q.id) as count
                                FROM questions q
                                JOIN question_tags qt ON q.id = qt.question_id
                                WHERE q.category_id = :category_id
                                AND qt.tag_id = :tag_id";

                $parentQuestionStmt = $this->pdo->prepare($parentQuestionSql);
                $parentQuestionStmt->execute([
                    ':category_id' => $parent['id'],
                    ':tag_id' => $tagId
                ]);

                $parentQuestionCount = $parentQuestionStmt->fetchColumn();
                $parent['question_count'] = (int)$parentQuestionCount;
                $parent['total_questions'] = $parent['question_count'];

                // Process each child category
                foreach ($children as $child) {
                    // Get question count for this child
                    $childQuestionSql = "SELECT COUNT(DISTINCT q.id) as count
                                   FROM questions q
                                   JOIN question_tags qt ON q.id = qt.question_id
                                   WHERE q.category_id = :category_id
                                   AND qt.tag_id = :tag_id";

                    $childQuestionStmt = $this->pdo->prepare($childQuestionSql);
                    $childQuestionStmt->execute([
                        ':category_id' => $child['id'],
                        ':tag_id' => $tagId
                    ]);

                    $questionCount = $childQuestionStmt->fetchColumn();
                    $child['question_count'] = (int)$questionCount;

                    // Only add children that have questions
                    if ($child['question_count'] > 0) {
                        $parent['children'][] = $child;
                        $parent['total_questions'] += $child['question_count'];
                    }
                }

                // Only add parent categories that have questions (directly or via children)
                if ($parent['total_questions'] > 0) {
                    $result[] = $parent;
                }
            }

            error_log("DEBUG: Final result has " . count($result) . " categories with questions for tag $tagId");

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting categories hierarchy for tag: " . $e->getMessage());
            return [];
        }
    }
    public function getChildCategories($parentId)
    {
        $sql = "SELECT c.id, c.name, c.slug, c.parent_id,
            (SELECT COUNT(*) FROM questions q WHERE q.category_id = c.id) as question_count
            FROM categories c
            WHERE c.parent_id = :parent_id
            ORDER BY c.name";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':parent_id' => $parentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting child categories: " . $e->getMessage());
            return [];
        }
    }

    public function getTopLevelCategories()
    {
        try {
            $sql = "SELECT id, name, slug, parent_id 
                FROM categories 
                WHERE parent_id = 0 OR parent_id IS NULL 
                ORDER BY name ASC";

            $stmt = $this->pdo->query($sql);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting top-level categories: " . $e->getMessage());
            return [];
        }
    }

    public function updateCategoryTagAssociationsWithChildren($tagId, $categoryIds)
    {
        try {
            $this->pdo->beginTransaction();

            // First, delete all existing associations for this tag
            $deleteSql = "DELETE FROM tag_categories WHERE tag_id = ?";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute([$tagId]);

            // Get all child category IDs for the selected parent categories
            $allCategoryIds = $categoryIds;

            foreach ($categoryIds as $parentId) {
                $childrenSql = "SELECT id FROM categories WHERE parent_id = ?";
                $childrenStmt = $this->pdo->prepare($childrenSql);
                $childrenStmt->execute([$parentId]);

                while ($row = $childrenStmt->fetch(PDO::FETCH_ASSOC)) {
                    $allCategoryIds[] = $row['id'];
                }
            }

            // Remove duplicates
            $allCategoryIds = array_unique($allCategoryIds);

            // Insert new associations
            if (!empty($allCategoryIds)) {
                $insertSql = "INSERT INTO tag_categories (tag_id, category_id) VALUES (?, ?)";
                $insertStmt = $this->pdo->prepare($insertSql);

                foreach ($allCategoryIds as $categoryId) {
                    $insertStmt->execute([$tagId, $categoryId]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error updating category-tag associations: " . $e->getMessage());
            return false;
        }
    }

    public function getCategoriesByTagId($tagId)
    {
        try {
            $sql = "SELECT category_id FROM tag_categories WHERE tag_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$tagId]);

            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting categories by tag ID: " . $e->getMessage());
            return [];
        }
    }
}
