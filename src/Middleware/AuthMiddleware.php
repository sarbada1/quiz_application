<?php

namespace MVC\Middleware;

class AuthMiddleware {
    public function handle($request, $next) {

        // Allow access to the login page
        if ($request['uri'] === '/admin/login') {
            return $next($request);
        }

        // Check if the URI starts with '/admin/' and redirect if not logged in
        if (!isset($_SESSION['username']) && strpos($request['uri'], '/admin/') === 0) {
            header('Location: /admin/login');
            exit();
        }

        return $next($request);
    }
}