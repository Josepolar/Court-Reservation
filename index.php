<?php
/**
 * Court Reservation System - Philippine Sports Facility Booking
 * Main entry point - Routes all requests
 */
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session settings (must be before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Start session
session_start();

// Load helper functions
require_once __DIR__ . '/app/helpers.php';

// Load configuration
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Load base Model class first, then additional models (multiple classes in one file)
require_once __DIR__ . '/app/Models/Model.php';
require_once __DIR__ . '/app/Models/Additional.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/',
        __DIR__ . '/app/Models/',
        __DIR__ . '/app/Controllers/',
        __DIR__ . '/app/Services/',
        __DIR__ . '/app/Middleware/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Make database globally available
$GLOBALS['db'] = $db;

// Simple router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/Court-Reservation', '', $uri);
$uri = $uri ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

// Create router instance
$router = new Router();

// Load routes
require_once __DIR__ . '/routes/web.php';
require_once __DIR__ . '/routes/api.php';

// Handle the request
$router->dispatch($uri, $method);
