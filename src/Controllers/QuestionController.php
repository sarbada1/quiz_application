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
    public $quizModel;
    public $questiontypeModel;
    public $questionModel;

    public function __construct(PDO $pdo)
    {
        $this->quizModel = new QuizModel($pdo);
        $this->questiontypeModel = new QuestionTypeModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $selectedQuiz = $_GET['quiz'] ?? null;
        $questionType = $_GET['question_type'] ?? null;
    
        $result = $this->questionModel->getQuestionsGroupedPaginated($page, 10, $selectedQuiz, $questionType);
    
        $content = $this->render('admin/question/view', [
            'questions' => $result['questions'],
            'totalPages' => $result['pages'],
            'currentPage' => $page,
            'selectedQuiz' => $selectedQuiz,
            'questionType' => $questionType,
            'quizzes' => $this->quizModel->getAll(),
            'questionTypes' => $this->questionModel->getQuestionTypes()
        ]);
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
                header('Location: /quiz-play/admin/question/add');
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

            $result = $this->questionModel->updateQuestion($id, $question_text, $quiz_id, $question_type);

            if ($result) {
                $_SESSION['message'] = "Question edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /quiz-play/admin/question/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Question.";
                $_SESSION['status'] = "danger";
                header('Location: /quiz-play/admin/question/edit/' . $id);
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
    public function filterQuestion($id)
    {
        if($id=='0'){
            $questions = $this->questionModel->getAll();
        }
        else{

            $questions = $this->questionModel->questionFilter($id);
        }
        $i = 1; // Initialize $i before the loop
    if($questions){
        foreach($questions as $question)
        {
            echo '<tr>';
            echo '<td>'.$i++.'</td>'; // Increment $i inside the loop
            echo "<td>".$question['question_text']."</td>";
            echo "<td>".$question['title']."</td>";
            echo "<td>".$question['type']."</td>";
            echo '<td>
                    <button class="success">
                        <a href="/quiz-play/admin/answer/add/'.$question["id"].'">Add</a>
                    </button>
                    <button class="warning">
                        <a href="/quiz-play/admin/answer/list/'.$question["id"].'">View</a>
                    </button>
                  </td>';
            echo '<td>
                    <button class="primary">
                        <a href="/quiz-play/admin/question/edit/'.$question["id"].'">Edit</a>
                    </button>
                    <button class="danger">
                        <a href="/quiz-play/admin/question/delete/'.$question["id"].'" onclick="return confirm(\'Are you sure to delete?\')">Delete</a>
                    </button>
                  </td>';
            echo '</tr>';
        }
    }
    else{
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
