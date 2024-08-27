<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;

use PDO;



class HomeController extends Controller
{

    private $categoryModel;


    public function __construct(PDO $pdo)
    {
  
        $this->categoryModel = new CategoryModel($pdo);
    }

    public function index()
    {
        $categories = $this->categoryModel->getAllCategories();
        $content = $this->render('user/index', ['categories' => $categories]);
        echo $this->render('user/layout', ['content' => $content]);
    }


}
