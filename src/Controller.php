<?php

namespace MVC;

class Controller {
    protected function render($view, $data = []) {
        extract($data);
        ob_start();
        $viewPath = __DIR__ . '/Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("View file not found: $viewPath");
        }
        return ob_get_clean();
    }
}
