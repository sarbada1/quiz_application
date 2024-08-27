<?php

require 'vendor/autoload.php';

use MVC\Config\Database;
use MVC\Middleware\AuthMiddleware;
use MVC\Router;

// Database connection setup
$dsn = 'mysql:host=localhost;dbname=quiz_system';
$username = 'root';
$password = 'Root@1234';

$database = new Database($dsn, $username, $password);
$pdo = $database->getPdo();

// Create router with PDO instance
$router = new Router($pdo);

// Load routes from routes.php
$routes = require 'src/routes.php';
$router->loadRoutes($routes);
$router->addMiddleware(AuthMiddleware::class);
if (!class_exists(AuthMiddleware::class)) {
    die("AuthMiddleware class not found. Check your autoloader and file structure.");
}

// Dispatch the request
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);