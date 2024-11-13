<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use PDO;
use PDOException;

class ProgramController extends Controller
{
    public $categoryModel;
    public $programModel;
    public $quizModel;

    public function __construct(PDO $pdo)
    {
        $this->categoryModel = new CategoryModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
    }

    public function index()
    {
        $categories = $this->categoryModel->getTopCategories();
        $content = $this->render('admin/program/add', ['categories' => $categories]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['category_id'] ?? '';
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($categoryId)) {
                echo "category is required.";
                return;
            }
            if (empty($name)) {
                echo "Name is required.";
                return;
            }
        
           $result= $this->programModel->createProgram($categoryId, $name, $description,$slug);
            if ($result) {
                $_SESSION['message'] = 'Program created successfully.';
                $_SESSION['status'] = "success";
                header('Location: /admin/program/add');
            } else {
                $_SESSION['message'] = "Error adding Question";
                $_SESSION['status'] = "danger";
            }
        }


    }

    public function list()
    {
        $programs = $this->programModel->getWithCategory();
        $content = $this->render('admin/program/list', ['programs' => $programs]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
public function showTest()
{
    $programs = $this->programModel->getWithCategory();
    $quiz = $this->quizModel->getAll();

    $content = $this->uirender('user/test', [
        'programs' => $programs,
        'quizzes' => $quiz,

    ]);

    echo $this->uirender('user/layout', ['content' => $content]);

}

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = $_POST['category_id'] ?? '';
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            
            try {
                $this->programModel->updateProgram($id, $categoryId, $name, $description,$slug);
                $_SESSION['message'] = 'Program updated successfully.';
                $_SESSION['status'] = 'success';
                header('Location: /admin/program/edit/' . $id);
                exit;
            } catch (PDOException $e) {
                $_SESSION['message'] = 'Error updating program: ' . $e->getMessage();
                $_SESSION['status'] = 'danger';
            }
        }

        $program = $this->programModel->getById($id);
        $categories = $this->categoryModel->getTopCategories();
        
        $content = $this->render('admin/program/edit', [
            'program' => $program,
            'categories' => $categories
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        try {
            $this->programModel->deleteProgram($id);
            $_SESSION['message'] = 'Program deleted successfully.';
            $_SESSION['status'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Error deleting program: ' . $e->getMessage();
            $_SESSION['status'] = 'danger';
        }
        header('Location: /admin/program/list');
        exit;
    }
}