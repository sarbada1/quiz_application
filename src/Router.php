<?php

namespace MVC;
use MVC\Middleware\AuthMiddleware;

class Router {
    protected $routes = [];
    protected $pdo;
    protected $middleware = [];

    public function __construct($pdo = null) {
        $this->pdo = $pdo;
    }

    public function loadRoutes($routes) {
        foreach ($routes as $route) {
            $this->addRoute($route['route'], $route['controller'], $route['action'], $route['method']);
        }
    }

    public function addRoute($route, $controller, $action, $method = 'GET') {
        $method = strtoupper($method);
        $this->routes[] = [
            'route' => $route,
            'method' => $method,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function addMiddleware($middleware) {
        $this->middleware[] = $middleware;
    }

    public function dispatch($uri) {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // Apply middleware
        $request = ['uri' => $uri, 'method' => $method];
        foreach ($this->middleware as $middlewareClass) {
            $middleware = new $middlewareClass();
            $request = $middleware->handle($request, function($req) { return $req; });
            
            // If middleware redirected, stop dispatch
            if (headers_sent()) {
                return;
            }
        }

        foreach ($this->routes as $route) {
            $pattern = $this->convertRouteToRegex($route['route']);
            if (preg_match($pattern, $uri, $matches) && $route['method'] === $method) {
                array_shift($matches); // Remove the full match
                $controllerClass = $route['controller'];
                $action = $route['action'];

                // Check if controller requires PDO
                if ($this->pdo) {
                    $controller = new $controllerClass($this->pdo);
                } else {
                    $controller = new $controllerClass();
                }
                
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        // If no route matches
        http_response_code(404);
        include 'src/404.php';
        exit();
    }

    private function convertRouteToRegex($route) {
        return '#^' . preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route) . '$#';
    }
}