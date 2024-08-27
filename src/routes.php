<?php

use MVC\Controllers\AnswerController;
use MVC\Controllers\AuthController;
use MVC\Controllers\CategoryController;
use MVC\Controllers\HomeController;
use MVC\Controllers\LevelController;
use MVC\Controllers\QuestionController;
use MVC\Controllers\QuestionTypeController;
use MVC\Controllers\QuizController;
use MVC\Controllers\TeacherController;
use MVC\Middleware\AuthMiddleware;

return [
    ['route' => '/', 'controller' => HomeController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/user/register', 'controller' => AuthController::class, 'action' => 'register', 'method' => 'POST'],
    ['route' => '/user/login', 'controller' => AuthController::class, 'action' => 'userlogin', 'method' => 'POST'],
    ['route' => '/user/logout', 'controller' => AuthController::class, 'action' => 'userLogout', 'method' => 'GET'],
    ['route' => '/category/{slug}', 'controller' => CategoryController::class, 'action' => 'showCategory', 'method' => 'GET'],
    ['route' => '/quiz/{slug}', 'controller' => QuizController::class, 'action' => 'showQuiz', 'method' => 'GET'],
    
    ['route' => '/admin', 'controller' => AuthController::class, 'action' => 'showDashboard', 'method' => 'GET'],
    ['route' => '/admin/login', 'controller' => AuthController::class, 'action' => 'showLoginForm', 'method' => 'GET'],
    ['route' => '/admin/login', 'controller' => AuthController::class, 'action' => 'login', 'method' => 'POST'],
    ['route' => '/admin/logout', 'controller' => AuthController::class, 'action' => 'logout', 'method' => 'GET'],

    ['route' => '/admin/teacher/add', 'controller' => TeacherController::class, 'action' => 'showTeacherForm', 'method' => 'GET'],
    ['route' => '/admin/teacher/add', 'controller' => TeacherController::class, 'action' => 'addTeacher', 'method' => 'POST'],
    ['route' => '/admin/teacher/list', 'controller' => TeacherController::class, 'action' => 'listTeacher', 'method' => 'GET'],
    ['route' => '/admin/teacher/edit/{id}', 'controller' => TeacherController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/teacher/edit/{id}', 'controller' => TeacherController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/teacher/delete/{id}', 'controller' => TeacherController::class, 'action' => 'delete', 'method' => 'GET'],

    ['route' => '/admin/student/list', 'controller' => TeacherController::class, 'action' => 'listStudent', 'method' => 'GET'],

    ['route' => '/admin/category/list', 'controller' => CategoryController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/category/add', 'controller' => CategoryController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/category/add', 'controller' => CategoryController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/category/edit/{id}', 'controller' => CategoryController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/category/edit/{id}', 'controller' => CategoryController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/category/delete/{id}', 'controller' => CategoryController::class, 'action' => 'delete', 'method' => 'GET'],
    
    ['route' => '/admin/questiontype/list', 'controller' => QuestionTypeController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/questiontype/add', 'controller' => QuestionTypeController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/questiontype/add', 'controller' => QuestionTypeController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/questiontype/edit/{id}', 'controller' => QuestionTypeController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/questiontype/edit/{id}', 'controller' => QuestionTypeController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/questiontype/delete/{id}', 'controller' => QuestionTypeController::class, 'action' => 'delete', 'method' => 'GET'],

    ['route' => '/admin/level/list', 'controller' => LevelController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/level/add', 'controller' => LevelController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/level/add', 'controller' => LevelController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/level/edit/{id}', 'controller' => LevelController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/level/edit/{id}', 'controller' => LevelController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/level/delete/{id}', 'controller' => LevelController::class, 'action' => 'delete', 'method' => 'GET'],
    
    ['route' => '/admin/quiz/list', 'controller' => QuizController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/quiz/add', 'controller' => QuizController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/quiz/add', 'controller' => QuizController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/quiz/edit/{id}', 'controller' => QuizController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/quiz/edit/{id}', 'controller' => QuizController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/quiz/delete/{id}', 'controller' => QuizController::class, 'action' => 'delete', 'method' => 'GET'],
    
    ['route' => '/admin/question/list', 'controller' => QuestionController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/question/add', 'controller' => QuestionController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/question/add', 'controller' => QuestionController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/question/edit/{id}', 'controller' => QuestionController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/question/edit/{id}', 'controller' => QuestionController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/question/delete/{id}', 'controller' => QuestionController::class, 'action' => 'delete', 'method' => 'GET'],
    
    ['route' => '/admin/answer/list/{id}', 'controller' => AnswerController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/answer/add/{id}', 'controller' => AnswerController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/answer/add/{id}', 'controller' => AnswerController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/answer/edit/{id}', 'controller' => AnswerController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/answer/edit/{id}', 'controller' => AnswerController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/answer/delete/{id}', 'controller' => AnswerController::class, 'action' => 'delete', 'method' => 'GET'],
];