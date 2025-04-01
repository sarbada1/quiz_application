<?php

namespace MVC\Controllers;

use PDO;
use Exception;
use MVC\Controller;
use MVC\Models\User;
use MVC\Models\TagModel;
use MVC\Models\QuizModel;
use MVC\Models\LevelModel;
use MVC\Models\ProgramModel;
use MVC\Models\CategoryModel;
use MVC\Models\QuestionModel;
use MVC\Models\ExamSessionModel;
use MVC\Models\QuizAttemptModel;
use MVC\Models\MockTestQuestionModel;

class QuizController extends Controller
{

    const ADMIN_TYPE = 1;
    const TEACHER_TYPE = 2;
    const STUDENT_TYPE = 3;

    public $quizModel;
    public $levelModel;
    public $categoryModel;
    public $programModel;
    private $quizAttemptModel;
    private $mockTestQuestionModel;
    private $userModel;
    private $tagModel;
    private $questionModel;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->quizModel = new QuizModel($pdo);
        $this->levelModel = new LevelModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->userModel = new User($pdo);
        $this->tagModel = new TagModel($pdo);
        $this->mockTestQuestionModel = new MockTestQuestionModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->quizAttemptModel = new QuizAttemptModel($pdo); // Add this line

    }

    public function index()
    {
        $quizzes = $this->quizModel->getAll();
        $content = $this->render('admin/quiz/view', ['quizzes' => $quizzes]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function previousYearQuizzes()
    {
        $quizzes = $this->quizModel->getQuiz('previous_year');
        $programs = $this->programModel->getWithCategory();
        $quiz = $this->quizModel->getAll();
        $content = $this->uirender('user/previous_year_quizzes', [
            'previousYearQuizzes' => $quizzes,
            'quizzes' => $quiz,
            'programs' => $programs,
        ]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function previousYearQuiz($id)
    {
        $quiz = $this->quizModel->getById($id);
        $questions = $this->questionModel->getPreviousYearQuestions($id);
        $programs = $this->programModel->getWithCategory();
        $quizzes = $this->quizModel->getAll();
        $content = $this->uirender('user/previous_year_quiz', [
            'quiz' => $quiz, 
            'questions' => $questions,
            'quizzes' => $quizzes,
            'programs' => $programs,
            ]
        );
        echo $this->uirender('user/layout', ['content' => $content]);
    }
    public function previousYearQuizQuestions($quizId)
    {
        $quiz = $this->quizModel->getById($quizId);
        $questions = $this->questionModel->getPreviousYearQuestions($quizId);

        $content = $this->render('admin/exam/previous_year_quiz_questions', ['quiz' => $quiz, 'questions' => $questions]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function quizList()
    {
        $quizzes = $this->quizModel->getQuiz('quiz');
        $programs = $this->programModel->getAll();
        $content = $this->render('admin/exam/quiz', ['quizzes' => $quizzes, 'program' => $programs]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function mockTestList()
    {
        $mocktests = $this->quizModel->getQuiz('mock'); // Get mock type quizzes
        $programs = $this->programModel->getAll(); // Get all programs


        $content = $this->render('admin/exam/mocktest', [
            'mocktests' => $mocktests,
            'program' => $programs
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function previousList()
    {
        $mocktests = $this->quizModel->getQuiz('previous_year'); // Get mock type quizzes
        $programs = $this->programModel->getAll(); // Get all programs


        $content = $this->render('admin/exam/previous', [
            'mocktests' => $mocktests,
            'program' => $programs
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function realExamList()
    {
        $mocktests = $this->quizModel->getQuiz('real_exam'); // Get mock type quizzes
        $programs = $this->programModel->getAll(); // Get all programs


        $content = $this->render('admin/exam/realexam', [
            'mocktests' => $mocktests,
            'program' => $programs
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showQuizDetail($slug)
    {
        $quiz = $this->quizModel->getQuizQuestionBySlug($slug);
        $categories = $this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();


        if (!$quiz) {
            header('location:/404.php');
            echo "Quiz not found";
            return;
        }


        $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['usertype_id'])
            && $_SESSION['usertype_id'] == self::STUDENT_TYPE;
        $content = $this->uirender('user/quiz_info', [
            'quiz' => $quiz,
            'isLoggedIn' => $isLoggedIn,
            'quizzes' => $categories,
            'programs' => $programs,
        ]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function startQuiz($slug, $count = 10)
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['message'] = 'Please login to start the quiz';
                header('Location: /quiz/' . $slug);
                exit;
            }

            $quiz = $this->quizModel->getQuizQuestionBySlug($slug);
            if (!$quiz) {
                $_SESSION['message'] = 'Quiz not found';
                header('Location: /quiz');
                exit;
            }

            error_log("Found quiz: " . json_encode($quiz)); // Debug log

            $questionCount = min((int)$count, 50);
            // Debug log attempt data


            // Create attempt
            $attemptData = [
                'user_id' => $_SESSION['user_id'],
                'quiz_id' => $quiz['id'],
                'total_questions' => $questionCount,
                'started_at' => date('Y-m-d H:i:s')
            ];

            $attemptId = $this->quizAttemptModel->createAttempt($attemptData);

            if (!$attemptId) {
                throw new \Exception('Failed to create quiz attempt');
            }

            error_log("Created attempt with ID: $attemptId"); // Debug log

            $questions = $this->quizModel->getQuizQuestions($quiz['id'], $questionCount);


            if (empty($questions)) {
                throw new \Exception('No questions found for this quiz');
            }
            $id = $_SESSION['user_id'];
            $user = $this->userModel->getById($id);
            $content = $this->uirender('user/quiz/play', [
                'quiz' => $quiz,
                'questions' => $questions,
                'user' => $user,
                'attemptId' => $attemptId
            ]);

            echo $this->uirender('user/layout', ['content' => $content]);
        } catch (\Exception $e) {
            error_log("Error in startQuiz: " . $e->getMessage());
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
            header('Location: /quiz/' . $slug);
            exit;
        }
    }

    public function submitQuiz()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);


            if (!$data || !isset($data['attemptId']) || !isset($data['answers'])) {
                throw new \Exception('Invalid request data');
            }

            error_log("Received answers: " . json_encode($data['answers'])); // Debug log

            // Save each answer with question order
            foreach ($data['answers'] as $index => $answer) {
                $success = $this->quizAttemptModel->saveAnswer(
                    $data['attemptId'],
                    $answer['questionId'], // Changed from using array key
                    $answer['answerId'],
                    $answer['isCorrect'],
                    $index // Use index as order
                );

                if (!$success) {
                    error_log("Failed to save answer for question: " . $answer['questionId']);
                }
            }

            // Complete the attempt
            $success = $this->quizAttemptModel->completeAttempt($data['attemptId'], [
                'correct_answers' => $data['correctCount'],
                'wrong_answers' => $data['wrongCount'],
                'score' => $data['score'],
                'completed_at' => date('Y-m-d H:i:s')
            ]);

            if (!$success) {
                throw new \Exception('Failed to save attempt');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'attemptId' => $data['attemptId'],
                'score' => (float)$data['score'],
                'correctCount' => (int)$data['correctCount'],
                'wrongCount' => (int)$data['wrongCount'],
                'totalQuestions' => (int)$data['totalQuestions']
            ]);
        } catch (\Exception $e) {
            error_log("Error in submitQuiz: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function showQuiz()
    {
        $quiz = $this->quizModel->getQuiz('quiz');
        $programs = $this->programModel->getWithCategory();

        $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['usertype_id'])
            && $_SESSION['usertype_id'] == self::STUDENT_TYPE;

        $content = $this->uirender('user/quiz', [
            'quiz' => $quiz,
            'quizzes' => $quiz,
            'programs' => $programs,
            'isLoggedIn' => $isLoggedIn,
        ]);

        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Create quiz
                $quizData = [
                    'title' => $_POST['title'],
                    'slug' => $_POST['slug'],
                    'description' => $_POST['description'],
                    'type' => $_POST['type'],
                    'total_marks' => $_POST['total_marks'] ?? 1,
                    'duration' => $_POST['duration'] ?? '',
                    'status' => $_POST['status'] ?? 'draft',
                    'year' => $_POST['year'] ?? null,
                    'categories' => !empty($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] : [],
                    'tags' => !empty($_POST['tags']) && is_array($_POST['tags']) ? $_POST['tags'] : []
                ];

                $quizId = $this->quizModel->createQuizWithTags($quizData);
                if ($_POST['type'] === 'real_exam' && !isset($_POST['schedule_later'])) {
                    // Create a session for this exam
                    $examSessionModel = new ExamSessionModel($this->pdo);
                    $startTime = $_POST['exam_start_time'] ?? null;
                    $endTime = $_POST['exam_end_time'] ?? null;
                    
                    if ($startTime && $endTime) {
                        $examSessionModel->createSession($quizId, $startTime, $endTime);
                    }
                }
                $_SESSION['message'] = "Quiz added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/quiz/add');
                exit;
            } catch (Exception $e) {
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['status'] = "error";
                header('Location: /admin/quiz/add');
                exit;
            }
        }
    }
    public function updateYear($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'] ?? null;

            if ($year === null || !is_numeric($year)) {
                $_SESSION['message'] = "Invalid year.";
                $_SESSION['status'] = "danger";
                header("Location: /admin/exam/previous");
                exit;
            }

            try {
                $this->quizModel->updateQuizYear($id, $year);
                $_SESSION['message'] = "Year updated successfully!";
                $_SESSION['status'] = "success";
            } catch (Exception $e) {
                $_SESSION['message'] = "Error updating year: " . $e->getMessage();
                $_SESSION['status'] = "danger";
            }

            header("Location: /admin/create/previous");
            exit;
        }
    }
    public function updateStudent($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $no_of_student = $_POST['no_of_student'] ?? null;

            if ($no_of_student === null) {
                $_SESSION['message'] = "Invalid year.";
                $_SESSION['status'] = "danger";
                header("Location: /admin/exam/previous");
                exit;
            }

            try {
                $this->quizModel->updateStudent($id, $no_of_student);
                $_SESSION['message'] = "No of Student updated successfully!";
                $_SESSION['status'] = "success";
            } catch (Exception $e) {
                $_SESSION['message'] = "Error updating student: " . $e->getMessage();
                $_SESSION['status'] = "danger";
            }

            header("Location: /admin/create/real_exam");
            exit;
        }
    }
    public function showForm()
    {
        $categories = $this->categoryModel->getAllCategories();
        $levels = $this->levelModel->getAll();
        $tags = $this->tagModel->getAllTags();

        $content = $this->render('admin/quiz/add', [
            'categories' => $categories,
            'levels' => $levels,
            'tags' => $tags
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function configureMock($quizId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                foreach ($_POST['categories'] as $categoryId => $config) {
                    $this->quizModel->addQuizCategory($quizId, [
                        'category_id' => $categoryId,
                        'marks_allocated' => $config['marks'],
                        'number_of_questions' => $config['questions']
                    ]);
                }

                // Generate random questions for each category
                $this->quizModel->generateMockQuestions($quizId);

                $_SESSION['message'] = "Mock test configured successfully!";
                header('Location: /admin/quiz/list');
                exit;
            } catch (Exception $e) {
                $_SESSION['message'] = $e->getMessage();
            }
        }

        $quiz = $this->quizModel->getById($quizId);
        $categories = $this->categoryModel->getAllCategories();

        return $this->render('admin/quiz/configure_mock', [
            'quiz_id' => $quizId,
            'total_marks' => $quiz['total_marks'],
            'categories' => $categories
        ]);
    }
    public function showMockConfig($id)
    {
        try {
            $quiz = $this->quizModel->getById($id);


            // Get all categories
            $categories = $this->categoryModel->getAllCategories();

            // Get existing configuration
            $quiz_categories = $this->quizModel->getQuizCategories($id);

            // Format existing config for easy access in view
            $existing_config = [];
            foreach ($quiz_categories as $qc) {
                $existing_config[$qc['category_id']] = [
                    'marks_allocated' => $qc['marks_allocated'],
                    'number_of_questions' => $qc['number_of_questions']
                ];
            }

            $content = $this->render('admin/quiz/config', [
                'quiz' => $quiz,
                'categories' => $categories,
                'quiz_categories' => $quiz_categories,
                'existing_config' => $existing_config
            ]);

            echo $this->render('admin/layout', ['content' => $content]);
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
            header('Location: /admin/quiz/configure-mock/$id');
            exit;
        }
    }

    public function saveMockConfig($id)
    {
        try {

            $configData = [
                'quiz_id' => $id,
                'categories' => $_POST['categories'] ?? []
            ];

            // Model handles all DB operations including transactions
            $this->quizModel->saveMockConfiguration($configData);

            $_SESSION['message'] = 'Mock test configured successfully';
            $_SESSION['status'] = 'success';
            header('Location: /admin/quiz/configure-mock/' . $id);
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
            header("Location: /admin/quiz/configure-mock/" . $id);
            exit;
        }
    }

    public function updateConfig()
    {
        try {
            $quizId = $_POST['quiz_id'] ?? null;
            if (!$quizId) {
                throw new Exception('Quiz ID is required');
            }

            // Process form data and update configuration
            $success = $this->quizModel->updateConfiguration($quizId, $_POST);

            echo json_encode([
                'success' => true,
                'message' => 'Mock test configuration updated successfully',
                'config' => [
                    'remaining_marks' => $this->quizModel->getRemainingMarks($quizId)
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
 * Update category allocation for a quiz
 */
public function updateCategoryAllocation()
{
    try {
 

        $categoryId = $_POST['category_id'] ?? null;
        $quizId = $_POST['quiz_id'] ?? null;
        $numberQuestions = $_POST['number_of_questions'] ?? null;
        $marksAllocated = $_POST['marks_allocated'] ?? null;
    
        // Validate inputs
        if (!$categoryId || !$quizId || !$numberQuestions || !$marksAllocated) {
            throw new Exception('Missing required parameters');
        }
        
        if (!is_numeric($numberQuestions) || $numberQuestions < 1 || 
            !is_numeric($marksAllocated) || $marksAllocated < 1) {
            throw new Exception('Invalid question count or marks value');
        }
        
        // Check if the current questions exceed the new allocation
        $existingCount = $this->mockTestQuestionModel->getQuestionCountForCategory($quizId, $categoryId);
        if ($existingCount > $numberQuestions) {
            throw new Exception('Cannot reduce allocation below current question count (' . $existingCount . ')');
        }
        
        // Update the allocation in the database
        $success = $this->quizModel->updateCategoryAllocation(
            $quizId, 
            $categoryId, 
            $numberQuestions, 
            $marksAllocated
        );
        
        if (!$success) {
            throw new Exception('Failed to update allocation');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Category allocation updated successfully'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
    public function showSet($quizId)
    {
        $quiz = $this->quizModel->getById($quizId);
        $sets = $this->quizModel->getSets($quizId);

        $content = $this->render('admin/quiz/sets', [
            'quiz' => $quiz,
            'sets' => $sets ?? ''
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function createSet($quizId)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $data = [
                'set_name' => $_POST['set_name'],
                'status' => $_POST['status']
            ];

            $setId = $this->quizModel->createSet($quizId, $data);

            $_SESSION['message'] = 'Set created successfully';
            $_SESSION['status'] = 'success';

            header("Location: /admin/quiz/$quizId/sets");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
            header("Location: /admin/quiz/$quizId/sets");
            exit;
        }
    }

    public function deleteSet($setId)
    {
        try {
            $this->quizModel->deleteSet($setId);
            $_SESSION['message'] = 'Set deleted successfully';
            $_SESSION['status'] = 'success';
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function publishSet($setId)
    {
        try {
            $this->quizModel->publishSet($setId);
            $_SESSION['message'] = 'Set published successfully';
            $_SESSION['status'] = 'success';
        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            $_SESSION['status'] = 'error';
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function edit($id)
    {
        $quiz = $this->quizModel->getById($id);
        $examSession = null;
        if ($quiz['type'] === 'real_exam') {
            $examSessionModel = new ExamSessionModel($this->pdo);
            $examSession = $examSessionModel->getLatestSessionForExam($id);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'id' => $id,
                    'title' => $_POST['title'],
                    'slug' => $_POST['slug'],
                    'description' => $_POST['description'],
                    'type' => $_POST['type'],
                    'status' => $_POST['status'],
                    'year' => $_POST['year'] ?? null,
                    'total_marks' => $_POST['total_marks'] ?? 1,
                    'duration' => $_POST['duration'] ?? '',
                    'categories' => array_map(function ($categoryId) {
                        return [
                            'category_id' => $categoryId,
                            'marks_allocated' => 0,
                            'number_of_questions' => 0
                        ];
                    }, $_POST['categories'] ?? []),
                    'tags' => $_POST['tags'] ?? []
                ];

                $this->quizModel->updateQuiz($data);

                if ($_POST['type'] === 'real_exam' && !isset($_POST['schedule_later'])) {
                    $examSessionModel = new ExamSessionModel($this->pdo);
                    $startTime = $_POST['exam_start_time'] ?? null;
                    $endTime = $_POST['exam_end_time'] ?? null;
                    
                    if ($startTime && $endTime) {
                        // If there's an existing session, update it
                        if ($examSession) {
                            $examSessionModel->updateSession($examSession['id'], $startTime, $endTime);
                        } else {
                            // Otherwise create a new one
                            $examSessionModel->createSession($id, $startTime, $endTime);
                        }
                    }
                }
                $_SESSION['message'] = 'Quiz updated successfully';
                $_SESSION['status'] = 'success';
                header('Location: /admin/quiz/edit/' . $id);
                exit;
            } catch (Exception $e) {
                $_SESSION['message'] = $e->getMessage();
                $_SESSION['status'] = 'error';
            }
        }

        $categories = $this->categoryModel->getAllCategories();
        $tags = $this->tagModel->getAllTags();
        $selectedCategories = $this->quizModel->getQuizCategoryIds($id);
        $selectedTags = $this->quizModel->getQuizTagIds($id);

        $content = $this->render('admin/quiz/edit', [
            'quiz' => $quiz,
            'categories' => $categories,
            'tags' => $tags,
            'selectedCategories' => $selectedCategories,
            'selectedTags' => $selectedTags,
            'examSession' => $examSession

        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function delete($id)
    {
        $result = $this->quizModel->deleteQuiz($id);

        if ($result) {
            $_SESSION['message'] = "Quiz deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Quiz.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/quiz/list');
        exit;
    }
    public function getReview($attemptId)
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            $reviewData = $this->quizAttemptModel->getReviewData($attemptId);

            if ($reviewData === false) {
                throw new \Exception('Failed to load review data');
            }

            echo json_encode([
                'success' => true,
                'answers' => $reviewData
            ]);
        } catch (\Exception $e) {
            error_log("Review error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function showHistory()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $history = $this->quizAttemptModel->getUserHistory($_SESSION['user_id']);

        $content = $this->uirender('user/quiz/history', [
            'history' => $history
        ]);

        echo $this->uirender('user/layout', ['content' => $content]);
    }
    public function configureQuiz()
    {
        $categories = $this->categoryModel->getAllCategories();
        $levels = $this->levelModel->getAll();

        $content = $this->uirender('user/quiz_configure', [
            'categories' => $categories,
            'levels' => $levels
        ]);
        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function startCustomQuiz()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Please login to start the quiz';
            header('Location: /quiz/configure');
            exit;
        }

        $categoryId = $_POST['category_id'] ?? null;
        $levelId = $_POST['level_id'] ?? null;
        $questionCount = min((int)($_POST['question_count'] ?? 10), 50);

        // Get questions based on selected criteria
        $questions = $this->quizModel->getCustomQuestions($categoryId, $levelId, $questionCount);
        // print_r($questions);    
        if (empty($questions)) {
            $_SESSION['message'] = 'No questions found for selected criteria';
            $_SESSION['status'] = 'error';
            header('Location: /quiz/configure');
            exit;
        }

        // Create attempt record
        $attemptData = [
            'user_id' => $_SESSION['user_id'],
            'quiz_id' => null, // Custom quiz
            'total_questions' => $questionCount,
            'started_at' => date('Y-m-d H:i:s')
        ];

        $attemptId = $this->quizAttemptModel->createAttempt($attemptData);

        if (!$attemptId) {
            $_SESSION['message'] = 'Failed to create quiz attempt';
            $_SESSION['status'] = 'error';
            header('Location: /quiz/configure');
            exit;
        }

        // Render custom quiz play view
        $content = $this->uirender('user/quiz/custom_play', [
            'questions' => $questions,
            'attemptId' => $attemptId
        ]);

        echo $this->uirender('user/layout', ['content' => $content]);
    }
    public function createRealExam()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'slug' => $_POST['slug'],
                'description' => $_POST['description'],
                'type' => 'real_exam',
                'total_marks' => $_POST['total_marks'],
                'duration' => $_POST['duration'],
                'status' => 'draft',
                'year' => $_POST['year'],
                'categories' => $_POST['categories'] ?? [],
                'tags' => $_POST['tags'] ?? []
            ];
            $this->quizModel->createQuizWithTags($data);
            $_SESSION['message'] = "Real Exam created successfully!";
            $_SESSION['status'] = "success";
            header('Location: /admin/quiz/list');
            exit;
        }
        $categories = $this->categoryModel->getAllCategories();
        $tags = $this->tagModel->getAllTags();
        $content = $this->render('admin/quiz/add', [
            'categories' => $categories,
            'tags' => $tags
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function startRealExam($slug)
    {
        $quiz = $this->quizModel->getQuizBySlug($slug);
        $questions = $this->quizModel->getRealExamQuestions('bca');
        $content = $this->render('user/real_exam', [
            'quiz' => $quiz,
            'questions' => $questions
        ]);
        echo $this->render('user/layout', ['content' => $content]);
    }
}
