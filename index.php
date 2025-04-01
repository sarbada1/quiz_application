<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configure session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

require 'vendor/autoload.php';

use MVC\Config\Database;
use MVC\Middleware\AuthMiddleware;
use MVC\Router;
use MVC\Config\App;

// Initialize App config
App::init();
date_default_timezone_set('Asia/Kathmandu');
$serverName = $_SERVER['SERVER_NAME'] ?? '';
$serverPort = $_SERVER['SERVER_PORT'] ?? '';
$isLocalhost = in_array($serverName, ['localhost', '127.0.0.1']);
$isDevelopmentPort = ($serverPort == '3500');

if ($isLocalhost && $isDevelopmentPort) {
    // Local development environment
    $dsn = 'mysql:host=localhost;dbname=quiz_system';
    $username = 'root';
    $password = '';
} else {
    // Production environment
    $dsn = 'mysql:host=localhost;dbname=ybqxhkxdav';
    $username = 'ybqxhkxdav';
    $password = 'tzSukN5Jzu';
}

try {
    $database = new Database($dsn, $username, $password);
    $pdo = $database->getPdo();
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

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