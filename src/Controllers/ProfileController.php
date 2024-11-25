<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\User;
use MVC\Models\QuizModel;
use MVC\Models\ProgramModel;
use MVC\Models\CategoryModel;
use MVC\Models\UserInfoModel;
use MVC\Models\QuizAttemptModel;
use MVC\Models\MockTestAttemptModel;

class ProfileController extends Controller
{
    private $userModel;
    private $userinfoModel;
    public $quizModel;
    public $categoryModel;
    public $programModel;
    protected $pdo;
    private $quizAttemptModel;
    private $mockTestAttemptModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->userinfoModel = new UserInfoModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizAttemptModel = new QuizAttemptModel($pdo);
        $this->mockTestAttemptModel = new MockTestAttemptModel($pdo);
    }

    public function index()
    {
        $id = $_SESSION['user_id'];
        $user = $this->userModel->getById($id);
        $userinfo = $this->userinfoModel->getById($id);
        $categories = $this->categoryModel->getTopCategories($id);
        $quizzes = $this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();
        $quizHistory = $this->quizAttemptModel->getUserHistory($_SESSION['user_id']);
        $mocktestHistory = $this->mockTestAttemptModel->getUserHistory($_SESSION['user_id']);

        $content = $this->uirender('user/profile', [
            'user' => $user,
            'userinfo' => $userinfo,
            'categories' => $categories,
            'quizzes' => $quizzes,
            'programs' => $programs,
            'quizHistory' => $quizHistory,
            'mocktestHistory' => $mocktestHistory
        ]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function addUserInfo()
    {
        $id = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $age = $_POST['age'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $college = $_POST['college'] ?? '';
            $address = $_POST['address'] ?? '';

            // Update the user data
            $this->userModel->editUser($id, $username, $email);

            // Check if user info exists
            $existingUserInfo = $this->userinfoModel->getById($id);

            if ($existingUserInfo) {
                // Update existing user info
                $this->userinfoModel->updateUserInfo($age, $phone, $address, $college, $id);
            } else {
                // Create new user info
                $this->userinfoModel->createUserInfo($age, $phone, $address, $college, $id);
            }

            // Set success message and redirect
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['status'] = "success";
            header('Location: /profile');
            exit;
        }
    }
}
