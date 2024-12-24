<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\User;
use MVC\Models\QuizModel;
use MVC\Models\StudentModel;
use MVC\Models\TeacherModel;
use MVC\Services\SmsService;
use MVC\Models\QuestionModel;
use MVC\Validators\Validator;
use MVC\Models\QuestionReportModel;

class AuthController extends Controller
{
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

    private $maxOtpAttempts = 3;
    private $otpExpiryMinutes = 10;
    private $smsService;
    private $recaptchaSecret = '6LfghJYqAAAAADD8dz4vZtxw2BjGraY8ler5seJ2';
    private $sitekey = '6LfghJYqAAAAAOrFMiLflio-cKyywWTXy6Ssr2xq';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->model = new User($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->teacherModel = new TeacherModel($pdo);
        $this->studentModel = new StudentModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->reportModel = new QuestionReportModel($pdo);
        $this->smsService = new SmsService(); // Initialize SMS service

    }


    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }
    
        try {
            // Validate input data
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'cpassword' => $_POST['cpassword'] ?? ''
            ];
    
            // First validate all data BEFORE doing anything else
            $this->validateRegistrationData($data);
    
            // Check if email/phone already exists
            if ($this->model->isEmailExists($data['email'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'Email already registered'
                ], 400);
            }
            
            if ($this->model->isPhoneExists($data['phone'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'Phone number already registered'
                ], 400);
            }
            $lastOtpTime = $this->model->getLastOtpTime($data['phone']);
            if ($lastOtpTime) {
                $cooldownMinutes = 2; // Set cooldown period
                $timeDiff = time() - strtotime($lastOtpTime);
                
                if ($timeDiff < ($cooldownMinutes * 60)) {
                    $waitTime = ceil(($cooldownMinutes * 60 - $timeDiff) / 60);
                    throw new \Exception("Please wait {$waitTime} minutes before requesting another OTP");
                }
            }
            // Create a temporary user record WITHOUT OTP
            $tempUserId = $this->model->insert([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'phone' => $data['phone'],
                'is_verified' => 0,
                'usertype_id' => self::STUDENT_TYPE
            ]);
    
            if (!$tempUserId) {
                throw new \Exception('Failed to create user record');
            }
    
            // Generate and send OTP only after user record is created
            $otp = $this->smsService->generateOTP();
            $result = $this->smsService->sendOTP($data['phone'], $otp);
    
            if ($result['status'] === 'error') {
                // Rollback user creation if OTP sending fails
                $this->model->delete($tempUserId);
                throw new \Exception($result['message']);
            }
    
            // Update user record with OTP details
            $this->model->update($tempUserId, [
                'otp' => $otp,
                'otp_attempts' => 0,
                'otp_expires' => date('Y-m-d H:i:s', time() + ($this->otpExpiryMinutes * 60)),
                'last_otp_sent' => date('Y-m-d H:i:s')
            ]);
    
            // Store minimal data in session
            $_SESSION['temp_registration'] = [
                'user_id' => $tempUserId,
                'phone' => $data['phone']
            ];
    
            return $this->jsonResponse([
                'success' => true,
                'message' => 'OTP sent successfully'
            ]);
    
        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
    public function verifyOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }
    
        try {
            if (!isset($_SESSION['temp_registration']['user_id'])) {
                throw new \Exception('Registration session expired');
            }
    
            $userId = $_SESSION['temp_registration']['user_id'];
            $user = $this->model->find($userId);
            
            if (!$user) {
                throw new \Exception('User not found');
            }
    
            $inputData = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid request format');
            }
    
            $otp = $inputData['otp'] ?? '';
            
            // Validate OTP
            if ($otp !== $user['otp']) {
                $this->model->incrementOtpAttempts($userId);
                throw new \Exception('Invalid OTP');
            }
    
            // Verify OTP expiry
            if (strtotime($user['otp_expires']) < time()) {
                throw new \Exception('OTP has expired');
            }
    
            // Mark user as verified
            $this->model->update($userId, [
                'is_verified' => 1,
                'otp' => null,
                'otp_attempts' => null,
                'otp_expires' => null
            ]);
    
            // Clear session
            unset($_SESSION['temp_registration']);
    
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Registration successful'
            ]);
    
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
    private function validateRecaptcha(?string $response): bool
    {
        if (empty($response)) {
            throw new \Exception('reCAPTCHA verification failed');
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->recaptchaSecret,
            'response' => $response
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $result = json_decode($response, true);
        curl_close($ch);

        if (!$result['success']) {
            throw new \Exception('reCAPTCHA verification failed');
        }

        return true;
    }

    private function validateOTP($otp): string
    {
        if (empty($otp) || !is_string($otp) || strlen($otp) !== 6 || !ctype_digit($otp)) {
            throw new \Exception('Invalid OTP format');
        }
        return $otp;
    }
    private function validateRegistrationData(array $data): void
    {
        $validator = new Validator($data, [
            'username' => ['required', 'min:3', 'alpha_space'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'phone:NP'],
            'password' => ['required', 'min:8'],
            'cpassword' => ['required', 'same:password']
        ]);

        if (!$validator->validate()) {
            throw new \Exception($validator->getFirstError());
        }
    }
    private function validateTempRegistration()
    {
        $tempData = $_SESSION['temp_registration'] ?? null;

        if (!$tempData) {
            throw new \Exception('Registration session expired');
        }

        if (time() > $tempData['expires']) {
            unset($_SESSION['temp_registration']);
            throw new \Exception('OTP expired');
        }

        if ($tempData['attempts'] >= $this->maxOtpAttempts) {
            unset($_SESSION['temp_registration']);
            throw new \Exception('Too many failed attempts');
        }

        return $tempData;
    }

    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    public function login()
    {
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

    public function userlogin()
    {
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
                $_SESSION['usertype_id'] = $user['usertype_id'];

                header('Location: /');
                exit();
            } else {
                $_SESSION['message'] = "Invalid credentials or unauthorized access.";
                $_SESSION['status'] = "danger";
            }
        }
    }

    public function showLoginForm()
    {
        include 'src/Views/admin/auth/login.php';
    }

    public function loginmodal()
    {
        $quizzes = $this->quizModel->getAll();
        $content = $this->uirender('user/auth/login', ['quizzes' => $quizzes]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }
    // Add a method to show the admin dashboard
    public function showDashboard()
    {
        $counts = [
            'teacher_count' => $this->teacherModel->getCount(),
            'student_count' => $this->studentModel->getCount(),
            'question_count' => $this->questionModel->getCount(),
            'report_count' => $this->reportModel->getCount()
        ];

        $dashboardContent = $this->render('admin/dashboard', ['counts' => $counts]);
        echo $this->render('admin/layout', ['content' => $dashboardContent]);
    }
    public function logout()
    {
        include 'src/Views/admin/auth/logout.php';
    }

    public function userLogout()
    {
        session_start();
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header('Location: /'); // Redirect to the homepage
        exit();
    }
}
