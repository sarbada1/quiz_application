<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\CategoryModel;
use MVC\Models\ProgramModel;
use MVC\Models\QuestionModel;
use MVC\Models\QuizModel;
use MVC\Models\TagModel;
use PDO;

class HomeController extends Controller
{
    public $categoryModel;
    public $quizModel;
    public $questionModel;
    public $programModel;
    public $tagModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->categoryModel = new CategoryModel($pdo);
        $this->quizModel = new QuizModel($pdo);
        $this->questionModel = new QuestionModel($pdo);
        $this->programModel = new ProgramModel($pdo);
        $this->tagModel = new TagModel($pdo);
    }

    public function index()
    {
        try {
            // Get tags that have questions (instead of categories)
            $tagsWithQuestions = $this->tagModel->getTagsWithQuestions();
            
            // Add some properties for better display
            foreach ($tagsWithQuestions as &$tag) {
                if (!isset($tag['description'])) {
                    $tag['description'] = 'Take quizzes and test your knowledge in ' . $tag['name'] . '.';
                }
                if (!isset($tag['icon'])) {
                    $tag['icon'] = 'fas fa-graduation-cap'; // Default icon
                }
            }
            unset($tag);
            
            $content = $this->uirender('user/index', [
                'tagsWithQuestions' => $tagsWithQuestions
            ]);
            
            echo $this->uirender('user/layout', ['content' => $content]);
        } catch (\Exception $e) {
            error_log("Error in HomeController::index - " . $e->getMessage());
            // Handle error appropriately
        }
    }
    

public function showTagQuizzes($tagSlug)
{
    try {
        // Get tag by slug
        $tag = $this->tagModel->getBySlug($tagSlug);
        
        if (!$tag) {
            $_SESSION['message'] = "Tag not found.";
            $_SESSION['status'] = "danger";
            header('Location: /');
            exit;
        }
        
        // Get categories that are:
        // 1. Associated with this tag in tag_categories table
        // 2. Have questions with this tag
        $categoriesWithQuestions = $this->categoryModel->getCategoriesHierarchyForTag($tag['id']);
        
        // Log the results for debugging
        error_log("DEBUG: Found " . count($categoriesWithQuestions) . " categories for tag '{$tag['name']}'");
        
        // Render the view with the categories
        $content = $this->uirender('user/tag_quizzes', [
            'tag' => $tag,
            'categories' => $categoriesWithQuestions
        ]);
        
        echo $this->uirender('user/layout', ['content' => $content]);
    } catch (\Exception $e) {
        error_log("Error in HomeController::showTagQuizzes - " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['status'] = "danger";
        header('Location: /');
        exit;
    }
}
}