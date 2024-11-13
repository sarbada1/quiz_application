<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\LevelModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use PDO;

session_start();

class QuizController extends Controller
{
    public $quizModel;
    public $levelModel;
    public $categoryModel;
    public $programModel;


    public function __construct(PDO $pdo)
    {
        $this->quizModel = new QuizModel($pdo);
        $this->levelModel = new LevelModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->programModel = new ProgramModel($pdo);
    }

    public function index()
    {
        $quizzes = $this->quizModel->getAll();
        $content = $this->render('admin/quiz/view', ['quizzes' => $quizzes]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showQuizDetail($slug)
    {
        $quiz = $this->quizModel->getQuizQuestionBySlug($slug);
        $categories = $this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();


        if (!$quiz) {
            header('location:/404.php');
            echo "Quiz not found";
            return;
        }

        $isLoggedIn = isset($_SESSION['user_id']);

        $content = $this->uirender('user/quiz_info', [
            'quiz' => $quiz,
            'isLoggedIn' => $isLoggedIn,
            'quizzes' => $categories,
            'programs' => $programs,
        ]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function startQuiz($slug)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $quiz = $this->quizModel->getQuizQuestionBySlug($slug);
        if (!$quiz) {
            header('location:/404.php');
            echo "Quiz not found";
            return;
        }
        $question = $this->quizModel->getQuestionsByQuizId($quiz['id']);
    

        $content = $this->uirender('user/question', [
            'questions' => $question
        ]);

        echo $this->uirender('user/layout', ['content' => $content]);

    }
    public function showQuiz()
    {
        $quiz = $this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();


        $content = $this->uirender('user/quiz', [
            'quiz' => $quiz,
            'quizzes' => $quiz,
            'programs' => $programs,
        ]);

        echo $this->uirender('user/layout', ['content' => $content]);
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
            $difficulty_level = $_POST['difficulty_level'] ?? '';
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


            $result = $this->quizModel->createQuiz($title, $slug, $description, $category_id, $user_id, $difficulty_level);

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
            $difficulty_level = $_POST['difficulty_level'] ?? '';
            $user_id = $_SESSION['user_id'] ?? '';


            if (empty($title)) {
                echo "Title is required.";
                return;
            }
            if (empty($description)) {
                echo "Description is required.";
                return;
            }

            $result = $this->quizModel->updateQuiz($id, $title, $slug, $description, $category_id, $user_id, $difficulty_level);

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
        $levels = $this->levelModel->getAll();

        $content = $this->render('admin/quiz/edit', [
            'category' => $category,
            'categories' => $categories,
            'levels' => $levels,
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
