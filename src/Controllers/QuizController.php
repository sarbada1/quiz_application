<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\LevelModel;
use MVC\Models\QuizModel;
use PDO;



class QuizController extends Controller
{
    private $quizModel;
    private $levelModel;
    private $categoryModel;


    public function __construct(PDO $pdo)
    {
        $this->quizModel = new QuizModel($pdo);
        $this->levelModel = new LevelModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
    }

    public function index()
    {
        $quizzes = $this->quizModel->getAll();
        $content = $this->render('admin/quiz/view', ['quizzes' => $quizzes]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showQuiz($slug)
    {
        $quiz = $this->quizModel->getQuizBySlug($slug);
        if (!$quiz) {
            // Handle case where category is not found
            echo "Category not found.";
            return;
        }
    
        $questions = $this->quizModel->getQuestionsByQuizId($quiz['id']);
        $levels = $this->levelModel->getAll();    
        $content = $this->render('user/quiz', [
            'questions' => $questions,
            'levels' => $levels
        ]);
        echo $this->render('user/layout', ['content' => $content]);
    }
    public function showAddForm()
    {
        $categories = $this->categoryModel->getAllCategories();
        $levels = $this->levelModel->getAll();
        $content = $this->render('admin/quiz/add', [
            'categories' => $categories,
            'levels' => $levels,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $user_id = $_SESSION['user_id'] ?? '';

            if (empty($title)) {
                echo "Title is required.";
                return;
            }
            if (empty($title)) {
                echo "Slug is required.";
                return;
            }
            if (empty($description)) {
                echo "Description is required.";
                return;
            }
         

            $result = $this->quizModel->createQuiz($title, $slug,$description, $category_id,$user_id);

            if ($result) {
                $_SESSION['message'] = "Quiz added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/quiz/add');
            } else {
                $_SESSION['message'] = "Error adding Quiz";
                $_SESSION['status'] = "danger";
            }
        }
    }

    public function edit($id)
    {
        $category = $this->quizModel->getById($id);
        if (!$category) {
            echo "Quiz not found.";
            return;
        }
        $categories = $this->categoryModel->getAllCategories();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $user_id = $_SESSION['user_id'] ?? '';


            if (empty($title)) {
                echo "Title is required.";
                return;
            }
            if (empty($description)) {
                echo "Description is required.";
                return;
            }

            $result = $this->quizModel->updateQuiz($id,$title,$slug, $description, $category_id,$user_id);

            if ($result) {
                $_SESSION['message'] = "Quiz edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/quiz/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Quiz.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/quiz/edit/' . $id);
                exit;
            }
        }

        $content = $this->render('admin/quiz/edit', [
            'category' => $category,
            'categories' => $categories,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $result = $this->quizModel->deleteQuiz($id);

        if ($result) {
            $_SESSION['message'] = "Quiz deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Quiz.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/quiz/list');
        exit;
    }
}
