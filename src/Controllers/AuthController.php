<?php

namespace MVC\Controllers;

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
use PDO;
use Exception;
use MVC\Controller;
use MVC\Models\User;
use MVC\Models\QuizModel;
use MVC\Models\StudentModel;
use MVC\Models\TeacherModel;
use MVC\Services\SmsService;
use MVC\Models\QuestionModel;
use MVC\Validators\Validator;
use MVC\Models\ActivityLogModel;
use PHPMailer\PHPMailer\PHPMailer;
use MVC\Models\QuestionReportModel;
use MVC\Models\MockTestAttemptModel;

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
    private $activityLogModel;
    private $mockTestAttemptModel;

    private $maxOtpAttempts = 3;
    private $otpExpiryMinutes = 5;
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
        $this->mockTestAttemptModel = new MockTestAttemptModel($pdo);
        $this->reportModel = new QuestionReportModel($pdo);
        $this->activityLogModel = new ActivityLogModel($pdo);
        $this->smsService = new SmsService();
    }


public function register()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->jsonResponse(['error' => 'Invalid request method'], 405);
    }

    try {
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'cpassword' => $_POST['cpassword'] ?? ''
        ];

        // Validate data
        $this->validateRegistrationData($data);

        // Check existing email/phone
        if ($this->model->isEmailExists($data['email'])) {
            throw new \Exception('Email already registered');
        }
        if ($this->model->isPhoneExists($data['phone'])) {
            throw new \Exception('Phone number already registered');
        }

        // Generate OTP
        $otp = $this->smsService->generateOTP();
        
        // Store user data in session with OTP
        $_SESSION['temp_registration'] = [
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'otp' => $otp,
            'expires' => time() + ($this->otpExpiryMinutes * 60),
            'attempts' => 0
        ];

        // Send OTP via email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sanjelsarbada12@gmail.com';
            $mail->Password = 'xvxgdbfxlqpxehip';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('sanjelsarbada12@gmail.com', 'Tu Entrance');
            $mail->addAddress($data['email'], $data['username']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code for Registration';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;'>
                    <h2 style='color: #4a4a4a;'>Welcome to TU Entrance!</h2>
                    <p>Hello {$data['username']},</p>
                    <p>Thank you for registering with us. To complete your registration, please use the following verification code:</p>
                    <div style='background-color: #f2f2f2; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                        {$otp}
                    </div>
                    <p>This code will expire in {$this->otpExpiryMinutes} minutes.</p>
                    <p>If you didn't request this code, please ignore this email.</p>
                    <p>Best regards,<br>TU Entrance Team</p>
                </div>
            ";

            $mail->send();
            
            // Log OTP for debugging
            error_log("OTP sent to {$data['email']}: $otp");

            return $this->jsonResponse([
                'success' => true,
                'message' => 'OTP sent successfully to your email'
            ]);
            
        } catch (Exception $e) {
            error_log("Email failed: " . $mail->ErrorInfo);
            throw new \Exception("Failed to send verification code. Please try again.");
        }

    } catch (\Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return $this->jsonResponse([
            'success' => false,
            'error' => $e->getMessage()
        ], 400);
    }
}

public function verifyOTP()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->jsonResponse(['error' => 'Invalid request method'], 405);
    }

    try {
        if (!isset($_SESSION['temp_registration'])) {
            throw new \Exception('Registration session expired. Please try registering again.');
        }

        $data = $_SESSION['temp_registration'];

        // Verify OTP expiry
        if (time() > $data['expires']) {
            unset($_SESSION['temp_registration']);
            throw new \Exception('Verification code has expired. Please request a new one.');
        }

        // Get OTP from request body
        $inputData = json_decode(file_get_contents('php://input'), true);
        $submittedOTP = $inputData['otp'] ?? '';

        // Track attempts
        $_SESSION['temp_registration']['attempts'] += 1;
        
        // Check max attempts
        if ($_SESSION['temp_registration']['attempts'] > $this->maxOtpAttempts) {
            unset($_SESSION['temp_registration']);
            throw new \Exception('Too many incorrect attempts. Please register again.');
        }

        // Compare OTPs
        if ($submittedOTP !== $data['otp']) {
            throw new \Exception('Invalid verification code. Please try again.');
        }

        // Create user account
        $userId = $this->model->insert([
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'], // Already hashed in register method
            'is_verified' => 1,
            'usertype_id' => self::STUDENT_TYPE
        ]);

        if (!$userId) {
            throw new \Exception('Failed to create account. Please try again.');
        }

        // Log activity
        $this->activityLogModel->log(
            $userId,
            'student_register',
            'New student registration',
            '👤'
        );

        // Clear session
        unset($_SESSION['temp_registration']);

        // Set user session
        $_SESSION['user_id'] = $userId;
        $_SESSION['name'] = $data['username'];
        $_SESSION['usertype_id'] = self::STUDENT_TYPE;

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Registration successful'
        ]);
    } catch (\Exception $e) {
        error_log("OTP verification error: " . $e->getMessage());
        return $this->jsonResponse([
            'success' => false,
            'error' => $e->getMessage()
        ], 400);
    }
}



    public function sendOTP($data)
    {
        try {
            $otp = rand(100000, 999999);

            // Send OTP via email
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;
                $mail->Username = 'sanjelsarbada12@gmail.com'; // SMTP username
                $mail->Password = 'xvxgdbfxlqpxehip'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('sanjelsarbada12@gmail.com', 'Tu Entrance');
                $mail->addAddress($data['email'], $data['username']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "Your OTP code is: <b>$otp</b>";

                $mail->send();
            } catch (Exception $e) {
                throw new \Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }

            // Send OTP via SMS (commented out)
            $result = $this->smsService->sendOTP($data['phone'], $otp);
            if ($result['status'] === 'error') {
                throw new \Exception($result['message']);
            }

            // Store data in session
            $_SESSION['temp_registration'] = [
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'otp' => $otp,
                'expires' => time() + ($this->otpExpiryMinutes * 60)
            ];


            return $this->jsonResponse([
                'success' => true,
                'message' => 'OTP sent successfully'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function resendOTP()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }
    
        try {
            if (!isset($_SESSION['temp_registration'])) {
                throw new \Exception('Registration session expired');
            }
    
            $data = $_SESSION['temp_registration'];
            
            // Generate new OTP
            $otp = $this->smsService->generateOTP();
            
            // Send OTP via SMS
            $result = $this->smsService->sendOTP($data['phone'], $otp);
            if ($result['status'] === 'error') {
                throw new \Exception($result['message']);
            }
            
            // Update session with new OTP and reset expiry time
            $_SESSION['temp_registration']['otp'] = $otp;
            $_SESSION['temp_registration']['expires'] = time() + ($this->otpExpiryMinutes * 60);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'OTP resent successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
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
    // private function validateRegistrationData($data)
    // {
    //     if (empty($data['username']) || empty($data['email']) || empty($data['phone']) || empty($data['password']) || empty($data['cpassword'])) {
    //         throw new \Exception('All fields are required');
    //     }

    //     if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    //         throw new \Exception('Invalid email format');
    //     }

    //     if ($data['password'] !== $data['cpassword']) {
    //         throw new \Exception('Passwords do not match');
    //     }

    //     // Add more validation as needed
    // }
    private function jsonResponse($data, $status = 200)
    {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Invalid request method'], 405);
        }

        try {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                throw new \Exception('Username and Password are required');
            }

            $user = $this->model->validateUser($username, $password, self::STUDENT_TYPE); // Keep student type check    
            if (!$user) {
                throw new \Exception('Invalid username or password');
            }

            if ($user['usertype_id'] != self::STUDENT_TYPE) {
                throw new \Exception('Unauthorized access');
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['username'];
            $_SESSION['usertype_id'] = $user['usertype_id'];

            return $this->jsonResponse([
                'success' => true,
                'redirect' => '/'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
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
        $recentActivities = $this->getRecentActivities();

        // Get test performance stats 
        $stats = $this->getTestPerformanceStats();

        $dashboardContent = $this->render('admin/dashboard', [
            'counts' => $counts,
            'recentActivities' => $recentActivities,
            'stats' => $stats
        ]);

        echo $this->render('admin/layout', ['content' => $dashboardContent]);
    }
    private function getRecentActivities()
    {
        return $this->activityLogModel->getRecentActivities();
    }
    private function getTestPerformanceStats()
    {
        $stats = $this->mockTestAttemptModel->getOverallStats();
        return [
            'avg_score' => number_format($stats['avg_score'] ?? 0, 1),
            'tests_taken' => $stats['total_attempts'] ?? 0,
            'pass_rate' => number_format($stats['pass_rate'] ?? 0, 1)
        ];
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
