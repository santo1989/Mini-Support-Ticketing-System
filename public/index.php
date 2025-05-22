<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';


$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove base path
$basePath = '/Mini-Support-Ticketing-System/public';
$request = str_replace($basePath, '', $request);

// Handle routing
switch ("$method $request") {
    case 'POST /register':
        UsersController::register();
        break;
    case 'POST /login':
        UsersController::login();
        break;
    default:
        ApiResponse::error('Endpoint not found', 404);
}
