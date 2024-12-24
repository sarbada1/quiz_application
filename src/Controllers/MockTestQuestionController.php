<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\MockTestAttemptModel;
use MVC\Models\MockTestModel;
use MVC\Models\MockTestQuestionModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuestionModel;
use MVC\Models\QuizModel;
use PDO;

class MockTestQuestionController extends Controller
{
    const ADMIN_TYPE = 1;
    const TEACHER_TYPE = 2;
    const STUDENT_TYPE = 3;

    private $mockTestModel;
    private $mockTestQuestionModel;
    private $questionModel;
    private $mockTestAttemptModel;
    public $programModel;
    public $quizModel;

    public function __construct(PDO $pdo)
    {
        $this->mockTestModel = new MockTestModel($pdo);
        $this->mockTestQuestionModel = new MockTestQuestionModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->mockTestAttemptModel = new MockTestAttemptModel($pdo);
    }


    public function index($mockTestId)
    {
        // Fetch the mock test and related program data
        $mockTest = $this->mockTestModel->getById($mockTestId);
        $program = $this->programModel->getById($mockTest['program_id']);

        // Fetch all questions with their answers for the specific mock test
        $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($mockTestId);

        // Check if the questions were retrieved and if the structure is correct
        if (!$questions || empty($questions)) {
            $_SESSION['message'] = "No questions found for this Mock Test.";
            $_SESSION['status'] = "warning";
        }

        // Pass the retrieved data to the view
        $content = $this->render('admin/mocktestquestion/view', [
            'mockTest' => $mockTest,
            'program' => $program,
            'questions' => $questions,  // Ensure the questions array is passed correctly
        ]);

        // Render the final layout
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm($mockTestId)
    {
        $mockTest = $this->mockTestModel->getById($mockTestId);
        $existingQuestions = $this->mockTestQuestionModel->getQuestionIdsByMockTestId($mockTestId);
        $quizzes = $this->quizModel->getAll();
        $questionTypes = $this->questionModel->getQuestionTypes();
        
        // Get filter parameters
        $quizId = $_GET['quiz_id'] ?? null;
        $questionType = $_GET['question_type'] ?? null;
        
        // Fetch filtered questions
        $allQuestions = $this->questionModel->getFilteredQuestions($quizId, $questionType);
        
        if (!$mockTest) {
            $_SESSION['message'] = "Mock Test not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/mocktest/list/' . $mockTest['program_id']);
            exit;
        }
        
        $content = $this->render('admin/mocktestquestion/add', [
            'mockTest' => $mockTest,
            'questions' => $allQuestions,
            'existingQuestions' => $existingQuestions,
            'quizzes' => $quizzes,
            'questionTypes' => $questionTypes,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function toggleQuestion($action, $questionId, $mockTestId)
    {
        if ($action === 'add') {
            // Add the question to the mock test
            $this->mockTestQuestionModel->createQuestion($mockTestId, $questionId);
        } elseif ($action === 'remove') {
            // Remove the question from the mock test
            $this->mockTestQuestionModel->deleteMockQuestion($mockTestId, $questionId);
        }
    }

    public function showMockTest($slug)
    {
        $programs = $this->programModel->getWithCategory();
        $quiz = $this->quizModel->getAll();
        $mockTest = $this->mockTestModel->getBySlug($slug);
        $mockTestId = $mockTest['id'];

        // Only initialize if not set (don't reset existing answers)
        if (!isset($_SESSION['answeredQuestions'][$mockTestId])) {
            $_SESSION['answeredQuestions'][$mockTestId] = [];
        }

        
        
        $isLoggedIn =isset($_SESSION['user_id']) && isset($_SESSION['usertype_id']) 
        && $_SESSION['usertype_id'] == self::STUDENT_TYPE;
        $_SESSION['current_mocktest_id'] = $mockTestId;
        $_SESSION['test_start_time'] = time();

        // Get questions without filtering by answered questions
        $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($mockTestId);

        // Reset score counters
        $this->resetTestSession();

        $content = $this->uirender('user/mocktest', [
            'programs' => $programs,
            'quizzes' => $quiz,
            'questions' => $questions,
            'mockTest' => $mockTest,
            'isLoggedIn' => $isLoggedIn,
            'answeredQuestions' => $_SESSION['answeredQuestions'][$mockTestId]
        ]);

        echo $this->uirender('user/testlayout', ['content' => $content]);
    }

    private function resetTestSession()
    {
        // Only reset score counters, not answered questions
        $_SESSION['correctAnswers'] = 0;
        $_SESSION['wrongAnswers'] = 0;
    }

    public function clearTestSession($mocktestId)
    {
        // Reset everything for fresh start
        $_SESSION['correctAnswers'] = 0;
        $_SESSION['wrongAnswers'] = 0;
        $_SESSION['answeredQuestions'][$mocktestId] = [];

        echo json_encode(['success' => true]);
    }


    public function checkAnswer($answerId, $questionId, $mockTestId)
    {
        // Initialize session counters if not set
        if (!isset($_SESSION['correctAnswers'])) {
            $_SESSION['correctAnswers'] = 0;
        }
        if (!isset($_SESSION['wrongAnswers'])) {
            $_SESSION['wrongAnswers'] = 0;
        }

        $isCorrect = $this->mockTestQuestionModel->checkAnswer($answerId, $questionId);

        if ($isCorrect) {
            $_SESSION['correctAnswers']++;
        } else {
            $_SESSION['wrongAnswers']++;
        }

        echo json_encode([
            'isCorrect' => $isCorrect,
            'correctAnswers' => $_SESSION['correctAnswers'],
            'wrongAnswers' => $_SESSION['wrongAnswers']
        ]);
    }


    public function submitPerformance()
    {
        try {
            // Validate session data
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['error' => 'User not logged in']);
                return;
            }

            if (!isset($_SESSION['current_mocktest_id'])) {
                echo json_encode(['error' => 'No active mock test found']);
                return;
            }

            // Get data from session
            $userId = $_SESSION['user_id'];
            $mockTestId = $_SESSION['current_mocktest_id'];
            $correctAnswers = $_SESSION['correctAnswers'] ?? 0;
            $wrongAnswers = $_SESSION['wrongAnswers'] ?? 0;

            // Get total questions
            $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($mockTestId);
            $totalQuestions = count($questions);

            // Calculate scores
            $unattempted = $totalQuestions - ($correctAnswers + $wrongAnswers);
            $score = ($correctAnswers / $totalQuestions) * 100;
            $timeTaken = isset($_SESSION['test_start_time']) ? time() - $_SESSION['test_start_time'] : 0;

            // Prepare attempt data
            $attemptData = [
                'user_id' => $userId,
                'mock_test_id' => $mockTestId,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'unattempted' => $unattempted,
                'score' => number_format($score, 2),
                'time_taken' => $timeTaken,
                'completion_status' => 'completed'
            ];

            // Save attempt
            $result = $this->mockTestAttemptModel->createAttempt($attemptData);

            if (!$result) {
                throw new \Exception('Failed to save attempt data');
            }

            // Return success response
            echo json_encode([
                'success' => true,
                'correctAnswers' => $correctAnswers,
                'wrongAnswers' => $wrongAnswers,
                'totalQuestions' => $totalQuestions,
                'score' => $score,
                'unattempted' => $unattempted
            ]);
        } catch (\Exception $e) {
            error_log('Error in submitPerformance: ' . $e->getMessage());
            echo json_encode(['error' => 'Failed to save attempt: ' . $e->getMessage()]);
        }
    }

    public function restartTest($slug)
    {
        // Reset all test-related session variables
        $this->resetTestSession();

        // Clear answered questions for this test
        $mockTest = $this->mockTestModel->getBySlug($slug);
        if ($mockTest) {
            $_SESSION['answeredQuestions'][$mockTest['id']] = [];
        }

        // Redirect to show the test again
        header("Location: /mocktest/" . $slug);
        exit;
    }

    

    public function delete($id)
    {
        $question = $this->mockTestQuestionModel->getById($id);

        if (!$question) {
            $_SESSION['message'] = "Question not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/mocktest/list');
            exit;
        }

        $result = $this->mockTestQuestionModel->deleteQuestion($id);

        if ($result) {
            $_SESSION['message'] = "Question deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Question.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/mocktestquestion/view/' . $question['programmes_mock_test_id']);
        exit;
    }
}
