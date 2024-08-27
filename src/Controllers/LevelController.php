<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\LevelModel;
use PDO;



class LevelController extends Controller
{
    private $levelModel;

    public function __construct(PDO $pdo)
    {
        $this->levelModel = new LevelModel($pdo);
    }

    public function index()
    {
        $categories = $this->levelModel->getAll();
        $content = $this->render('admin/level/view', ['teachers' => $categories]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $content = $this->render('admin/level/add');
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $level = $_POST['level'] ?? '';

            if (empty($level)) {
                echo "level is required.";
                return;
            }

            $result = $this->levelModel->createLevel($level);

            if ($result) {
                $_SESSION['message'] = "Level added successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/level/add');
            } else {
                $_SESSION['message'] = "Error adding Level";
                $_SESSION['status'] = "danger";
            }
        }
    }

    public function edit($id)
    {
        $category = $this->levelModel->getById($id);
        if (!$category) {
            echo "Level not found.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $level = $_POST['level'] ?? '';
           
            if (empty($level)) {
                echo "Level name is required.";
                return;
            }

         

            $result = $this->levelModel->updateLevel($id,$level);

            if ($result) {
                $_SESSION['message'] = "Level edited successfully!";
                $_SESSION['status'] = "success";
                header('Location: /admin/level/edit/' . $id);
                exit;
            } else {
                $_SESSION['message'] = "Error updating Level.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/level/edit/' . $id);
                exit;
            }
        }

        $content = $this->render('admin/level/edit', [
            'category' => $category,
        ]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $result = $this->levelModel->deleteLevel($id);

        if ($result) {
            $_SESSION['message'] = "Level deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting Level.";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/level/list');
        exit;
    }
}
