<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\MockTestAnswerModel;
use MVC\Models\MockTestQuestionModel;
use PDO;



class MockTestAnswerController extends Controller
{

    private $questionModel;
    private $answerModel;


    public function __construct(PDO $pdo)
    {
        $this->questionModel = new MockTestQuestionModel($pdo);
        $this->answerModel = new MockTestAnswerModel($pdo);
    }

    public function index($id)
    {
        $question = $this->questionModel->getById($id);
    
        if (!$question) {
            $_SESSION['message'] = "Question not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/mocktestanswer/list');
            exit;
        }
    
        $answers = $this->answerModel->getByQuestionId($id);
    
        $content = $this->render('admin/mocktestanswer/view', [
            'question' => $question,
            'answers' => $answers,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm($id)
    {
        $question = $this->questionModel->getById($id);
        if (!$question) {
            $_SESSION['message'] = "Question not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/mocktestanswer/list');
            exit;
        }
    
        $content = $this->render('admin/mocktestanswer/add', [
            'question' => $question,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    

    public function add($id)
    {
        $question = $this->questionModel->getById($id);
    
        if (!$question) {
            $_SESSION['message'] = "Question not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/mocktestanswer/list');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $answer = $_POST['answer'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $isCorrect = isset($_POST['isCorrect']) ? (int)$_POST['isCorrect'] : 0;
    
            if (empty($answer)) {
                echo "Answer is required.";
                return;
            }
    
            $result = $this->answerModel->createAnswer($id, $answer, $reason, $isCorrect);
    
            if ($result) {
                $_SESSION['message'] = "Answer added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktestanswer/add/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error adding Answer.";
                $_SESSION['status'] = "danger";
            }
        }
    
        $content = $this->render('admin/mocktestanswer/add', [
            'question' => $question,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    

    public function edit($id)
    {
        $answer = $this->answerModel->getById($id);
        if (!$answer) {
            $_SESSION['message'] = "Answer not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/mocktestanswer/list/' . $answer['question_id']);
            exit;
        }
    
        $question = $this->questionModel->getById($answer['question_id']);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $answer_text = $_POST['answer'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $isCorrect = isset($_POST['isCorrect']) ? (int)$_POST['isCorrect'] : 0;
    
            if (empty($answer_text)) {
                echo "Answer is required.";
                return;
            }
    
            $result = $this->answerModel->updateAnswer($id, $answer['question_id'], $answer_text, $reason, $isCorrect);
    
            if ($result) {
                $_SESSION['message'] = "Answer edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktestanswer/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Answer.";
                $_SESSION['status'] = "danger";
            }
        }
    
        $content = $this->render('admin/mocktestanswer/edit', [
            'question' => $question,
            'answer' => $answer,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function delete($id)
    {
        $result = $this->answerModel->deleteAnswer($id);
        // $answer = $this->answerModel->getById($id);

        if ($result) {
            $_SESSION['message'] = "Answer deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Answer.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/mocktestanswer/list/' . $id);
        exit;
    }
}
