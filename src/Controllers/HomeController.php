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
        try {
            $categories = $this->quizModel->getAll();
            $programs = $this->programModel->getWithCategory();
            $parentCategories = $this->categoryModel->getParentCategoriesWithChildren();
    
            // Add default description if missing
            foreach ($parentCategories as &$category) {
                if (!isset($category['description'])) {
                    $category['description'] = 'Explore ' . $category['name'] . ' topics and test your knowledge.';
                }
            }
            unset($category); // Break reference
    
            $content = $this->uirender('user/index', [
                'quizzes' => $categories,
                'programs' => $programs,
                'parentCategories' => $parentCategories
            ]);
            
            echo $this->uirender('user/layout', ['content' => $content]);
        } catch (\Exception $e) {
            error_log("Error in HomeController::index - " . $e->getMessage());
            // Handle error appropriately
        }
    }


}
