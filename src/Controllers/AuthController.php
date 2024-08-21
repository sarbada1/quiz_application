<?php
namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\User;
use PDO;

class AuthController extends Controller {
    protected $model;

    public function __construct(PDO $pdo) {
        $this->model = new User($pdo);
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
                echo "Invalid username or password.";
            }
        }
    }

    public function showLoginForm() {
        include 'src/Views/admin/auth/login.php';
    }
    
    // Add a method to show the admin dashboard
    public function showDashboard() {
        $dashboardContent = $this->render('admin/dashboard');
        echo $this->render('admin/layout', ['content' => $dashboardContent]);
    }
    public function logout() {
        include 'src/Views/admin/auth/logout.php';

    }
}
