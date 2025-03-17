<?php

namespace MVC\Controllers;

use PDO;
use MVC\Controller;
use MVC\Models\QuizModel;
use MVC\Models\ProgramModel;
use MVC\Models\CategoryModel;
use MVC\Models\MockTestAttemptModel;
use MVC\Models\MockTestModel;
use MVC\Models\MockTestQuestionModel;
use MVC\Models\QuestionModel;
use MVC\Models\SubjectTestModel;

class SubjectTestController extends Controller 
{
    const STUDENT_TYPE = 3;
    
    public $subjectTestModel;
    public $programModel;
    public $quizModel;
    public $categoryModel;
    public $mockTestModel;
    public $questionModel;
    public $mockTestAttemptModel;
    public $mockTestQuestionModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->subjectTestModel = new SubjectTestModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->mockTestModel = new MockTestModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->mockTestAttemptModel = new MockTestAttemptModel($pdo);
        $this->mockTestQuestionModel = new MockTestQuestionModel($pdo);
    }

    public function index($id)
    {
        $program = $this->programModel->getById($id);
        $subjecttests = $this->subjectTestModel->getByProgramId($id);

        $content = $this->render('admin/subjecttest/view', [
            'program' => $program,
            'subjecttests' => $subjecttests,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showSubjectTest($slug) {
        $programs = $this->programModel->getWithCategory();
        $program = $this->programModel->getBySlug($slug);
        // Get subjects and chapters
        $categories = $this->categoryModel->getSubjectsWithChapters($program['category_id']);
        
       
        // Organize into hierarchy
        $subjects = [];
        foreach ($categories as $category) {
            if ($category['level'] === 0) {
                // This is a subject
                $subjects[$category['id']] = [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'question_count' => $category['question_count'],
                    'chapters' => []
                ];
            } else {
                // This is a chapter
                $subjects[$category['parent_id']]['chapters'][] = [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'question_count' => $category['question_count']
                ];
            }
        }
        
        // Filter out subjects with no questions
        $subjects = array_filter($subjects, function($subject) {
            return $subject['question_count'] > 0 || 
                   array_sum(array_column($subject['chapters'], 'question_count')) > 0;
        });
    
        $content = $this->uirender('user/subject_test', [
            'programs' => $programs,
            'subjects' => $subjects,
            'program' => $program
        ]);
    
        echo $this->uirender('user/layout', ['content' => $content]);
    }
    
    public function startSubjectTest($id) {
        $subject = $this->categoryModel->getById($id);
        $mockTest = $this->mockTestModel->getByCategory($id);
        $mockTestId = $mockTest['id'];
    
        // Initialize session for this test
        if (!isset($_SESSION['answeredQuestions'][$mockTestId])) {
            $_SESSION['answeredQuestions'][$mockTestId] = [];
        }
    
        $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['usertype_id']) 
                     && $_SESSION['usertype_id'] == self::STUDENT_TYPE;
        
        $_SESSION['current_subject_id'] = $mockTestId;
        $_SESSION['test_start_time'] = time();
    
        // Get questions for this subject
        $questions = $this->mockTestQuestionModel->getQuestionsWithAnswersByMockTestId($mockTestId);
    
        // Reset score counters
        // $this->resetTestSession();
    
        $content = $this->uirender('user/subject_exam', [
            'questions' => $questions,
            'mockTest' => $mockTest,
            'subject' => $subject,
            'isLoggedIn' => $isLoggedIn,
            // 'answeredQuestions' => $_SESSION['answeredQuestions'][$mockTestId]
        ]);
    
        echo $this->uirender('user/testlayout', ['content' => $content]);
    }
    
    public function checkAnswer($answerId, $questionId, $subjectId) {
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
    
    public function submitPerformance() {
        try {
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['current_subject_id'])) {
                throw new \Exception('Invalid session data');
            }
    
            $userId = $_SESSION['user_id'];
            $subjectId = $_SESSION['current_subject_id'];
            $correctAnswers = $_SESSION['correctAnswers'] ?? 0;
            $wrongAnswers = $_SESSION['wrongAnswers'] ?? 0;
            $totalQuestions = $this->mockTestQuestionModel->getTotalQuestions($subjectId);
            
            $score = ($correctAnswers / $totalQuestions) * 100;
            $timeTaken = time() - $_SESSION['test_start_time'];
            
            $attemptData = [
                'user_id' => $userId,
                'mock_test_id' => $subjectId,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'score' => $score,
                'time_taken' => $timeTaken,
                'completion_status' => 'completed'
            ];
    
            $this->mockTestAttemptModel->createAttempt($attemptData);
    
            echo json_encode([
                'success' => true,
                'correctAnswers' => $correctAnswers,
                'wrongAnswers' => $wrongAnswers,
                'score' => $score,
                'totalQuestions' => $totalQuestions
            ]);
        } catch (\Exception $e) {
            error_log('Error in submitPerformance: ' . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    // Add other controller methods for CRUD operations
    // Similar to MockTestController but for subject tests
}