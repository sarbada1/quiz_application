<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\QuestionTypeModel;
use PDO;



class QuestionTypeController extends Controller
{
    private $questionTypeModel;

    public function __construct(PDO $pdo)
    {
        $this->questionTypeModel = new QuestionTypeModel($pdo);
    }

    public function index()
    {
        $categories = $this->questionTypeModel->getAll();
        $content = $this->render('admin/questiontype/view', ['teachers' => $categories]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $content = $this->render('admin/questiontype/add');
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $time_per_question = $_POST['time_per_question'] ?? '';

            if (empty($type)) {
                echo "Type is required.";
                return;
            }

            $result = $this->questionTypeModel->createQuestionType($type,$slug,$time_per_question);

            if ($result) {
                $_SESSION['message'] = "Question Type added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/questiontype/add');
            } else {
                $_SESSION['message'] = "Error adding Question Type";
                $_SESSION['status'] = "danger";
            }
        }
    }

    public function edit($id)
    {
        $category = $this->questionTypeModel->getById($id);
        if (!$category) {
            echo "Question Type not found.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $time_per_question = $_POST['time_per_question'] ?? '';
            if (empty($type)) {
                echo "Question Type name is required.";
                return;
            }

         

            $result = $this->questionTypeModel->updateQuestionType($id,$type,$slug,$time_per_question);

            if ($result) {
                $_SESSION['message'] = "Question Type edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/questiontype/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Question Type.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/questiontype/edit/' . $id);
                exit;
            }
        }

        $content = $this->render('admin/questiontype/edit', [
            'category' => $category,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $result = $this->questionTypeModel->deleteQuestionType($id);

        if ($result) {
            $_SESSION['message'] = "Question Type deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Question Type.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/questiontype/list');
        exit;
    }
}
