<?php

namespace MVC\Controllers;

use PDO;
use Exception;
use MVC\Controller;
use MVC\Models\TagModel;
use MVC\Models\QuizModel;
use MVC\Models\CategoryModel;
use MVC\Models\LevelModel;
use MVC\Models\QuestionModel;
use MVC\Models\QuestionTypeModel;



class QuestionController extends Controller
{
    public $quizModel;
    public $questiontypeModel;
    public $questionModel;
    public $tagModel;
    public $levelModel;
    public $pdo;


    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
        $this->quizModel = new QuizModel($pdo);
        $this->questiontypeModel = new QuestionTypeModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->tagModel = new TagModel($pdo);
        $this->levelModel = new LevelModel($pdo);
    }

    public function index()
    {
        $selectedCategory = $_GET['category'] ?? '';
    
    // Build query conditionally
    $conditions = [];
    $params = [];
    
    if ($selectedCategory) {
        $conditions[] = "q.category_id = :category_id";
        $params[':category_id'] = $selectedCategory;
    }
    
    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
    
    // Get all filtered questions - NO PAGINATION HERE
    $query = "SELECT q.*, c.name as category_name 
              FROM questions q
              LEFT JOIN categories c ON q.category_id = c.id
              $whereClause
              ORDER BY q.id DESC";
    
    $stmt = $this->pdo->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    

        $content = $this->render('admin/question/view', [
            'questions' => $questions,
            'selectedCategory' => $selectedCategory,
            'categories' => $this->categoryModel->getAllCategories(),
        ]);

        echo $this->render('admin/layout', ['content' => $content]);
    }


    public function showAddForm()
    {
        $questionTypes = $this->questiontypeModel->getAll();
        $quizModels = $this->quizModel->getAll();
        $tags = $this->tagModel->getAllTags();
        $categories = $this->categoryModel->getAllCategories();
        $levels = $this->levelModel->getAll();

        $content = $this->render('admin/question/add', [
            'questionTypes' => $questionTypes,
            'quizModels' => $quizModels,
            'tags' => $tags,
            'categories' => $categories,
            'levels' => $levels
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question_text = $_POST['question_text'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $difficulty_level = $_POST['difficulty_level'] ?? 'easy';
            $marks = $_POST['marks'] ?? 1;
            $year = $_POST['year'] ?? null;
            $question_type = $_POST['question_type'] ?? 'quiz';
            $tagIds = $_POST['tags'] ?? [];
    
            if (empty($question_text) || empty($category_id)) {
                $_SESSION['message'] = "Question text and category are required.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/question/add');
                exit;
            }
    
            try {
                $this->pdo->beginTransaction();
    
                $questionId = $this->questionModel->createQuestion(
                    $question_text,
                    $difficulty_level,
                    $marks,
                    $category_id,
                    $question_type,
                    $year
                );
    
                if ($questionId && !empty($tagIds)) {
                    $this->questionModel->addQuestionTags($questionId, $tagIds);
                }
    
                $this->pdo->commit();
                $_SESSION['message'] = "Question added successfully!";
                $_SESSION['status'] = "success";
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $_SESSION['message'] = "Error adding question: " . $e->getMessage();
                $_SESSION['status'] = "danger";
            }
    
            header('Location: /admin/question/add');
            exit;
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question_text = $_POST['question_text'] ?? '';
            $question_type = $_POST['question_type'] ?? '';
            $difficulty_level = $_POST['difficulty_level'] ?? 'easy';
            $marks = $_POST['marks'] ?? 1;
            $year = $_POST['year'] ?? null;
            $category_id = $_POST['category_id'] ?? '';
            $tagIds = $_POST['tags'] ?? [];
    
            if (empty($question_text) || empty($question_type) || empty($category_id)) {
                $_SESSION['message'] = "All fields are required.";
                $_SESSION['status'] = "danger";
                header("Location: /admin/question/edit/$id");
                exit;
            }
    
            $this->pdo->beginTransaction();
            try {
                $result = $this->questionModel->updateQuestion(
                    $id, 
                    $question_text, 
                    $question_type, 
                    $difficulty_level, 
                    $marks, 
                    $year, 
                    $category_id
                );
    
                // Update tags
                $this->questionModel->deleteQuestionTags($id);
                if (!empty($tagIds)) {
                    $this->questionModel->addQuestionTags($id, $tagIds);
                }
    
                $this->pdo->commit();
                if ($result) {
                    $_SESSION['message'] = "Question updated successfully!";
                    $_SESSION['status'] = "success";
                }
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $_SESSION['message'] = "Error updating question: " . $e->getMessage();
                $_SESSION['status'] = "danger";
            }
    
            header("Location: /admin/question/edit/$id");
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get question data
            $question = $this->questionModel->getById($id);
            if (!$question) {
                $_SESSION['message'] = "Question not found";
                $_SESSION['status'] = "danger";
                header('Location: /admin/question/list');
                exit;
            }
    
            // Get all required data
            $quizModels = $this->quizModel->getAll();
            $questionTypes = $this->questiontypeModel->getAll();
            $tags = $this->tagModel->getAllTags();
            $questionTags = $this->questionModel->getQuestionTags($id);
            $categories = $this->categoryModel->getAllCategories();
            $levels = $this->levelModel->getAll();
    
            // Render edit form
            $content = $this->render('admin/question/edit', [
                'question' => $question,
                'quizModels' => $quizModels,
                'questionTypes' => $questionTypes,
                'tags' => $tags,
                'questionTags' => $questionTags,
                'categories' => $categories,
                'levels' => $levels
            ]);
            echo $this->render('admin/layout', ['content' => $content]);
            return;
        }
    }    public function filterQuestion($id)
    {
        if ($id == '0') {
            $questions = $this->questionModel->getAll();
        } else {

            $questions = $this->questionModel->questionFilter($id);
        }
        $i = 1; // Initialize $i before the loop
        if ($questions) {
            foreach ($questions as $question) {
                echo '<tr>';
                echo '<td>' . $i++ . '</td>'; // Increment $i inside the loop
                echo "<td>" . $question['question_text'] . "</td>";
                echo "<td>" . $question['title'] . "</td>";
                echo "<td>" . $question['type'] . "</td>";
                echo '<td>
                    <button class="success">
                        <a href="/quiz-play/admin/answer/add/' . $question["id"] . '">Add</a>
                    </button>
                    <button class="warning">
                        <a href="/quiz-play/admin/answer/list/' . $question["id"] . '">View</a>
                    </button>
                  </td>';
                echo '<td>
                    <button class="primary">
                        <a href="/quiz-play/admin/question/edit/' . $question["id"] . '">Edit</a>
                    </button>
                    <button class="danger">
                        <a href="/quiz-play/admin/question/delete/' . $question["id"] . '" onclick="return confirm(\'Are you sure to delete?\')">Delete</a>
                    </button>
                  </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No data found</td></tr>';
        }
    }

    public function delete($id)
    {
        $result = $this->questionModel->deleteQuestion($id);

        if ($result) {
            $_SESSION['message'] = "Question deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Question.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /quiz-play/admin/question/list');
        exit;
    }
}
