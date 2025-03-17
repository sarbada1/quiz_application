<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\TagModel;
use PDO;

class TagController extends Controller
{
    private $tagModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->tagModel = new TagModel($pdo);
    }

    public function index()
    {
        $tags = $this->tagModel->getAllTags();
        $content = $this->render('admin/tag/view', ['tags' => $tags]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function showAddForm()
    {
        $content = $this->render('admin/tag/add');
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';

            if (empty($name)) {
                $_SESSION['message'] = "Tag name is required.";
                $_SESSION['status'] = "danger";
                header('Location: /admin/tag/add');
                return;
            }

            $result = $this->tagModel->createTag($name, $slug);

            if ($result) {
                $_SESSION['message'] = "Tag added successfully!";
                $_SESSION['status'] = "success";
            } else {
                $_SESSION['message'] = "Error adding tag";
                $_SESSION['status'] = "danger";
            }
            header('Location: /admin/tag/add');
        }
    }

    public function edit($id)
    {
        $tag = $this->tagModel->getById($id);
        if (!$tag) {
            $_SESSION['message'] = "Tag not found.";
            $_SESSION['status'] = "danger";
            header('Location: /admin/tag/list');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';

            $result = $this->tagModel->updateTag($id, $name, $slug);

            if ($result) {
                $_SESSION['message'] = "Tag updated successfully!";
                $_SESSION['status'] = "success";
            } else {
                $_SESSION['message'] = "Error updating tag";
                $_SESSION['status'] = "danger";
            }
            header('Location: /admin/tag/edit/' . $id);
            return;
        }

        $content = $this->render('admin/tag/edit', ['tag' => $tag]);
        echo $this->render('admin/layout', ['content' => $content]);
    }

    public function delete($id)
    {
        $result = $this->tagModel->deleteTag($id);

        if ($result) {
            $_SESSION['message'] = "Tag deleted successfully!";
            $_SESSION['status'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting tag";
            $_SESSION['status'] = "danger";
        }

        header('Location: /admin/tag/list');
    }
}