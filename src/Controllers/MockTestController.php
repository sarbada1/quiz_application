<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\MockTestModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use PDO;

class MockTestController extends Controller
{
    public $programModel;
    public $quizModel;
    private $mockTestModel;

    public function __construct(PDO $pdo)
    {
        $this->programModel = new ProgramModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->mockTestModel = new MockTestModel($pdo);
    }

    public function index($id)
    {
        $program = $this->programModel->getById($id);
        $mocktests = $this->mockTestModel->getByProgramId($id);
    
        $content = $this->render('admin/mocktest/view', [
            'program' => $program,
            'mocktests' => $mocktests,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm($id)
    {
        $program = $this->programModel->getById($id);
    
        if (!$program) {
            $_SESSION['message'] = "Program not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }
    
        $content = $this->render('admin/mocktest/add', [
            'program' => $program,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }
    public function showTestDetail($slug)
    {
        $programs = $this->programModel->getWithCategory();
        $quiz = $this->quizModel->getAll();
        $id=$this->programModel->getBySlug($slug);
        $mocktests = $this->mockTestModel->getByProgramId($id['id']);
        $content = $this->uirender('user/test_info', [
            'programs' => $programs,
            'quizzes' => $quiz,
            'mocktests' => $mocktests,
    
        ]);
    
        echo $this->uirender('user/layout', ['content' => $content]);
    
    }
    public function add($id)
    {
        $program = $this->programModel->getById($id);
    
        if (!$program) {
            $_SESSION['message'] = "Program not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $time = $_POST['time'] ?? '';

            $result = $this->mockTestModel->createMockTest($id, $name, $time,$slug);
    
            if ($result) {
                $_SESSION['message'] = "Mock Test added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktest/add/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error adding Mock Test.";
                $_SESSION['status'] = "danger";
            }
        }
    
        $content = $this->render('admin/mocktest/add', [
            'program' => $program,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function edit($id)
    {
        $mocktest = $this->mockTestModel->getById($id);
    
        if (!$mocktest) {
            $_SESSION['message'] = "Mock Test not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $time = $_POST['time'] ?? '';
         
            $result = $this->mockTestModel->updateMockTest($id, $name, $time,$slug);
    
            if ($result) {
                $_SESSION['message'] = "Mock Test edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/mocktest/edit/' . $mocktest['id']);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Mock Test.";
                $_SESSION['status'] = "danger";
            }
        }
    
        $content = $this->render('admin/mocktest/edit', [
            'mocktest' => $mocktest,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $mocktest = $this->mockTestModel->getById($id);

        if (!$mocktest) {
            $_SESSION['message'] = "Mock Test not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/program/list');
            exit;
        }

        $result = $this->mockTestModel->deleteMockTest($id);

        if ($result) {
            $_SESSION['message'] = "Mock Test deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Mock Test.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/mocktest/list/' . $mocktest['program_id']);
        exit;
    }
}