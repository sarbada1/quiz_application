<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;

use PDO;



class HeaderController extends Controller
{

    // private $categoryModel;


    public function __construct(PDO $pdo)
    {
  
        $this->categoryModel = new CategoryModel($pdo);
    }

  


}
