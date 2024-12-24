<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\QuizModel;
use MVC\Models\LevelModel;
use MVC\Models\ProgramModel;
use MVC\Models\CategoryModel;
use MVC\Models\QuizAttemptModel;


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



    public function __construct(PDO $pdo)
    {
        $this->quizModel = new QuizModel($pdo);
        $this->levelModel = new LevelModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizAttemptModel = new QuizAttemptModel($pdo); // Add this line

    }

    public function index()
    {
        $quizzes = $this->quizModel->getAll();
        $content = $this->render('admin/quiz/view', ['quizzes' => $quizzes]);
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


        $isLoggedIn =isset($_SESSION['user_id']) && isset($_SESSION['usertype_id']) 
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
        error_log("Starting quiz with slug: $slug and count: $count"); // Debug log

        try {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['message'] = 'Please login to start the quiz';
                header('Location: /quiz/' . $slug);
                exit;
            }

            $quiz = $this->quizModel->getBySlug($slug);
            if (!$quiz) {
                $_SESSION['message'] = 'Quiz not found';
                header('Location: /quiz');
                exit;
            }

            error_log("Found quiz: " . json_encode($quiz)); // Debug log

            $questionCount = min((int)$count, 50);

            // Debug log attempt data
            error_log("Attempting to create quiz attempt with data: " . json_encode([
                'user_id' => $_SESSION['user_id'],
                'quiz_id' => $quiz['id'],
                'total_questions' => $questionCount
            ]));

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

            $questions = $this->quizModel->getRandomQuestions($quiz['id'], $questionCount);
            if (empty($questions)) {
                throw new \Exception('No questions found for this quiz');
            }

            $content = $this->uirender('user/quiz/play', [
                'quiz' => $quiz,
                'questions' => $questions,
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

            // Save each answer with question order
            foreach ($data['answers'] as $questionId => $answer) {
                $this->quizAttemptModel->saveAnswer(
                    $data['attemptId'],
                    $questionId,
                    $answer['answerId'],
                    $answer['isCorrect'],
                    $answer['questionOrder'] ?? 0
                );
            }

            // Complete the attempt
            $success = $this->quizAttemptModel->completeAttempt($data['attemptId'], [
                'correct_answers' => $data['correctCount'],
                'wrong_answers' => $data['wrongCount'],
                'score' => $data['score']
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
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function showQuiz()
    {
        $quiz = $this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();

        $isLoggedIn =isset($_SESSION['user_id']) && isset($_SESSION['usertype_id']) 
        && $_SESSION['usertype_id'] == self::STUDENT_TYPE;

        $content = $this->uirender('user/quiz', [
            'quiz' => $quiz,
            'quizzes' => $quiz,
            'programs' => $programs,
            'isLoggedIn' => $isLoggedIn,
        ]);

        echo $this->uirender('user/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $categories = $this->categoryModel->getAllCategories();
        $levels = $this->levelModel->getAll();
        $content = $this->render('admin/quiz/add', [
            'categories' => $categories,
            'levels' => $levels,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $difficulty_level = $_POST['difficulty_level'] ?? '';
            $user_id = $_SESSION['user_id'] ?? '';

            if (empty($title)) {
                echo "Title is required.";
                return;
            }
            if (empty($title)) {
                echo "Slug is required.";
                return;
            }
            if (empty($description)) {
                echo "Description is required.";
                return;
            }


            $result = $this->quizModel->createQuiz($title, $slug, $description, $category_id, $user_id, $difficulty_level);

            if ($result) {
                $_SESSION['message'] = "Quiz added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/quiz/add');
            } else {
                $_SESSION['message'] = "Error adding Quiz";
                $_SESSION['status'] = "danger";
            }
        }
    }

    public function edit($id)
    {
        $category = $this->quizModel->getById($id);
        if (!$category) {
            echo "Quiz not found.";
            return;
        }
        $categories = $this->categoryModel->getAllCategories();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $description = $_POST['description'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $difficulty_level = $_POST['difficulty_level'] ?? '';
            $user_id = $_SESSION['user_id'] ?? '';


            if (empty($title)) {
                echo "Title is required.";
                return;
            }
            if (empty($description)) {
                echo "Description is required.";
                return;
            }

            $result = $this->quizModel->updateQuiz($id, $title, $slug, $description, $category_id, $user_id, $difficulty_level);

            if ($result) {
                $_SESSION['message'] = "Quiz edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/quiz/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Quiz.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/quiz/edit/' . $id);
                exit;
            }
        }
        $levels = $this->levelModel->getAll();

        $content = $this->render('admin/quiz/edit', [
            'category' => $category,
            'categories' => $categories,
            'levels' => $levels,
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
}
