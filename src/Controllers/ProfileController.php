<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use MVC\Models\User;
use MVC\Models\UserInfoModel;
use PDO;

class ProfileController extends Controller
{
    private $userModel;
    private $userinfoModel;
    public $quizModel;
    public $categoryModel;
    public $programModel;
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
        $this->userinfoModel = new UserInfoModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->programModel = new ProgramModel($pdo);
    }

    public function index()
    {
        $id = $_SESSION['user_id'];
        $user = $this->userModel->getById($id);
        $userinfo = $this->userinfoModel->getById($id);
        $categories = $this->categoryModel->getTopCategories($id);
        $quizzes=$this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();
        $content = $this->uirender('user/profile', ['user' => $user, 
        'userinfo' => $userinfo,
        'categories'=>$categories,
        'quizzes'=>$quizzes,
        'programs'=>$programs,
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
            $this->userModel->editUser($id, $username,$email);

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
