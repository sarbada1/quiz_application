<?php

namespace MVC\Models;

use PDO;
use PDOException;

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

// Add these methods to TagModel

/**
 * Get tags associated with a specific category
 */
public function getTagsByCategoryId($categoryId)
{
    try {
        $sql = "SELECT t.* 
                FROM tags t
                JOIN tag_categories tc ON t.id = tc.tag_id
                WHERE tc.category_id = :category_id
                ORDER BY t.name";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting tags for category: " . $e->getMessage());
        return [];
    }
}

/**
 * Get count of categories for each tag
 */
public function getTagsWithCategoryCounts()
{
    $sql = "SELECT t.id, t.name, t.slug, COUNT(tc.category_id) as category_count
            FROM tags t
            LEFT JOIN tag_categories tc ON t.id = tc.tag_id
            GROUP BY t.id
            ORDER BY t.name";
    
    try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tags with category counts: " . $e->getMessage());
        return [];
    }
}

/**
 * Get tags that have questions associated with them
 */
public function getTagsWithQuestions()
{
    try {
        $sql = "SELECT DISTINCT t.id, t.name, t.slug, 
                COUNT(DISTINCT q.id) as question_count
                FROM tags t
                JOIN question_tags qt ON t.id = qt.tag_id
                JOIN questions q ON qt.question_id = q.id
                GROUP BY t.id
                HAVING COUNT(DISTINCT q.id) > 0
                ORDER BY t.name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting tags with questions: " . $e->getMessage());
        return [];
    }
}

/**
 * Get tag by slug
 */
public function getBySlug($slug)
{
    try {
        $sql = "SELECT * FROM tags WHERE slug = :slug";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting tag by slug: " . $e->getMessage());
        return null;
    }
}
}