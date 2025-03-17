<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryTypeModel;
use PDO;

class CategoryTypeController extends Controller {
    private $categoryTypeModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->categoryTypeModel = new CategoryTypeModel($pdo);
    }

    public function index() {
        $types = $this->categoryTypeModel->getAll();
        $content = $this->render('admin/category_type/list', ['types' => $types]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm() {
        $content = $this->render('admin/category_type/add');
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
       

            if (empty($name)) {
                $_SESSION['message'] = "Name is required";
                $_SESSION['status'] = "danger";
                header('Location: /admin/category-type/add');
                exit;
            }

            $result = $this->categoryTypeModel->create($name);
            if ($result) {
                $_SESSION['message'] = "Category type created successfully";
                $_SESSION['status'] = "success";
            } else {
                $_SESSION['message'] = "Error creating category type";
                $_SESSION['status'] = "danger";
            }
            header('Location: /admin/category-type/list');
            exit;
        }
    }

    public function edit($id) {
        $type = $this->categoryTypeModel->getById($id);
        if (!$type) {
            $_SESSION['message'] = "Category type not found";
            $_SESSION['status'] = "danger";
            header('Location: /admin/category-type/list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';


            $result = $this->categoryTypeModel->update($id, $name);
            if ($result) {
                $_SESSION['message'] = "Category type updated successfully";
                $_SESSION['status'] = "success";
            } else {
                $_SESSION['message'] = "Error updating category type";
                $_SESSION['status'] = "danger";
            }
            header('Location: /admin/category-type/list');
            exit;
        }

        $content = $this->render('admin/category_type/edit', ['type' => $type]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id) {
        $result = $this->categoryTypeModel->delete($id);
        if ($result) {
            $_SESSION['message'] = "Category type deleted successfully";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting category type";
            $_SESSION['status'] = "danger";
        }
        header('Location: /admin/category-type/list');
        exit;
    }
}