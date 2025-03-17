<?php

namespace MVC;
use MVC\Config\App;
use MVC\Middleware\AuthMiddleware;

class Router {
    protected $routes = [];
    protected $pdo;
    protected $middleware = [];
    protected $baseUrl;


    public function __construct($pdo = null) {
        $this->pdo = $pdo;
        $this->baseUrl = App::getBaseUrl();

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
        $uri = strtok($uri, '?');
        error_log("Attempting to match URI: $uri with method: $method");
        if (!empty($this->baseUrl) && strpos($uri, $this->baseUrl) === 0) {
            $uri = substr($uri, strlen($this->baseUrl));
        }
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
            error_log("Checking route pattern: $pattern against URI: $uri");
            
            if (preg_match($pattern, $uri, $matches) && $route['method'] === $method) {
                error_log("Route matched! Controller: {$route['controller']}, Action: {$route['action']}");
                array_shift($matches); // Remove the full match
                $controllerClass = $route['controller'];
                $action = $route['action'];
    
                try {
                    if ($this->pdo) {
                        $controller = new $controllerClass($this->pdo);
                    } else {
                        $controller = new $controllerClass();
                    }
                    
                    call_user_func_array([$controller, $action], $matches);
                    return;
                } catch (\Exception $e) {
                    error_log("Route execution error: " . $e->getMessage());
                    http_response_code(500);
                    echo json_encode(['error' => 'Internal Server Error']);
                    exit;
                }
            }
        }

        // If no route matches
        
        http_response_code(404);
        include 'src/404.php';
        exit();
    }
    public function generateUrl($path) {
        // Make sure path starts with a slash
        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        
        return $this->baseUrl . $path;
    }
    private function convertRouteToRegex($route) {
        return '#^' . preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route) . '$#';
    }
}