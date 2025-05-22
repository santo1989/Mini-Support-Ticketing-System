<?php
require_once __DIR__ . '/ApiResponse.php';

class AdminMiddleware
{
    public static function handle()
    {
        global $user;
        if ($user->role !== 'admin') {
            ApiResponse::error('Forbidden', 403);
        }
    }
}
