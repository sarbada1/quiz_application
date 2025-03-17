<?php

namespace MVC\Helpers;

use MVC\Config\App;

class UrlHelper {
    public static function url($path = '') {
        // Make sure path starts with a slash if not empty
        if (!empty($path) && substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        
        return App::getBaseUrl() . $path;
    }
}