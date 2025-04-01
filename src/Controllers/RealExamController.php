<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\QuizModel;
use MVC\Models\MockTestModel;
use MVC\Models\ExamAttemptModel;
use MVC\Models\ExamSessionModel;
use MVC\Models\MockTestQuestionModel;
use MVC\Models\User;

class RealExamController extends Controller
{
    const STUDENT_TYPE = 3;

    public $quizModel;
    private $mockTestModel;
    private $mockTestQuestionModel;
    private $mockTestAttemptModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->mockTestModel = new MockTestModel($pdo);
        $this->mockTestQuestionModel = new MockTestQuestionModel($pdo);
        $this->mockTestAttemptModel = new ExamAttemptModel($pdo);
    }
    public function examList()
    {
        $userId = $_SESSION['user_id'];

        // Get all real exams
        $exams = $this->quizModel->getQuiz('real_exam');

        // Check attempt status for each exam
        foreach ($exams as &$exam) {
            $exam['has_attempted'] = $this->mockTestAttemptModel->hasAttempted($userId, $exam['id']);
            
            // Get exam session status if available
            $examSessionModel = new ExamSessionModel($this->pdo);
            $status = $examSessionModel->getExamStatus($exam['id']);
            $exam['status'] = $status['status'] ?? 'not_scheduled';
            $exam['start_time'] = $status['start_time'] ?? null;
            $exam['end_time'] = $status['end_time'] ?? null;
            
            // Hide draft exams unless you're an admin
            if ($exam['status'] === 'draft' && $_SESSION['usertype_id'] != 1) {
                // Remove from array
                unset($exam);
                continue;
            }
        }

        $content = $this->uirender('user/exam_list', [
            'exams' => $exams
        ]);

        echo $this->uirender('user/testlayout', ['content' => $content]);
    }
    public function studentDashboard()
    {

        $userId = $_SESSION['user_id'];

        // Get all exams the student is registered for
        $registeredExams = $this->mockTestModel->getExamsForUser($userId);
      

        $examSessionModel = new ExamSessionModel($this->pdo);
        $examData = [
            'upcoming' => [],
            'in_progress' => [],
            'past' => []
        ];

        foreach ($registeredExams as $exam) {
            $status = $examSessionModel->getExamStatus($exam['id']);
            $hasAttempted = $this->mockTestAttemptModel->hasAttempted($userId, $exam['id']);

            // Add attempt status to exam data
            $exam['has_attempted'] = $hasAttempted;
            $exam['status'] = $status['status'] ?? 'not_scheduled';
            $exam['start_time'] = $status['start_time'] ?? null;
            $exam['end_time'] = $status['end_time'] ?? null;
            
            if ($hasAttempted) {
                $attemptInfo = $this->mockTestAttemptModel->getLatestAttempt($userId, $exam['id']);
                $exam['attempt_id'] = $attemptInfo['id'] ?? null;
                $exam['is_published'] = $attemptInfo['is_published'] ?? 0;
            }
            // Categorize based on status
            if ($status['status'] === 'waiting') {
                $exam['start_time'] = $status['start_time'] ?? null;
                $exam['end_time'] = $status['end_time'] ?? null;
                $examData['upcoming'][] = $exam;
            } elseif ($status['status'] === 'in_progress' && !$hasAttempted) {
                $exam['end_time'] = $status['end_time'] ?? null;
                $examData['in_progress'][] = $exam;
            } else {
                $examData['past'][] = $exam;
            }
        }

        $content = $this->uirender('user/dashboard', [
            'examData' => $examData
        ]);

        echo $this->uirender('user/testlayout', ['content' => $content]);
    }
    public function takeExam($examId)
    {
        $userId = $_SESSION['user_id'];
        // Check if exam exists
        $exam = $this->quizModel->getById($examId);
        if (!$exam) {
            $_SESSION['message'] = "Exam not found.";
            $_SESSION['status'] = "danger";
            header('Location: /exam/list');
            exit;
        }
    
        // Check if exam is of type real_exam
        if ($exam['type'] !== 'real_exam') {
            $_SESSION['message'] = "Invalid exam type.";
            $_SESSION['status'] = "danger";
            header('Location: /exam/list');
            exit;
        }
      

        // Check if student has already taken this exam
        if ($this->mockTestAttemptModel->hasAttempted($userId, $examId)>0) {
            $_SESSION['message'] = "You have already taken this exam.";
            $_SESSION['status'] = "danger";
            header('Location: /exam/list');
            exit;
        }
       
        // Check if exam is currently in progress
        $examSessionModel = new ExamSessionModel($this->pdo);
        $status = $examSessionModel->getExamStatus($examId);
     
        if ($status['status'] !== 'in_progress') {
            $_SESSION['message'] = "This exam is not currently in progress.";
            $_SESSION['status'] = "danger";
            header('Location: /exam/list');
            exit;
        }
    
        // Get questions for the exam
        $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($examId);

        // Render the exam interface
        $content = $this->uirender('user/exam', [
            'exam' => $exam,
            'questions' => $questions,
            'websocket_url' => 'ws://localhost:8080',  // Configure as needed
            'end_time' => $status['end_time']
        ]);
    
        echo $this->uirender('user/testlayout', ['content' => $content]);
    }

    public function submitExam()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);




        $userId = $_SESSION['user_id'];
        $examId = $data['exam_id'];
        $answers = $data['answers'];

        // Check if student has already submitted
        if ($this->mockTestAttemptModel->hasAttempted($userId, $examId)) {
            http_response_code(400);
            echo json_encode(['error' => 'You have already submitted this exam']);
            exit;
        }

        // Calculate score
        $score = 0;
        $correctCount = 0;
        $wrongCount = 0;

        foreach ($answers as $questionId => $answerId) {
            $isCorrect = $this->mockTestQuestionModel->checkAnswer($answerId, $questionId);
            if ($isCorrect) {
                $correctCount++;
                $score += 1; // Adjust scoring logic as needed
            } else {
                $wrongCount++;
            }
        }

        // Save the attempt
        $attemptId = $this->mockTestAttemptModel->createAttempt([
            'user_id' => $userId,
            'exam_id' => $examId,
            'score' => $score,
            'correct_answers' => $correctCount,
            'wrong_answers' => $wrongCount,
            'completed_at' => date('Y-m-d H:i:s'),
            'is_published' => 0  // Add this flag to indicate results are not published yet
        ]);

        foreach ($answers as $questionId => $answerId) {
            $this->mockTestAttemptModel->saveUserAnswer($attemptId, $questionId, $answerId);
        }
    
        echo json_encode([
            'success' => true,
            'message' => 'Exam submitted successfully. Your results will be published by the administrator.',
            'redirect_url' => '/student/dashboard',
            'attempt_id' => $attemptId
        ]);
    }

    public function adminStartExam($examId)
    {
        // Check if user is admin
        if (!isset($_SESSION['usertype_id']) || $_SESSION['usertype_id'] != 1) {
            $_SESSION['message'] = "Unauthorized access.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/dashboard');
            exit;
        }

        $exam = $this->quizModel->getById($examId);
        if (!$exam) {
            $_SESSION['message'] = "Exam not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/dashboard');
            exit;
        }

        $content = $this->render('admin/realexam/control', [
            'exam' => $exam,
            'websocket_url' => 'ws://localhost:8080'
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function adminControlPanel($examId)
    {
        // Authentication check
        if (!isset($_SESSION['usertype_id']) || $_SESSION['usertype_id'] != 1) {
            $_SESSION['message'] = "Unauthorized access.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/dashboard');
            exit;
        }

        $exam = $this->quizModel->getById($examId);
        if (!$exam) {
            $_SESSION['message'] = "Exam not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/dashboard');
            exit;
        }

        // Get session info
        $examSessionModel = new ExamSessionModel($this->pdo);
        $session = $examSessionModel->getActiveSession($examId);

        $content = $this->render('admin/realexam/control', [
            'exam' => $exam,
            'session' => $session
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function scheduleExam()
    {
        // Authentication check
        if (!isset($_SESSION['usertype_id']) || $_SESSION['usertype_id'] != 1) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['exam_id']) || !isset($data['start_time']) || !isset($data['duration'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
        date_default_timezone_set('Asia/Kathmandu');
        $examId = $data['exam_id'];
        $startTime = date('Y-m-d H:i:s', strtotime($data['start_time']));
        $endTime = date('Y-m-d H:i:s', strtotime($data['start_time'] . ' + ' . $data['duration'] . ' minutes'));

        $examSessionModel = new ExamSessionModel($this->pdo);
        $sessionId = $examSessionModel->createSession($examId, $startTime, $endTime);

        echo json_encode([
            'success' => true,
            'session_id' => $sessionId,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);
    }
    public function viewExamResults($examId)
    {
        
        $exam = $this->quizModel->getById($examId);
        if (!$exam) {
            $_SESSION['message'] = "Exam not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Get all attempts for this exam
        $attempts = $this->mockTestAttemptModel->getAttemptsByExamId($examId);

        
        
        $content = $this->render('admin/realexam/results', [
            'exam' => $exam,
            'attempts' => $attempts
        ]);
    
        echo $this->render('admin/layout', ['content' => $content]);
    }
    
    public function publishResults()
    {
        // Check if user is admin
        if (!isset($_SESSION['usertype_id']) || $_SESSION['usertype_id'] != 1) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!isset($data['attempt_ids']) || !is_array($data['attempt_ids'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }
    
        $attemptIds = $data['attempt_ids'];
        $success = $this->mockTestAttemptModel->publishResults($attemptIds);
    
        echo json_encode(['success' => $success]);
    }
    public function endExam()
    {
        // Authentication check
        if (!isset($_SESSION['usertype_id']) || $_SESSION['usertype_id'] != 1) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['session_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing session ID']);
            exit;
        }

        $examSessionModel = new ExamSessionModel($this->pdo);
        $success = $examSessionModel->updateSessionStatus($data['session_id'], 'ended');

        echo json_encode(['success' => $success]);
    }

    public function checkExamStatus($examId)
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $examSessionModel = new ExamSessionModel($this->pdo);
        $status = $examSessionModel->getExamStatus($examId);

        echo json_encode($status);
    }

public function showResults($attemptId)
{
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "You must be logged in to view results.";
        $_SESSION['status'] = "danger";
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Get attempt data
    $attempt = $this->mockTestAttemptModel->getAttemptById($attemptId);
    
    // Verify this attempt belongs to this user
    if (!$attempt || $attempt['user_id'] != $userId) {
        $_SESSION['message'] = "You don't have permission to view these results.";
        $_SESSION['status'] = "danger";
        header('Location: /exam/list');
        exit;
    }
    
    // Get exam data
    $exam = $this->quizModel->getById($attempt['exam_id']);
    if (!$exam) {
        $_SESSION['message'] = "Exam not found.";
        $_SESSION['status'] = "danger";
        header('Location: /exam/list');
        exit;
    }
    
    // Get user answers for this attempt
    $userAnswers = $this->mockTestAttemptModel->getUserAnswers($attemptId);
    
    // Get all questions for this exam
    $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($attempt['exam_id']);
    
    // Enhance questions with user answers and correctness
    foreach ($questions as &$question) {
        $question['user_answer_id'] = null;
        $question['is_correct'] = false;
        
        foreach ($userAnswers as $userAnswer) {
            if ($userAnswer['question_id'] == $question['id']) {
                $question['user_answer_id'] = $userAnswer['answer_id'];
                $question['is_correct'] = $userAnswer['is_correct'];
                break;
            }
        }
    }
    
    // Calculate statistics
    $totalQuestions = count($questions);
    $attemptedQuestions = 0;
    $correctAnswers = $attempt['correct_answers'];
    $wrongAnswers = $attempt['wrong_answers'];
    
    foreach ($questions as $question) {
        if ($question['user_answer_id']) {
            $attemptedQuestions++;
        }
    }
    
    $score = $attempt['score'];
    $percentageScore = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100, 2) : 0;
    
    // Render the result template
    $content = $this->uirender('user/exam_results', [
        'attempt' => $attempt,
        'exam' => $exam,
        'questions' => $questions,
        'totalQuestions' => $totalQuestions,
        'attemptedQuestions' => $attemptedQuestions,
        'correctAnswers' => $correctAnswers,
        'wrongAnswers' => $wrongAnswers,
        'score' => $score,
        'percentageScore' => $percentageScore
    ]);
    
    echo $this->uirender('user/testlayout', ['content' => $content]);
}

public function viewStudentResult($attemptId)
{
    // Get attempt data
    $attempt = $this->mockTestAttemptModel->getAttemptById($attemptId);
    
    
    // Get user data
    $userModel = new User($this->pdo);
    $student = $userModel->getById($attempt['user_id']);
    
    // Get exam data
    $exam = $this->quizModel->getById($attempt['exam_id']);
    if (!$exam) {
        $_SESSION['message'] = "Exam not found.";
        $_SESSION['status'] = "danger";
        header('Location: /admin/dashboard');
        exit;
    }
    
    // Get user answers for this attempt
    $userAnswers = $this->mockTestAttemptModel->getUserAnswers($attemptId);
    echo $attemptId; die;
    
    // Get all questions for this exam
    $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($attempt['exam_id']);
    
    // Enhance questions with user answers and correctness
    foreach ($questions as &$question) {
        $question['user_answer_id'] = null;
        $question['is_correct'] = false;
        
        foreach ($userAnswers as $userAnswer) {
            if ($userAnswer['question_id'] == $question['id']) {
                $question['user_answer_id'] = $userAnswer['answer_id'];
                $question['is_correct'] = $userAnswer['is_correct'];
                break;
            }
        }
    }
    
    // Calculate statistics
    $totalQuestions = count($questions);
    $attemptedQuestions = 0;
    $correctAnswers = $attempt['correct_answers'];
    $wrongAnswers = $attempt['wrong_answers'];
    
    foreach ($questions as $question) {
        if ($question['user_answer_id']) {
            $attemptedQuestions++;
        }
    }
    
    $score = $attempt['score'];
    $percentageScore = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100, 2) : 0;
    
    // Render the result template
    $content = $this->render('admin/realexam/student_result', [
        'attempt' => $attempt,
        'exam' => $exam,
        'student' => $student,
        'questions' => $questions,
        'totalQuestions' => $totalQuestions,
        'attemptedQuestions' => $attemptedQuestions,
        'correctAnswers' => $correctAnswers,
        'wrongAnswers' => $wrongAnswers,
        'score' => $score,
        'percentageScore' => $percentageScore
    ]);
    
    echo $this->render('admin/layout', ['content' => $content]);
}
}
