<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\TagModel;
use MVC\Models\QuizModel;
use MVC\Models\ProgramModel;
use MVC\Models\CategoryModel;
use MVC\Models\CategoryTypeModel;
use PHPMailer\PHPMailer\Exception;



class CategoryController extends Controller
{
    public $categoryModel;
    public $programModel;
    public $quizModel;
    public $tagModel;
    public $categoryTypeModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->categoryModel = new CategoryModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->tagModel = new TagModel($pdo);
        $this->categoryTypeModel = new CategoryTypeModel($pdo);
    }

    public function index()
    {
        $categories = $this->categoryModel->getCategoriesHierarchy();
        $categorytypes = $this->categoryTypeModel->getAll();
        $content = $this->render(
            'admin/category/view',
            [
                'categories' => $categories,
                'categorytypes' => $categorytypes,
            ]
        );
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $categories = $this->categoryModel->getAllCategoriesWithParent();
        $tags = $this->tagModel->getAllTags();
        $categorytypes = $this->categoryTypeModel->getAll();
        $content = $this->render('admin/category/add', [
            'categories' => $categories,
            'tags' => $tags,
            'categorytypes' => $categorytypes,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showCategory($slug)
    {
        $category = $this->categoryModel->getCategoryBySlug($slug);
        if (!$category) {
            // Handle case where category is not found
            echo "Category not found.";
            return;
        }
        // print_r($category);die;
        $quizzes = $this->categoryModel->getQuizzesByCategory($category['id']);
        $programs = $this->programModel->getWithCategory();
        $quiz = $this->quizModel->getAll();
        $content = $this->uirender('user/categories', [
            'category' => $category,
            'c_quizzes' => $quizzes,
            'quizzes' => $quiz,
            'programs' => $programs,
        ]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }

 public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $parentId = $_POST['parent_id'] ?? 0;
            $category_type_id = 2;
    
            // Add validation messages
            if (empty($name)) {
                $_SESSION['message'] = "Category name is required";
                $_SESSION['status'] = "danger";
                header('Location: /admin/category/add');
                exit;
            }
    
            $result = $this->categoryModel->createCategory($name, $slug, $parentId, $category_type_id);
    
            if ($result) {
                $_SESSION['message'] = "Category added successfully!";
                $_SESSION['status'] = "success";
            } else {
                $_SESSION['message'] = "Error adding category";
                $_SESSION['status'] = "danger";
            }
            header('Location: /admin/category/add');
            exit;
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            echo "Category not found.";
            return;
        }

        $categories = $this->categoryModel->getAllCategories();
        $categorytypes = $this->categoryTypeModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $parentId = $_POST['parent_id'] ?? 0;
            $category_type_id = 2;

       

            $result = $this->categoryModel->updateCategory($id, $name,$slug, $parentId,$category_type_id);

            if ($result) {
                $_SESSION['message'] = "Category edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/category/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating category.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/category/edit/' . $id);
                exit;
            }
        }

        $content = $this->render('admin/category/edit', [
            'category' => $category,
            'categories' => $categories,
            'categorytypes' => $categorytypes,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }


    public function delete($id)
    {
        // Check if category has children
        $categories = $this->categoryModel->getAllCategories();
        $hasChildren = false;
        foreach ($categories as $category) {
            if ($category['parent_id'] == $id) {
                $hasChildren = true;
                break;
            }
        }

        if ($hasChildren) {
            echo "Cannot delete category with subcategories.";
            return;
        }

        $result = $this->categoryModel->deleteCategory($id);

        if ($result) {
            $_SESSION['message'] = "Category deleted successfully!";
            $_SESSION['status'] = "success";
            header('Location: /admin/category/list');
            exit;
        } else {
            $_SESSION['message'] = "Error deleting category!";
            $_SESSION['status'] = "danger";
            header('Location: /admin/category/list');
        }
    }

    public function getCategoriesByTags()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            // Get tag IDs from the request
            $tagIds = $_POST['tag_ids'] ?? [];

            if (empty($tagIds)) {
                throw new \Exception('No tags selected');
            }

            // Get categories for these tags
            $categories = $this->categoryModel->getCategoriesByTagIds($tagIds);

            // Return as JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Add this method to allow selecting tags for a category

    public function manageTags($categoryId)
    {
        try {
            $category = $this->categoryModel->getCategoryById($categoryId);
            if (!$category) {
                $_SESSION['message'] = "Category not found";
                $_SESSION['status'] = "danger";
                header('Location: /admin/category/list');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tagIds = $_POST['tags'] ?? [];

                // Update the relationship
                $this->updateCategoryTagRelationship($categoryId, $tagIds);

                $_SESSION['message'] = "Tag associations updated successfully";
                $_SESSION['status'] = "success";
                header('Location: /admin/category/edit/' . $categoryId);
                exit;
            }

            // Get all tags
            $tags = $this->tagModel->getAllTags();

            // Get tags already associated with this category
            $associatedTags = $this->tagModel->getTagsByCategoryId($categoryId);
            $associatedTagIds = array_column($associatedTags, 'id');

            $content = $this->render('admin/category/manage-tags', [
                'category' => $category,
                'tags' => $tags,
                'associatedTagIds' => $associatedTagIds
            ]);

            echo $this->render('admin/layout', ['content' => $content]);
        } catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            $_SESSION['status'] = "danger";
            header('Location: /admin/category/list');
        }
    }

    private function updateCategoryTagRelationship($categoryId, $tagIds)
    {
        try {
            error_log("DEBUG - Updating tags for category $categoryId: " . json_encode($tagIds));

            // Start transaction if not already in one
            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
                $localTransaction = true;
            } else {
                $localTransaction = false;
            }

            // Delete existing relationships for this category
            $sql = "DELETE FROM tag_categories WHERE category_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $deleteResult = $stmt->execute([$categoryId]);

            error_log("DEBUG - Deleted existing tag relationships: " . ($deleteResult ? 'Success' : 'Failed'));

            // Insert new relationships
            $insertedCount = 0;
            if (!empty($tagIds)) {
                $insertSql = "INSERT INTO tag_categories (category_id, tag_id) VALUES (?, ?)";
                $insertStmt = $this->pdo->prepare($insertSql);

                foreach ($tagIds as $tagId) {
                    if (empty($tagId)) continue; // Skip empty values

                    $insertResult = $insertStmt->execute([$categoryId, $tagId]);
                    if ($insertResult) {
                        $insertedCount++;
                    } else {
                        error_log("DEBUG - Failed to insert tag relationship: category=$categoryId, tag=$tagId");
                    }
                }
            }

            error_log("DEBUG - Inserted $insertedCount new tag relationships");

            // Commit transaction if we started it
            if ($localTransaction) {
                $this->pdo->commit();
            }

            return true;
        } catch (\Exception $e) {
            error_log("ERROR - updateCategoryTagRelationship: " . $e->getMessage());

            // Rollback transaction if we started it
            if (isset($localTransaction) && $localTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
