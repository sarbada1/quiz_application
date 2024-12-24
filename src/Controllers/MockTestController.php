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

    public function index($id)
    {
        $program = $this->programModel->getById($id);
        $mocktests = $this->mockTestModel->getByProgramId($id);

        $content = $this->render('admin/mocktest/view', [
            'program' => $program,
            'mocktests' => $mocktests,
        ]);
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
    public function showTestDetail($slug)
    {
        $programs = $this->programModel->getWithCategory();
        $quiz = $this->quizModel->getAll();
        $id = $this->programModel->getBySlug($slug);
        $mocktests = $this->mockTestModel->getAllMockTests($id['id']);
        $isLoggedIn =isset($_SESSION['user_id']) && isset($_SESSION['usertype_id']) 
        && $_SESSION['usertype_id'] == self::STUDENT_TYPE;
        $content = $this->uirender('user/test_info', [
            'programs' => $programs,
            'quizzes' => $quiz,
            'mocktests' => $mocktests,
            'isLoggedIn' => $isLoggedIn,

        ]);

        echo $this->uirender('user/layout', ['content' => $content]);
    }
public function register($mocktestId)
{
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Please log in to register for the exam.";
        $_SESSION['status'] = "danger";
        header('Location: /login');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];

    if ($userType != 3) { // Check if the user is a student
        $_SESSION['message'] = "Only students can register for exams.";
        $_SESSION['status'] = "danger";
        header('Location: /mocktest/' . $mocktestId);
        exit;
    }

    if ($this->mockTestModel->isUserRegistered($userId, $mocktestId)) {
        $_SESSION['message'] = "You are already registered for this exam.";
        $_SESSION['status'] = "info";
    } else {
        $availableSeats = $this->mockTestModel->getAvailableSeats($mocktestId);
        if ($availableSeats > 0) {
            $this->mockTestModel->registerUser($userId, $mocktestId);
            $_SESSION['message'] = "Successfully registered for the exam.";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "No available seats for this exam.";
            $_SESSION['status'] = "danger";
        }
    }

    header('Location: /mocktest/' . $mocktestId);
    exit;
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

            $result = $this->mockTestModel->createMockTest($id, $name, $time, $slug, $no_of_student,$exam_time,$date);

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

            $result = $this->mockTestModel->updateMockTest($id, $name, $time, $slug, $no_of_student,$exam_time,$date);

            if ($result) {
                $_SESSION['message'] = "Mock Test edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktest/edit/' . $mocktest['id']);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Mock Test.";
                $_SESSION['status'] = "danger";
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
    public function saveProgress() {
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
