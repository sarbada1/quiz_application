<?php

use MVC\Controllers\TagController;
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
use MVC\Controllers\RealExamController;
use MVC\Controllers\SubjectTestController;
use MVC\Controllers\CategoryTypeController;
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
    [
        'route' => '/ajax/review/{attemptId}',
        'controller' => MockTestQuestionController::class,
        'action' => 'getReview',
        'method' => 'GET'
    ],
    [
        'route' => '/mocktests',
        'controller' => MockTestController::class,
        'action' => 'index',
        'method' => 'GET'
    ],
    [
        'route' => '/mocktest/{id}',
        'controller' => MockTestQuestionController::class,
        'action' => 'showMockTest',
        'method' => 'GET'
    ], // Add these new routes
    ['route' => '/subject/{slug}', 'controller' => SubjectTestController::class, 'action' => 'showSubjectTest', 'method' => 'GET'],
    ['route' => '/test/subject/{id}', 'controller' => MockTestQuestionController::class, 'action' => 'showMockTest', 'method' => 'GET'],
    ['route' => '/test/chapter/{id}', 'controller' => SubjectTestController::class, 'action' => 'startChapterTest', 'method' => 'GET'],
    ['route' => '/ajax/subject/submit-answer/{answerId}/{questionId}/{subjectId}', 'controller' => SubjectTestController::class, 'action' => 'checkAnswer', 'method' => 'GET'],
    ['route' => '/ajax/subject/submit-performance', 'controller' => SubjectTestController::class, 'action' => 'submitPerformance', 'method' => 'POST'],

    ['route' => '/ajax/mocktest/register', 'controller' => MockTestController::class, 'action' => 'mocktestRegister', 'method' => 'POST'],

    [
        'route' => '/ajax/get-review/{attemptId}',
        'controller' => MockTestQuestionController::class,
        'action' => 'getReview',
        'method' => 'GET'
    ],
    [
        'route' => '/review/{id}',
        'controller' => QuizController::class,
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
    ['route' => '/quiz/configure', 'controller' => QuizController::class, 'action' => 'configureQuiz', 'method' => 'GET'],
    ['route' => '/quiz/custom', 'controller' => QuizController::class, 'action' => 'startCustomQuiz', 'method' => 'POST'],
    ['route' => '/quiz/submit', 'controller' => QuizController::class, 'action' => 'submitQuiz', 'method' => 'POST'],
    ['route' => '/quiz/history', 'controller' => QuizController::class, 'action' => 'showHistory', 'method' => 'GET'],
    ['route' => '/quiz/{slug}/start/{count}', 'controller' => QuizController::class, 'action' => 'startQuiz', 'method' => 'GET'],
    ['route' => '/quiz/{slug}', 'controller' => QuizController::class, 'action' => 'showQuizDetail', 'method' => 'GET'],
    ['route' => '/quiz', 'controller' => QuizController::class, 'action' => 'showQuiz', 'method' => 'GET'],

    ['route' => '/exam/list', 'controller' => RealExamController::class, 'action' => 'examList', 'method' => 'GET'],
    ['route' => '/realexam/take/{id}', 'controller' => RealExamController::class, 'action' => 'takeExam', 'method' => 'GET'],
    ['route' => '/api/exam/{id}/status', 'controller' => RealExamController::class, 'action' => 'checkExamStatus', 'method' => 'GET'],
    ['route' => '/api/exam/submit', 'controller' => RealExamController::class, 'action' => 'submitExam', 'method' => 'POST'],
    ['route' => '/api/exam/log-activity', 'controller' => RealExamController::class, 'action' => 'logSuspiciousActivity', 'method' => 'POST'],
    ['route' => '/exam/results/{id}', 'controller' => RealExamController::class, 'action' => 'showResults', 'method' => 'GET'],
    ['route' => '/student/dashboard', 'controller' => RealExamController::class, 'action' => 'studentDashboard', 'method' => 'GET'],

    ['route' => '/api/admin/exam/schedule', 'controller' => RealExamController::class, 'action' => 'scheduleExam', 'method' => 'POST'],
    ['route' => '/admin/exam/results/{id}', 'controller' => RealExamController::class, 'action' => 'viewExamResults', 'method' => 'GET'],
    ['route' => '/admin/exam/student-result/{id}', 'controller' => RealExamController::class, 'action' => 'viewStudentResult', 'method' => 'GET'],
    ['route' => '/api/admin/exam/publish-results', 'controller' => RealExamController::class, 'action' => 'publishResults', 'method' => 'POST'],
    ['route' => '/api/admin/exam/end', 'controller' => RealExamController::class, 'action' => 'endExam', 'method' => 'POST'],
    ['route' => '/admin/realexam/control/:id', 'controller' => RealExamController::class, 'action' => 'adminControlPanel', 'method' => 'GET'],
    ['route' => '/quiz/real-exam/{slug}', 'controller' => QuizController::class, 'action' => 'startRealExam', 'method' => 'GET'],

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
    ['route' => '/admin/quiz/add', 'controller' => QuizController::class, 'action' => 'showForm', 'method' => 'GET'],
    ['route' => '/admin/quiz/add', 'controller' => QuizController::class, 'action' => 'add', 'method' => 'POST'],
    [
        'route' => '/admin/quiz/questions/{id}',
        'controller' => QuizController::class,
        'action' => 'previousYearQuizQuestions',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/quiz/{id}/sets',
        'controller' => QuizController::class,
        'action' => 'showSet',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/quiz/{id}/sets/create',
        'controller' => QuizController::class,
        'action' => 'createSet',
        'method' => 'POST'
    ],
    [
        'route' => '/admin/quiz/sets/{id}/delete',
        'controller' => QuizController::class,
        'action' => 'deleteSet',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/quiz/sets/{id}/publish',
        'controller' => QuizController::class,
        'action' => 'publishSet',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/quiz/sets/{id}/toggle-status',
        'controller' => QuizController::class,
        'action' => 'toggleSetStatus',
        'method' => 'POST'
    ],
    ['route' => '/admin/quiz/configure-mock/{id}', 'controller' => QuizController::class, 'action' => 'showMockConfig', 'method' => 'GET'],
    ['route' => '/admin/quiz/create-real-exam', 'controller' => QuizController::class, 'action' => 'createRealExam', 'method' => 'POST'],
    ['route' => '/admin/quiz/configure-mock/{id}', 'controller' => QuizController::class, 'action' => 'saveMockConfig', 'method' => 'POST'],
    ['route' => '/admin/quiz/update-config', 'controller' => QuizController::class, 'action' => 'updateConfig', 'method' => 'POST'],

    ['route' => '/admin/create/quiz', 'controller' => QuizController::class, 'action' => 'quizList', 'method' => 'GET'],
    ['route' => '/admin/create/mocktest', 'controller' => QuizController::class, 'action' => 'mockTestList', 'method' => 'GET'],
    ['route' => '/admin/create/previous', 'controller' => QuizController::class, 'action' => 'previousList', 'method' => 'GET'],
    ['route' => '/admin/create/real_exam', 'controller' => QuizController::class, 'action' => 'realExamList', 'method' => 'GET'],
    ['route' => '/admin/real_exam/question/{id}', 'controller' => QuestionImportController::class, 'action' => 'indexword', 'method' => 'GET'],
    [
        'route' => '/admin/realexam/add/{id}',
        'controller' => MockTestQuestionController::class,
        'action' => 'showAddForm',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/tag/associate-categories',
        'controller' => TagController::class,
        'action' => 'associateCategories',
        'method' => 'POST'
    ],
    [
        'route' => '/admin/tag/categories/{id}',
        'controller' => TagController::class,
        'action' => 'getCategoriesForTag',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/tag/get-categories-for-tag/{id}',
        'controller' => TagController::class,
        'action' => 'getCategoriesForTag',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/get-categories-by-tags',
        'controller' => CategoryController::class,
        'action' => 'getCategoriesByTags',
        'method' => 'POST'
    ],
    [
    'route' => '/admin/category/get-by-tags',
    'controller' => CategoryController::class,
    'action' => 'getCategoriesByTags',
    'method' => 'POST'
    ],
    [
    'route' => '/admin/question/bulk-manage', 
    'controller' => QuestionController::class, 
    'action' => 'bulkManage', 
    'method' => 'GET'
],
[
    'route' => '/admin/question/bulk-update-category', 
    'controller' => QuestionController::class, 
    'action' => 'bulkUpdateCategory', 
    'method' => 'POST'
],
[
    'route' => '/admin/category/manage-tags/{id}',
    'controller' => CategoryController::class,
    'action' => 'manageTags',
    'method' => 'GET'
],
[
    'route' => '/admin/category/manage-tags/{id}',
    'controller' => CategoryController::class,
    'action' => 'manageTags',
    'method' => 'POST'
],
[
    'route' => '/tag/{slug}',
    'controller' => HomeController::class,
    'action' => 'showTagQuizzes',
    'method' => 'GET'
],
[
    'route' => '/quiz/category/{id}',
    'controller' => QuizController::class,
    'action' => 'categoryQuizzes',
    'method' => 'GET'
],
[
    'route' => '/quiz/category/{id}/start/{count}',
    'controller' => QuizController::class,
    'action' => 'startCategoryQuiz',
    'method' => 'GET'
],
    ['route' => '/admin/quiz/edit/{id}', 'controller' => QuizController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/quiz/edit/{id}', 'controller' => QuizController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/quiz/delete/{id}', 'controller' => QuizController::class, 'action' => 'delete', 'method' => 'GET'],
    ['route' => '/admin/quiz/updateYear/{id}', 'controller' => QuizController::class, 'action' => 'updateYear', 'method' => 'POST'],
    ['route' => '/admin/quiz/updateStudent/{id}', 'controller' => QuizController::class, 'action' => 'updateStudent', 'method' => 'POST'],

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

    [
        'route' => '/admin/mocktest/list',
        'controller' => MockTestController::class,
        'action' => 'index',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/mocktest/list/{id}',
        'controller' => MockTestController::class,
        'action' => 'index',
        'method' => 'GET'
    ],
    ['route' => '/admin/mocktest/add/{id}', 'controller' => MockTestController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/mocktest/add/{id}', 'controller' => MockTestController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/mocktest/edit/{id}', 'controller' => MockTestController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/mocktest/edit/{id}', 'controller' => MockTestController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/mocktest/delete/{id}', 'controller' => MockTestController::class, 'action' => 'delete', 'method' => 'GET'],
    [
        'route' => '/mocktest/set/{id}',
        'controller' => MockTestController::class,
        'action' => 'startSet',
        'method' => 'GET'
    ],
    [
        'route' => '/previous-year-quizzes',
        'controller' => QuizController::class,
        'action' => 'previousYearQuizzes',
        'method' => 'GET'
    ],
    [
        'route' => '/previous-year-quiz/{id}',
        'controller' => QuizController::class,
        'action' => 'previousYearQuiz',
        'method' => 'GET'
    ],

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
    // Add these routes
    ['route' => '/admin/tag/list', 'controller' => TagController::class, 'action' => 'index', 'method' => 'GET'],
    ['route' => '/admin/tag/add', 'controller' => TagController::class, 'action' => 'showAddForm', 'method' => 'GET'],
    ['route' => '/admin/tag/add', 'controller' => TagController::class, 'action' => 'add', 'method' => 'POST'],
    ['route' => '/admin/tag/edit/{id}', 'controller' => TagController::class, 'action' => 'edit', 'method' => 'GET'],
    ['route' => '/admin/tag/edit/{id}', 'controller' => TagController::class, 'action' => 'edit', 'method' => 'POST'],
    ['route' => '/admin/tag/delete/{id}', 'controller' => TagController::class, 'action' => 'delete', 'method' => 'GET'],
    [
        'route' => '/question/report',
        'controller' => QuestionReportController::class,
        'action' => 'submitReport',
        'method' => 'POST'
    ],
    [
        'route' => '/previous_question/report',
        'controller' => QuestionReportController::class,
        'action' => 'submitPreviousReport',
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
    [
        'route' => '/admin/category-type/list',
        'controller' => CategoryTypeController::class,
        'action' => 'index',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/category-type/add',
        'controller' => CategoryTypeController::class,
        'action' => 'showAddForm',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/category-type/add',
        'controller' => CategoryTypeController::class,
        'action' => 'add',
        'method' => 'POST'
    ],
    [
        'route' => '/admin/category-type/edit/{id}',
        'controller' => CategoryTypeController::class,
        'action' => 'edit',
        'method' => 'GET'
    ],
    [
        'route' => '/admin/category-type/edit/{id}',
        'controller' => CategoryTypeController::class,
        'action' => 'edit',
        'method' => 'POST'
    ],
    [
        'route' => '/admin/category-type/delete/{id}',
        'controller' => CategoryTypeController::class,
        'action' => 'delete',
        'method' => 'GET'
    ],
    ['route' => '/ajax/filter-questions/{id}', 'controller' => QuestionController::class, 'action' => 'filterQuestion', 'method' => 'GET'],
    ['route' => '/ajax/toggle-question/{action}/{id}/{mocktestid}', 'controller' => MockTestQuestionController::class, 'action' => 'toggleQuestion', 'method' => 'POST'],
    ['route' => '/ajax/update-category-allocation', 'controller' => QuizController::class, 'action' => 'updateCategoryAllocation', 'method' => 'POST'],
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
    [
        'route' => '/ajax/submit-test',
        'controller' => MockTestQuestionController::class,
        'action' => 'submitTest',
        'method' => 'POST'
    ],
    [
        'route' => '/ajax/save-answer',
        'controller' => MockTestQuestionController::class,
        'action' => 'saveAnswer',
        'method' => 'POST'
    ],
    // New route for clearing test session
    [
        'route' => '/ajax/clear-test-session/{mocktestid}',
        'controller' => MockTestQuestionController::class,
        'action' => 'clearTestSession',
        'method' => 'GET'
    ],

];
