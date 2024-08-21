<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\QuestionModel;
use MVC\Models\QuestionTypeModel;
use MVC\Models\QuizModel;
use PDO;



class QuestionController extends Controller
{
    private $quizModel;
    private $questiontypeModel;
    private $categoryModel;
    private $questionModel;


    public function __construct(PDO $pdo)
    {
        $this->quizModel = new QuizModel($pdo);
        $this->questiontypeModel = new QuestionTypeModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
    }

    public function index()
    {
        $questions = $this->questionModel->getAll();
        $content = $this->render('admin/question/view', ['questions' => $questions]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $questionTypes = $this->questiontypeModel->getAll();
        $quizModels = $this->quizModel->getAll();
        $content = $this->render('admin/question/add', [
            'questionTypes' => $questionTypes,
            'quizModels' => $quizModels,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question_text = $_POST['question_text'] ?? '';
            $quiz_id = $_POST['quiz_id'] ?? '';
            $question_type = $_POST['question_type'] ?? '';

            if (empty($question_text)) {
                echo "Question text is required.";
                return;
            }
            if (empty($quiz_id)) {
                echo "Quiz is required.";
                return;
            }
            if (empty($question_type)) {
                echo "Question Type is required.";
                return;
            }
         

            $result = $this->questionModel->createQuestion($question_text, $quiz_id, $question_type);

            if ($result) {
                $_SESSION['message'] = "Question added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/question/add');
            } else {
                $_SESSION['message'] = "Error adding Question";
                $_SESSION['status'] = "danger";
            }
        }
    }

    public function edit($id)
    {
        $category = $this->questionModel->getById($id);
        if (!$category) {
            echo "Quiz not found.";
            return;
        }
        $questionTypes = $this->questiontypeModel->getAll();
        $quizModels = $this->quizModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question_text = $_POST['question_text'] ?? '';
            $quiz_id = $_POST['quiz_id'] ?? '';
            $question_type = $_POST['question_type'] ?? '';


            if (empty($question_text)) {
                echo "Question text is required.";
                return;
            }
            if (empty($quiz_id)) {
                echo "Quiz is required.";
                return;
            }
            if (empty($question_type)) {
                echo "Question Type is required.";
                return;
            }

            $result = $this->questionModel->updateQuestion($id,$question_text, $quiz_id, $question_type);

            if ($result) {
                $_SESSION['message'] = "Question edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/question/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Question.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/question/edit/' . $id);
                exit;
            }
        }

        $content = $this->render('admin/question/edit', [
            'questionTypes' => $questionTypes,
            'quizModels' => $quizModels,
            'category' => $category,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
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

        header('Location: /admin/question/list');
        exit;
    }
}
