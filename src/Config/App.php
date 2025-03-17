<?php

namespace MVC\Config;

class App {
    private static $baseUrl;
    
    public static function init() {
        // Detect if we're in production or local development
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        $serverPort = $_SERVER['SERVER_PORT'] ?? '';
        
        // Check if this is a local development environment
        $isLocalhost = in_array($serverName, ['localhost', '127.0.0.1']);
        $isDevelopmentPort = ($serverPort == '3500');
        
        if ($isLocalhost && $isDevelopmentPort) {
            // For local development
            self::$baseUrl = '';  // Empty means no prefix needed
        } else {
            // For production environment
            self::$baseUrl = 'https://tuentrance.com/quiz-play';
        }
        
        // For debugging
        // error_log("Server: $serverName:$serverPort, Using baseUrl: " . self::$baseUrl);
    }
    
    public static function getBaseUrl() {
        if (!isset(self::$baseUrl)) {
            self::init(); // Initialize if not already done
        }
        return self::$baseUrl;
    }
}