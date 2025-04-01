<?php

namespace MVC\Controllers;

use PDO;
use Exception;
use MVC\Controller;
use MVC\Models\ActivityLogModel;
use MVC\Models\QuizModel;
use MVC\Models\ProgramModel;
use MVC\Models\CategoryModel;
use MVC\Models\MockTestModel;
use MVC\Models\QuestionModel;
use MVC\Models\SubjectTestModel;
use MVC\Models\MockTestAttemptModel;
use MVC\Models\MockTestQuestionModel;

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
    public $categoryModel; // Add this property
    public $subjectTestModel;
    public $activityLogModel;

    public function __construct(PDO $pdo)
    {
        $this->mockTestModel = new MockTestModel($pdo);
        $this->mockTestQuestionModel = new MockTestQuestionModel($pdo);
        $this->subjectTestModel = new SubjectTestModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->mockTestAttemptModel = new MockTestAttemptModel($pdo);
        $this->activityLogModel = new ActivityLogModel($pdo);
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

    public function showAddForm($quizId)
    {
        $quiz = $this->quizModel->getById($quizId);
        $existingQuestions = $this->mockTestQuestionModel->getQuestionIdsByMockTestId($quizId);
        $categories = $this->categoryModel->getCategoryByQuiz($quizId);
        $categoryId = $_GET['category_id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $questionsPerPage = 10;
        
        // Get category allocations (questions count and marks)
        $categoryAllocations = $this->quizModel->getCategoryAllocations($quizId);
       
        // Count existing questions by category
        $existingQuestionsByCategory = $this->mockTestQuestionModel->getQuestionCountByCategory($quizId);
        // print_r($existingQuestionsByCategory);
        // die;
        $allQuestions = $this->questionModel->getFilteredQuestionsByCategoryAndQuiz($quizId, $page, $questionsPerPage, $categoryId);
        $totalQuestions = $this->questionModel->getTotalQuestionsByCategoryAndQuiz($quizId, $categoryId);
        $totalPages = ceil($totalQuestions / $questionsPerPage);
    
        $content = $this->render('admin/mocktestquestion/add', [
            'quiz' => $quiz,
            'questions' => $allQuestions,
            'existingQuestions' => $existingQuestions,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'categoryAllocations' => $categoryAllocations,
            'existingQuestionsByCategory' => $existingQuestionsByCategory
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showMockTest($setId)
    {
        try {
            // Get set details
            $set = $this->quizModel->getSetById($setId);
            if (!$set) {
                throw new Exception('Set not found');
            }

            // Get quiz details
            $quiz = $this->quizModel->getById($set['quiz_id']);
            if (!$quiz) {
                throw new Exception('Quiz not found');
            }

            // Get programs for navigation
            $programs = $this->programModel->getWithCategory();

            // Get category configurations for this quiz
            $categoryConfigs = $this->quizModel->getQuizCategories($quiz['id']);
            if (empty($categoryConfigs)) {
                throw new Exception('No categories configured for this quiz');
            }

            // Generate questions based on configurations
            $questions = [];
            $totalMarks = 0;

            foreach ($categoryConfigs as $config) {
                $categoryQuestions = $this->questionModel->getRandomQuestionsByCategory(
                    $config['category_id'],
                    $config['number_of_questions']
                );

                foreach ($categoryQuestions as $question) {
                    $answers = $this->mockTestQuestionModel->getAnswers($question['id']);
                    $questions[] = [
                        'id' => $question['id'],
                        'question_text' => $question['question_text'],
                        'category_id' => $config['category_id'],
                        'category_name' => $config['name'],
                        'marks' => $config['marks_allocated'] / $config['number_of_questions'],
                        'answers' => $answers
                    ];
                    $totalMarks += $config['marks_allocated'] / $config['number_of_questions'];
                }
            }

            // Session setup
            $_SESSION['current_set_id'] = $setId;
            $_SESSION['current_quiz_id'] = $quiz['id'];
            $_SESSION['test_start_time'] = time();
            $_SESSION['total_marks'] = $totalMarks;
            $this->resetTestSession();

            $isLoggedIn = isset($_SESSION['user_id']) &&
                isset($_SESSION['usertype_id']) &&
                $_SESSION['usertype_id'] == self::STUDENT_TYPE;

            $groupedQuestions = [];
            foreach ($questions as $question) {
                $categoryId = $question['category_id'];
                if (!isset($groupedQuestions[$categoryId])) {
                    $groupedQuestions[$categoryId] = [
                        'name' => $question['category_name'],
                        'questions' => []
                    ];
                }
                $groupedQuestions[$categoryId]['questions'][] = $question;
            }

            $_SESSION['grouped_questions'] = $groupedQuestions;

            // Render view 
            $content = $this->uirender('user/mocktest', [
                'quiz' => $quiz,
                'set' => $set,
                'questions' => $questions,
                'groupedQuestions' => $groupedQuestions,
                'programs' => $programs,
                'isLoggedIn' => $isLoggedIn,
                'totalMarks' => $totalMarks
            ]);

            echo $this->uirender('user/testlayout', ['content' => $content]);
        } catch (Exception $e) {
            error_log("Error in showMockTest: " . $e->getMessage());
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
            header('Location: /mocktests');
            exit;
        }
    }
    public function submitTest()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $setId = $data['mockTestId'] ?? null;

            if (!$setId) {
                throw new Exception('Invalid test submission');
            }

            // Get all submitted answers from session
            $answers = $_SESSION['test_answers'] ?? [];
            $totalMarks = 0;
            $obtainedMarks = 0;
            $correctAnswers = 0;
            $wrongAnswers = 0;
            $totalQuestions = 0;
            $attemptedQuestions = count($answers);
            $groupedQuestions = $_SESSION['grouped_questions'] ?? [];
            $totalQuestions = 0;
            foreach ($groupedQuestions as $category) {
                $totalQuestions += count($category['questions']);
            }

            // Create attempt record
            $attemptId = $this->mockTestAttemptModel->createAttempt([
                'user_id' => $_SESSION['user_id'],
                'set_id' => $setId,
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'attempted_questions' => $attemptedQuestions,
                'total_questions' => $totalQuestions
            ]);

            foreach ($answers as $questionId => $answerId) {
                $question = $this->questionModel->getById((int)$questionId);
                $answer = $this->mockTestQuestionModel->getAnswerById((int)$answerId);

                $isCorrect = $answer['isCorrect'] ?? false;
                $marksObtained = $isCorrect ? $question['marks'] : 0;

                // Save answer with type casting
                $this->mockTestAttemptModel->saveAnswer([
                    'attempt_id' => (int)$attemptId,
                    'question_id' => (int)$questionId,
                    'answer_id' => (int)$answerId
                ]);

                $totalMarks += (float)$question['marks'];
                $obtainedMarks += (float)$marksObtained;

                if ($isCorrect) {
                    $correctAnswers++;
                } else {
                    $wrongAnswers++;
                }
            }

            // Update attempt with marks
            $this->mockTestAttemptModel->updateAttempt($attemptId, [
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'attempted_questions' => $attemptedQuestions,
                'total_questions' => $totalQuestions
            ]);

            $this->activityLogModel->log(
                $_SESSION['user_id'],
                'test_attempt',
                'Submitted mock test',
'fa-clipboard-check'            );

            // Clear test session
            unset($_SESSION['test_answers']);
            unset($_SESSION['current_set_id']);
            unset($_SESSION['test_start_time']);

            echo json_encode([
                'success' => true,
                'attemptId' => $attemptId,
                'correctAnswers' => $correctAnswers,
                'wrongAnswers' => $wrongAnswers,
                'attemptedQuestions' => $attemptedQuestions,
                'unattemptedQuestions' => $totalQuestions - $attemptedQuestions,
                'score' => $obtainedMarks,
                'totalMarks' => $totalMarks,
                'totalQuestions' => $totalQuestions
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function saveAnswer()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['questionId']) || !isset($data['answerId'])) {
                throw new Exception('Missing required data');
            }

            if (!isset($_SESSION['test_answers'])) {
                $_SESSION['test_answers'] = [];
            }

            $_SESSION['test_answers'][$data['questionId']] = $data['answerId'];

            echo json_encode([
                'success' => true,
                'message' => 'Answer saved successfully'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function getReview($attemptId)
    {
        try {
            $answers = $this->mockTestAttemptModel->getAttemptReview($attemptId);

            echo json_encode([
                'success' => true,
                'answers' => $answers
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function reviewTest($attemptId)
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }

            $attempt = $this->mockTestAttemptModel->getAttemptWithAnswers($attemptId);

            if (!$attempt || $attempt['user_id'] !== $_SESSION['user_id']) {
                throw new Exception('Test review not found');
            }

            $content = $this->uirender('user/review', [
                'attempt' => $attempt,
                'questions' => $this->mockTestQuestionModel->getQuestionsWithAnswers($attemptId)
            ]);

            echo $this->uirender('user/layout', ['content' => $content]);
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
            header('Location: /');
            exit;
        }
    }
    public function toggleQuestion($action, $questionId, $mockTestId)
    {

        try {
            if ($action === 'add') {
                // Get question details to determine its category
                $question = $this->questionModel->getById($questionId);
                $categoryId = $question['category_id'];
                
                // Get allocation for this category
                $allocation = $this->quizModel->getCategoryAllocation($mockTestId, $categoryId);
                
                // Count existing questions in this category
                $existingCount = $this->mockTestQuestionModel->getQuestionCountForCategory($mockTestId, $categoryId);
                
                // Check if adding would exceed the limit
                if ($existingCount >= $allocation['number_of_questions']) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Cannot add more questions. Maximum limit of ' . $allocation['number_of_questions'] . ' questions reached for this category.',
                        'limit_exceeded' => true
                    ]);
                    return;
                }
                
                // Add the question to the mock test
                $this->mockTestQuestionModel->createQuestion($mockTestId, $questionId);
                echo json_encode(['success' => true, 'message' => 'Question added successfully']);
            } elseif ($action === 'remove') {
                // Remove the question from the mock test
                $this->mockTestQuestionModel->deleteMockQuestion($mockTestId, $questionId);
                echo json_encode(['success' => true, 'message' => 'Question removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }




    private function resetTestSession()
    {
        $_SESSION['correctAnswers'] = 0;
        $_SESSION['wrongAnswers'] = 0;
        $_SESSION['score'] = 0;
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

            if (!isset($_SESSION['current_test_id'])) {
                echo json_encode(['error' => 'No active mock test found']);
                return;
            }


            // Get data from session
            $userId = $_SESSION['user_id'];
            $mockTestId = $_SESSION['current_test_id'];
            $correctAnswers = $_SESSION['correctAnswers'] ?? 0;
            $wrongAnswers = $_SESSION['wrongAnswers'] ?? 0;

            // Get total questions
            $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($mockTestId);
            $totalQuestions = count($questions);

            // Calculate scores
            $unattempted = $totalQuestions - ($correctAnswers + $wrongAnswers);
            $score = ($correctAnswers / $totalQuestions) * 100;
            $timeTaken = isset($_SESSION['test_start_time']) ? time() - $_SESSION['test_start_time'] : 0;
            $stats = $this->mockTestAttemptModel->getTestStats($mockTestId);
            $userRank = $this->mockTestAttemptModel->getUserRank($userId, $mockTestId, $score);
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
            $attemptId = $this->mockTestAttemptModel->createAttempt($attemptData['user_id'], $_SESSION['current_set_id']);

            $this->mockTestAttemptModel->updateAttempt($attemptId, $attemptData);

            // Return success response
            echo json_encode([
                'success' => true,
                'correctAnswers' => $correctAnswers,
                'wrongAnswers' => $wrongAnswers,
                'totalQuestions' => $totalQuestions,
                'score' => $score,
                'unattempted' => $unattempted,
                'userCount' => $stats['attempt_count'],
                'highestScore' => $stats['highest_score'],
                'rank' => $userRank
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
