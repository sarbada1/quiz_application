<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuizModel;
use PDO;



class HomeController extends Controller
{

    public $categoryModel;
    public $quizModel;
    public $programModel;


    public function __construct(PDO $pdo)
    {
  
        $this->categoryModel = new CategoryModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->programModel = new ProgramModel($pdo);
    }

    public function index()
    {
        $categories = $this->quizModel->getAll();
        $programs = $this->programModel->getWithCategory();
        $content = $this->uirender('user/index', ['quizzes' => $categories,'programs'=>$programs]);
       
        echo $this->uirender('user/layout', ['content' => $content]);
    }


}
