<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\MockTestAttemptModel;
use MVC\Models\MockTestModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use PDO;

class MockTestController extends Controller
{

    const ADMIN_TYPE = 1;
    const TEACHER_TYPE = 2;
    const STUDENT_TYPE = 3;

    public $programModel;
    public $quizModel;
    private $mockTestModel;
    private $mockTestAttemptModel;

    public function __construct(PDO $pdo)
    {
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->mockTestModel = new MockTestModel($pdo);
        $this->mockTestAttemptModel = new MockTestAttemptModel($pdo);
    }

    public function index($id = null)
    {
        if (!$id) {
            // Get all programs if no ID provided
            $programs = $this->programModel->getAll();
            $content = $this->render('admin/mocktest/list', [
                'programs' => $programs
            ]);
        } else {
            // Get specific program's mock tests
            $program = $this->programModel->getById($id);
            $mocktests = $this->mockTestModel->getByProgramId($id);
    
            if (!$program) {
                $_SESSION['message'] = "Program not found";
                $_SESSION['status'] = "error";
                header('Location: /admin/program/list');
                exit;
            }
    
            $content = $this->render('admin/mocktest/view', [
                'program' => $program,
                'mocktests' => $mocktests,
            ]);
        }
        
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm($id)
    {
        $program = $this->programModel->getById($id);

        if (!$program) {
            $_SESSION['message'] = "Program not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }

        $content = $this->render('admin/mocktest/add', [
            'program' => $program,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showTestDetail($slug) {
        $programs = $this->programModel->getWithCategory();
        $quiz = $this->quizModel->getAll();
        $mocktest = $this->mockTestModel->getBySlug($slug);

        // print_r($mocktest);
        
        // Get sets if they exist
        $sets = [];
        if ($mocktest) {
            $sets = $this->quizModel->getSets($mocktest['id']);
        }
        // print_r($sets);

        $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['usertype_id']);
    
        $content = $this->uirender('user/test_info', [
            'programs' => $programs,
            'quizzes' => $quiz, 
            'mocktest' => $mocktest,
            'sets' => $sets,
            'isLoggedIn' => $isLoggedIn,
        ]);
    
        echo $this->uirender('user/layout', ['content' => $content]);
    }
    public function mocktestRegister()
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $mockTestId = $data['mockTestId'];
            $userId = $data['userId'];

            // Check if already registered
            if ($this->mockTestModel->isUserRegistered($userId, $mockTestId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You are already registered for this exam'
                ]);
                return;
            }

            // Register the user
            $result = $this->mockTestModel->registerUser($userId, $mockTestId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully registered for exam'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Registration failed'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function add($id)
    {
        $program = $this->programModel->getById($id);

        if (!$program) {
            $_SESSION['message'] = "Program not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $time = $_POST['time'] ?? '';
            $no_of_student = $_POST['no_of_student'] ?? '';
            $exam_time = $_POST['exam_time'] ?? '';
            $date = $_POST['date'] ?? '';

            $result = $this->mockTestModel->createMockTest($id, $name, $time, $slug, $no_of_student, $exam_time, $date);

            if ($result) {
                $_SESSION['message'] = "Mock Test added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktest/add/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error adding Mock Test.";
                $_SESSION['status'] = "danger";
            }
        }

        $content = $this->render('admin/mocktest/add', [
            'program' => $program,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function edit($id)
    {
        $mocktest = $this->mockTestModel->getById($id);

        if (!$mocktest) {
            $_SESSION['message'] = "Mock Test not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $time = $_POST['time'] ?? '';
            $no_of_student = $_POST['no_of_student'] ?? '';
            $exam_time = $_POST['exam_time'] ?? '';
            $date = $_POST['date'] ?? '';

            $result = $this->mockTestModel->updateMockTest($id, $name, $time, $slug, $no_of_student, $exam_time, $date);

            if ($result) {
                $_SESSION['message'] = "Mock Test edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktest/edit/' . $mocktest['id']);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Mock Test.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/mocktest/edit/' . $mocktest['id']);
            }
        }

        $content = $this->render('admin/mocktest/edit', [
            'mocktest' => $mocktest,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $mocktest = $this->mockTestModel->getById($id);

        if (!$mocktest) {
            $_SESSION['message'] = "Mock Test not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }

        $result = $this->mockTestModel->deleteMockTest($id);

        if ($result) {
            $_SESSION['message'] = "Mock Test deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Mock Test.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/mocktest/list/' . $mocktest['program_id']);
        exit;
    }
    public function showAttempts()
    {
        $attempts = $this->mockTestAttemptModel->getAllAttempts();

        $content = $this->render('admin/mocktest/attempts', [
            'attempts' => $attempts
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function checkStudentLimit($mockTestId)
    {
        try {
            $sql = "SELECT pmt.no_of_student, COUNT(DISTINCT mta.user_id) as current_students
                FROM programmes_mock_test pmt
                LEFT JOIN mock_test_attempts mta ON pmt.id = mta.mock_test_id 
                WHERE pmt.id = :mock_test_id
                GROUP BY pmt.id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['mock_test_id' => $mockTestId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['current_students'] < $result['no_of_student'];
        } catch (\PDOException $e) {
            error_log("Error checking student limit: " . $e->getMessage());
            return false;
        }
    }
    public function saveProgress()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO saved_mock_test_progress 
                (user_id, mock_test_id, progress_data, remaining_time)
                VALUES (:user_id, :mock_test_id, :progress_data, :remaining_time)
                ON DUPLICATE KEY UPDATE 
                progress_data = :progress_data,
                remaining_time = :remaining_time
            ");

            return $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'mock_test_id' => $data['mockTestId'],
                'progress_data' => json_encode($data['answers']),
                'remaining_time' => $data['timeLeft']
            ]);
        } catch (\PDOException $e) {
            error_log("Error saving progress: " . $e->getMessage());
            return false;
        }
    }
}
