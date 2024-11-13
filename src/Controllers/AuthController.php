<?php
namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\QuizModel;
use MVC\Models\User;
use PDO;

class AuthController extends Controller {
    protected $model;
    public $quizModel;
    protected $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->model = new User($pdo);
        $this->quizModel = new QuizModel($pdo);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['cpassword'] ?? '';

            // Basic validation
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                // Handle error, e.g., display an error message
                echo "All fields are required.";
                return;
            }

            if ($password !== $confirmPassword) {
                // Handle error, e.g., display an error message
                echo "Passwords do not match.";
                return;
            }

            // Password hashing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL statement
            $user = new User($this->pdo);
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'usertype_id' => 3
            ];

            $result = $user->insert($data);

            if ($result) {
                $_SESSION['message'] = "Registered successfully!";
                $_SESSION['status'] = "success";
                header('Location: /');
            } else {
                // Handle error, e.g., display an error message or redirect with error message
                return $this->render('/', ['error' => 'Error adding teacher.']);
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
    
            // Validate and sanitize inputs
            if (empty($username) || empty($password)) {
                echo "Username and Password are required.";
                return;
            }
    
            // Check credentials
            $user = $this->model->validateUser($username, $password);
            if ($user) {
                // Start a session and set user data
                
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['usertype_id'];  // Set the role based on usertype_id
                $_SESSION['user_id'] = $user['id'];  
                header('Location: /admin'); // Redirect to admin dashboard or another page
                exit();
            } else {
                $_SESSION['message'] = "Invalid username or password.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/login');
            }
        }
    }
    public function userlogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

            // Validate and sanitize inputs
            if (empty($username) || empty($password)) {
                echo "Username and Password are required.";
                return;
            }
    
            // Check credentials
            $user = $this->model->validateUser($username, $password);
            if ($user) {
                // Start a session and set user data
                $redirectUrl = $_SESSION['redirect_after_login'] ?? '/'; // Default to '/' if no redirect URL is set
                $_SESSION['name'] = $username;
                $_SESSION['user_id'] = $user['id'];  
                header('Location: /'); // Redirect to admin dashboard or another page
                exit();
            } else {
                echo "Invalid username or password.";
            }
        }
    }

    public function showLoginForm() {
        include 'src/Views/admin/auth/login.php';
    }
    
    public function loginmodal()
    {
        $quizzes=$this->quizModel->getAll();
        $content = $this->uirender('user/auth/login',['quizzes'=>$quizzes]);
        echo $this->uirender('user/layout', ['content' => $content]);

    }
    // Add a method to show the admin dashboard
    public function showDashboard() {
        $dashboardContent = $this->render('admin/dashboard');
        echo $this->render('admin/layout', ['content' => $dashboardContent]);
    }
    public function logout() {
        include 'src/Views/admin/auth/logout.php';
    }

    public function userLogout() {
        session_start();
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header('Location: /'); // Redirect to the homepage
        exit();
    }
    
}
