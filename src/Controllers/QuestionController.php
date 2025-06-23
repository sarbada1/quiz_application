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
            // $question_type = $_POST['question_type'] ?? 'quiz';
            $tagIds = $_POST['tags'] ?? [];

            if (empty($question_text) || empty($category_id)) {
                $_SESSION['message'] = "Question text and category are required.";
                $_SESSION['status'] = "danger";
                header('Location: https://exam.tuentrance.com/admin/question/add');
                exit;
            }

            try {
                $this->pdo->beginTransaction();

                $questionId = $this->questionModel->createQuestion(
                    $question_text,
                    $difficulty_level,
                    $marks,
                    $category_id,
                    // $question_type,
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

            header('Location: https://exam.tuentrance.com/admin/question/add');
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
                header("Location: https://exam.tuentrance.com/admin/question/edit/$id");
                exit;
            }

            $this->pdo->beginTransaction();
            try {
                // Handle image upload if present
                $image_path = null;
                if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] === UPLOAD_ERR_OK) {
                    // Get the current image path if it exists
                    $currentQuestion = $this->questionModel->getById($id);
                    $oldImagePath = $currentQuestion['image_path'] ?? null;

                    // Setup upload parameters
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/questions/';

                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    // Generate a unique filename
                    $filename = uniqid('question_') . '_' . basename($_FILES['question_image']['name']);
                    $upload_file = $upload_dir . $filename;

                    // Check file type
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $file_type = $_FILES['question_image']['type'];

                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception("Only JPG, PNG, GIF, and WebP images are allowed.");
                    }

                    // Move the uploaded file
                    if (move_uploaded_file($_FILES['question_image']['tmp_name'], $upload_file)) {
                        // Set the relative path for database storage
                        $image_path = '/uploads/questions/' . $filename;

                        // Delete the old image if it exists
                        if ($oldImagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImagePath)) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . $oldImagePath);
                        }
                    } else {
                        throw new Exception("Failed to upload image.");
                    }
                } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
                    // If remove image checkbox is checked, set image_path to empty
                    $currentQuestion = $this->questionModel->getById($id);
                    $oldImagePath = $currentQuestion['image_path'] ?? null;

                    // Delete the old image if it exists
                    if ($oldImagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImagePath)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $oldImagePath);
                    }

                    $image_path = ''; // Empty path will remove the image reference
                }

                // Update question with image if provided
                if ($image_path !== null) {
                    $result = $this->questionModel->updateQuestionWithImage(
                        $id,
                        $question_text,
                        $question_type,
                        $difficulty_level,
                        $marks,
                        $category_id,
                        $image_path,
                        $year
                    );
                } else {
                    // Use the original update method without changing the image
                    $result = $this->questionModel->updateQuestion(
                        $id,
                        $question_text,
                        $question_type,
                        $difficulty_level,
                        $marks,
                        $category_id,
                        $year
                    );
                }

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
                header('Location: https://exam.tuentrance.com/admin/question/list');
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
    }
    public function filterQuestion($id)
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
                        <a href="https://exam.tuentrance.com/admin/answer/add/' . $question["id"] . '">Add</a>
                    </button>
                    <button class="warning">
                        <a href="https://exam.tuentrance.com/admin/answer/list/' . $question["id"] . '">View</a>
                    </button>
                  </td>';
                echo '<td>
                    <button class="primary">
                        <a href="https://exam.tuentrance.com/admin/question/edit/' . $question["id"] . '">Edit</a>
                    </button>
                    <button class="danger">
                        <a href="https://exam.tuentrance.com/admin/question/delete/' . $question["id"] . '" onclick="return confirm(\'Are you sure to delete?\')">Delete</a>
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
            $_SESSION['message'] = "Error deleting Quesbtion.";
            $_SESSION['status'] = "danger";
        }

        header('Location: https://exam.tuentrance.com/admin/question/list');
        exit;
    }

    public function bulkManage()
    {
        try {
            // Get filters from query string
            $selectedTag = isset($_GET['tag_filter']) ? intval($_GET['tag_filter']) : null;
            $selectedCategory = isset($_GET['category_filter']) ? intval($_GET['category_filter']) : null;

            // Get all tags and categories for filter dropdowns
            $tags = $this->tagModel->getAllTags();
            $categories = $this->categoryModel->getAllCategoriesWithParent();

            // Get questions grouped by tag, with filters applied
            $questionsByTag = $this->questionModel->getQuestionsGroupedByTag($selectedTag, $selectedCategory);

            // Get total question count
            $totalQuestions = $this->questionModel->getCount();

            $content = $this->render('admin/question/bulk-manage', [
                'tags' => $tags,
                'categories' => $categories,
                'questionsByTag' => $questionsByTag,
                'selectedTag' => $selectedTag,
                'selectedCategory' => $selectedCategory,
                'totalQuestions' => $totalQuestions
            ]);

            echo $this->render('admin/layout', ['content' => $content]);
        } catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            $_SESSION['status'] = "danger";
            header('Location: /admin/question/list');
            exit;
        }
    }

    public function bulkUpdateCategory()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method");
            }

            $tagId = isset($_POST['tag_id']) ? intval($_POST['tag_id']) : null;
            $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;

            if (!$tagId || !$categoryId) {
                throw new Exception("Missing required parameters");
            }

            // Get the tag name for logging
            $tag = $this->tagModel->getById($tagId);
            if (!$tag) {
                throw new Exception("Tag not found");
            }

            // Get the category name for logging
            $category = $this->categoryModel->getCategoryById($categoryId);
            if (!$category) {
                throw new Exception("Category not found");
            }

            // Update all questions with this tag to the new category
            $updatedCount = $this->questionModel->updateCategoryByTag($tagId, $categoryId);

            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => "Updated {$updatedCount} questions from tag '{$tag['name']}' to category '{$category['name']}'",
                'count' => $updatedCount
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
