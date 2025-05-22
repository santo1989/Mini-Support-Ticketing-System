<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ApiToken.php';
require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/AuthMiddleware.php';

class UsersController
{
    public static function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Validate input
        $user = new User();
        $userId = $user->register($data['name'], $data['email'], $data['password']);
        ApiResponse::success(['userId' => $userId], 'User registered', 201);
    }

    public static function logout()
    {
        $token = AuthMiddleware::getBearerToken();
        error_log("Attempting to logout token: $token");

        $tokenModel = new ApiToken();
        $result = $tokenModel->deleteToken($token);

        error_log("Delete result: " . ($result ? "Success" : "Failed"));
        ApiResponse::success(null, 'Logged out');
    }

  

    public static function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        if (empty($data['email']) || empty($data['password'])) {
            ApiResponse::error('Email and password are required', 400);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user->password_hash)) {
            ApiResponse::error('Invalid credentials', 401);
        }

        $tokenModel = new ApiToken();
        $token = $tokenModel->createToken($user->id);

        ApiResponse::success([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }
}
