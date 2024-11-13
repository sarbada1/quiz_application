<?php

namespace MVC;

use MVC\Models\CategoryModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use PDO;

class Controller {
    protected $pdo;
    public $categoryModel;
    public $quizModel;
    public $programModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->categoryModel = new CategoryModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->programModel = new ProgramModel($pdo);
    }
    protected function render($view, $data = []) {

        extract($data);
        ob_start();
        $viewPath = __DIR__ . '/Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View file not found: $viewPath");
        }
        return ob_get_clean();
    }
    protected function uirender($view, $data = []) {
     
        
        $data['quizzes'] = $this->quizModel->getAll(); 
        $data['programs'] = $this->programModel->getWithCategory(); 
        // $programs = $this->programModel->getWithCategory();
        // $data['categories'] = $this->categoryModel->getTopCategories();

        extract($data);
        ob_start();
        $viewPath = __DIR__ . '/Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View file not found: $viewPath");
        }
        return ob_get_clean();
    }
}
