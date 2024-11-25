<?php
namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\QuizModel;
use MVC\Models\User;
use PDO;
use MVC\Models\TeacherModel;
use MVC\Models\StudentModel;
use MVC\Models\QuestionModel;
use MVC\Models\QuestionReportModel;

class AuthController extends Controller {
    // Add constants for user types
    const ADMIN_TYPE = 1;
    const TEACHER_TYPE = 2;
    const STUDENT_TYPE = 3;

    protected $model;
    public $quizModel;
    protected $pdo;
    private $teacherModel;
    private $studentModel;
    private $questionModel;
    private $reportModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->model = new User($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->teacherModel = new TeacherModel($pdo);
        $this->studentModel = new StudentModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->reportModel = new QuestionReportModel($pdo);
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
    
            if (empty($username) || empty($password)) {
                $_SESSION['message'] = "Username and Password are required.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/login');
                return;
            }
    
            $user = $this->model->validateUser($username, $password);
            
            if ($user) {
                // Check if user type is student (3)
                if ($user['usertype_id'] == self::STUDENT_TYPE) {
                    $_SESSION['message'] = "Students cannot access admin dashboard. Please use the main login.";
                    $_SESSION['status'] = "danger";
                    header('Location: /admin/login');
                    return;
                }
                
                // Only allow admin and teachers
                if ($user['usertype_id'] == self::ADMIN_TYPE || $user['usertype_id'] == self::TEACHER_TYPE) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $user['usertype_id'];
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: /admin');
                    exit();
                }
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
                $_SESSION['message'] = "Username and Password are required.";
                $_SESSION['status'] = "danger";
                return;
            }
    
            // Check credentials
            $user = $this->model->validateUser($username, $password);
            
            if ($user && $user['usertype_id'] == self::STUDENT_TYPE) {
                $redirectUrl = $_SESSION['redirect_after_login'] ?? '/';
                $_SESSION['name'] = $username;
                $_SESSION['user_id'] = $user['id'];
                header('Location: /');
                exit();
            } else {
                $_SESSION['message'] = "Invalid credentials or unauthorized access.";
                $_SESSION['status'] = "danger";
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
        $counts = [
            'teacher_count' => $this->teacherModel->getCount(),
            'student_count' => $this->studentModel->getCount(),
            'question_count' => $this->questionModel->getCount(),
            'report_count' => $this->reportModel->getCount()
        ];

        $dashboardContent = $this->render('admin/dashboard', ['counts' => $counts]);
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
