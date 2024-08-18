<?php

namespace MVC\Middleware;

class AuthMiddleware {
    public function handle($request, $next) {
        session_start();
        if (!isset($_SESSION['username']) && $request['uri'] !== '/admin/login') {
            header('Location: /admin/login');
            exit();
        }
        return $next($request);
    }
}