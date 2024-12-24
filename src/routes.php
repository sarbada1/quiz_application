<?php

use MVC\Controllers\AuthController;
use MVC\Controllers\HomeController;
use MVC\Controllers\QuizController;
use MVC\Controllers\LevelController;
use MVC\Controllers\AnswerController;
use MVC\Controllers\ProfileController;
use MVC\Controllers\ProgramController;
use MVC\Controllers\TeacherController;
use MVC\Controllers\CategoryController;
use MVC\Controllers\MockTestController;
use MVC\Controllers\QuestionController;
use MVC\Controllers\QuestionTypeController;
use MVC\Controllers\MockTestAnswerController;
use MVC\Controllers\QuestionImportController;
use MVC\Controllers\QuestionReportController;
use MVC\Controllers\MockTestQuestionController;

return [
    ['route' => '/', 'controller' => HomeController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/profile', 'controller' => ProfileController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/profile', 'controller' => ProfileController::class, 'action' => 'addUserInfo', 'method' => 'POST'],
    ['route' => '/user/register', 'controller' => AuthController::class, 'action' => 'register', 'method' => 'POST'],
    ['route' => '/user/verify-otp', 'controller' => AuthController::class, 'action' => 'verifyOTP', 'method' => 'POST'],
    ['route' => '/user/resend-otp', 'controller' => AuthController::class, 'action' => 'resendOTP', 'method' => 'POST'],
    ['route' => '/user/login', 'controller' => AuthController::class, 'action' => 'loginmodal', 'method' => 'GET'],
    ['route' => '/user/login', 'controller' => AuthController::class, 'action' => 'userlogin', 'method' => 'POST'],
    ['route' => '/user/logout', 'controller' => AuthController::class, 'action' => 'userLogout', 'method' => 'GET'],
    ['route' => '/category/{slug}', 'controller' => CategoryController::class, 'action' => 'showCategory', 'method' => 'GET'],
    ['route' => '/test', 'controller' => ProgramController::class, 'action' => 'showTest', 'method' => 'GET'],
    ['route' => '/test/{slug}', 'controller' => MockTestController::class, 'action' => 'showTestDetail', 'method' => 'GET'],
    ['route' => '/mocktest/{slug}', 'controller' => MockTestQuestionController::class, 'action' => 'showMockTest', 'method' => 'GET'],

    // Add this near the top of routes array, before more specific routes
    [
        'route' => '/review/{attemptId}',
        'controller' => QuizController::class,
        'action' => 'getReview',
        'method' => 'GET'
    ],

    [
        'route' => '/review/{id}',
        'controller' => 'MVC\Controllers\QuizController',
        'action' => 'getReview',
        'method' => 'GET'
    ],
    [
    'route' => '/ajax/save-progress',
    'controller' => MockTestController::class,
    'action' => 'saveProgress',
    'method' => 'POST'
],
[
    'route' => '/ajax/load-progress/{mockTestId}',
    'controller' => MockTestController::class,
    'action' => 'loadProgress',
    'method' => 'GET'
],
[
    'route' => '/ajax/save-progress',
    'controller' => MockTestController::class,
    'action' => 'saveProgress',
    'method' => 'POST'
],
[
    'route' => '/ajax/load-progress/{mockTestId}',
    'controller' => MockTestController::class,
    'action' => 'loadProgress',
    'method' => 'GET'
],
['route' => '/mocktest/register/{mocktestId}', 'controller' => MockTestController::class, 'action' => 'register', 'method' => 'POST'],
    // Quiz routes in correct order - from most specific to least specific
    ['route' => '/quiz/configure', 'controller' => QuizController::class, 'action' => 'configureQuiz', 'method' => 'GET'],
    ['route' => '/quiz/custom', 'controller' => QuizController::class, 'action' => 'startCustomQuiz', 'method' => 'POST'],
    ['route' => '/quiz/submit', 'controller' => QuizController::class, 'action' => 'submitQuiz', 'method' => 'POST'],
    ['route' => '/quiz/history', 'controller' => QuizController::class, 'action' => 'showHistory', 'method' => 'GET'],
    ['route' => '/quiz/{slug}/start/{count}', 'controller' => QuizController::class, 'action' => 'startQuiz', 'method' => 'GET'],
    ['route' => '/quiz/{slug}', 'controller' => QuizController::class, 'action' => 'showQuizDetail', 'method' => 'GET'],
    ['route' => '/quiz', 'controller' => QuizController::class, 'action' => 'showQuiz', 'method' => 'GET'],

    ['route' => '/ajax/quiz-answer', 'controller' => QuizController::class, 'action' => 'checkAnswer', 'method' => 'POST'],

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
    ['route' => '/admin/question/import', 'controller' => QuestionImportController::class, 'action' => 'import', 'method' => 'POST'],
    ['route' => '/admin/question/import', 'controller' => QuestionImportController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/question/import-text', 'controller' => QuestionImportController::class, 'action' => 'importText', 'method' => 'POST'],
    ['route' => '/admin/question/word', 'controller' => QuestionImportController::class, 'action' => 'indexword', 'method' => 'GET'],
    ['route' => '/admin/question/template/download', 'controller' => QuestionImportController::class, 'action' => 'downloadTemplate', 'method' => 'GET'],
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

    ['route' => '/admin/mocktest/list/{id}', 'controller' => MockTestController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/mocktest/add/{id}', 'controller' => MockTestController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/mocktest/add/{id}', 'controller' => MockTestController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/mocktest/edit/{id}', 'controller' => MockTestController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/mocktest/edit/{id}', 'controller' => MockTestController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/mocktest/delete/{id}', 'controller' => MockTestController::class, 'action' => 'delete', 'method' => 'GET'],

    ['route' => '/admin/mocktestquestion/list/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/mocktestquestion/add/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/mocktestquestion/add/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/mocktestquestion/edit/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/mocktestquestion/edit/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/mocktestquestion/delete/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'delete', 'method' => 'GET'],

    ['route' => '/admin/mocktestanswer/list/{id}', 'controller' => MockTestAnswerController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/mocktestanswer/add/{id}', 'controller' => MockTestAnswerController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/mocktestanswer/add/{id}', 'controller' => MockTestAnswerController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/mocktestanswer/edit/{id}', 'controller' => MockTestAnswerController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/mocktestanswer/edit/{id}', 'controller' => MockTestAnswerController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/mocktestanswer/delete/{id}', 'controller' => MockTestAnswerController::class, 'action' => 'delete', 'method' => 'GET'],

    ['route' => '/admin/program/list', 'controller' => ProgramController::class, 'action' => 'list', 'method' => 'GET'],
    ['route' => '/admin/program/edit/{id}', 'controller' => ProgramController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/program/edit/{id}', 'controller' => ProgramController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/program/add', 'controller' => ProgramController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/program/add', 'controller' => ProgramController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/program/delete/{id}', 'controller' => ProgramController::class, 'action' => 'delete', 'method' => 'GET'],
    ['route' => '/admin/mocktest/attempts', 'controller' => MockTestController::class, 'action' => 'showAttempts', 'method' => 'GET'],

    [
        'route' => '/question/report',
        'controller' => QuestionReportController::class,
        'action' => 'submitReport',
        'method' => 'POST'
    ],
    [
        'route' => '/admin/reports',
        'controller' => QuestionReportController::class,
        'action' => 'viewReports',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/reports/update/{id}',
        'controller' => QuestionReportController::class,
        'action' => 'updateReportStatus',
        'method' => 'POST'
    ],

    ['route' => '/ajax/filter-questions/{id}', 'controller' => QuestionController::class, 'action' => 'filterQuestion', 'method' => 'GET'],
    ['route' => '/ajax/toggle-question/{action}/{id}/{mocktestid}', 'controller' => MockTestQuestionController::class, 'action' => 'toggleQuestion', 'method' => 'GET'],
    [
        'route' => '/ajax/submit-answer/{answerid}/{questionid}/{mocktestid}',
        'controller' => MockTestQuestionController::class,
        'action' => 'checkAnswer',
        'method' => 'GET'
    ],
    ['route' => '/ajax/submit-performance', 'controller' => MockTestQuestionController::class, 'action' => 'submitPerformance', 'method' => 'POST'],
    [
        'route' => '/mocktest/restart/{slug}',
        'controller' => MockTestQuestionController::class,
        'action' => 'restartTest',
        'method' => 'GET'
    ],

    // New route for clearing test session
    [
        'route' => '/ajax/clear-test-session/{mocktestid}',
        'controller' => MockTestQuestionController::class,
        'action' => 'clearTestSession',
        'method' => 'GET'
    ],

];
