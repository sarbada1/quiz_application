<?php

use MVC\Controllers\AuthController;
use MVC\Controllers\CategoryController;
use MVC\Controllers\QuestionTypeController;
use MVC\Controllers\QuizController;
use MVC\Controllers\TeacherController;
use MVC\Controllers\UserController;
use MVC\Middleware\AuthMiddleware;

return [
    ['route' => '/', 'controller' => UserController::class, 'action' => 'index', 'method' => 'GET'],
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
    
    ['route' => '/admin/quiz/list', 'controller' => QuizController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/quiz/add', 'controller' => QuizController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/quiz/add', 'controller' => QuizController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/quiz/edit/{id}', 'controller' => QuizController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/quiz/edit/{id}', 'controller' => QuizController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/quiz/delete/{id}', 'controller' => QuizController::class, 'action' => 'delete', 'method' => 'GET'],];