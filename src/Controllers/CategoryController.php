<?php

namespace MVC\Controllers;

use MVC\Models\CategoryModel;
use MVC\Controller;
use PDO;



class CategoryController extends Controller
{
    public $categoryModel;

    public function __construct(PDO $pdo)
    {
        $this->categoryModel = new CategoryModel($pdo);
    }

    public function index()
    {
        $categories = $this->categoryModel->getCategoriesHierarchy();
        $content = $this->render('admin/category/view', ['categories' => $categories]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $categories = $this->categoryModel->getAllCategoriesWithParent();
        $content = $this->render('admin/category/add', ['categories' => $categories]);
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
    
        $quizzes = $this->categoryModel->getQuizzesByCategory($category['id']);
    
        $content = $this->render('user/categories', [
            'category' => $category,
            'quizzes' => $quizzes
        ]);
        echo $this->render('user/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $parentId = $_POST['parent_id'] ?? 0;

            if (empty($name)) {
                echo "Category name is required.";
                return;
            }

            $result = $this->categoryModel->createCategory($name,$slug, $parentId);

            if ($result) {
                $_SESSION['message'] = "Category added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/category/add');
            } else {
                $_SESSION['message'] = "Error adding category";
                $_SESSION['status'] = "danger";
            }
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $parentId = $_POST['parent_id'] ?? 0;

            if (empty($name)) {
                echo "Category name is required.";
                return;
            }
            if (empty($slug)) {
                echo "Category slug is required.";
                return;
            }

            if ($parentId == $id) {
                echo "A category cannot be its own parent.";
                return;
            }

            $result = $this->categoryModel->updateCategory($id, $name,$slug, $parentId);

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
            'categories' => $categories
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
}
