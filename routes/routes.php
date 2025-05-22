<?php
require_once __DIR__ . '/../helpers/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/AdminMiddleware.php';

function handleRoutes($method, $path)
{
    $segments = explode('/', $path);

    // Public routes
    if ($method === 'POST' && $segments[0] === 'register') {
        UsersController::register();
        return;
    }

    if ($method === 'POST' && $segments[0] === 'login') {
        UsersController::login();
        return;
    }

    // Protected routes
    AuthMiddleware::handle();

    if ($method === 'POST' && $segments[0] === 'logout') {
        UsersController::logout();
        return;
    }

    // Admin-only routes
    if ($segments[0] === 'departments') {
        AdminMiddleware::handle();
        DepartmentsController::handleRequest($method, $segments);
        return;
    }

    // Ticket routes
    if ($segments[0] === 'tickets') {
        TicketsController::handleRequest($method, $segments);
        return;
    }

    ApiResponse::error('Endpoint not found', 404);

    // Add this to handleRoutes()
    if ($method === 'POST' && $segments[0] === 'login') {
        UsersController::login();
        return;
    }

    // Protected routes
    AuthMiddleware::handle();

    if ($method === 'POST' && $segments[0] === 'logout') {
        UsersController::logout();
        return;
    }
}
