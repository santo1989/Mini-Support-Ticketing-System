<?php
// require_once __DIR__ . '/../models/ApiToken.php';
// require_once __DIR__ . '/ApiResponse.php';

// class AuthMiddleware
// {
//     public static function handle()
//     {
//         $token = self::getBearerToken();
//         if (!$token) {
//             ApiResponse::error('Unauthorized', 401);
//         }
//         $user = ApiToken::validateToken($token);
//         if (!$user) {
//             ApiResponse::error('Invalid token', 401);
//         }
//         $GLOBALS['user'] = $user;
//     }

//     // private static function getBearerToken()
//     // {
//     //     $headers = getallheaders();
//     //     if (isset($headers['Authorization'])) {
//     //         if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
//     //             return $matches[1];
//     //         }
//     //     }
//     //     return null;
//     // }

//     public function getBearerToken()
//     {
//         $headers = null;
//         if (isset($_SERVER['Authorization'])) {
//             $headers = trim($_SERVER["Authorization"]);
//         } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
//             $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
//         } elseif (function_exists('apache_request_headers')) {
//             $requestHeaders = apache_request_headers();
//             // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
//             $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
//             if (isset($requestHeaders['Authorization'])) {
//                 $headers = trim($requestHeaders['Authorization']);
//             }
//         }
//         if (!empty($headers)) {
//             if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
//                 return $matches[1];
//             }
//         }
//         return null;
//     }
// }


require_once __DIR__ . '/../models/ApiToken.php';
require_once __DIR__ . '/ApiResponse.php';

class AuthMiddleware {
    public static function handle() {
        $token = self::getBearerToken();
        if (!$token) {
            ApiResponse::error('Unauthorized: Missing token', 401);
        }
        
        $user = ApiToken::validateToken($token);
        if (!$user) {
            ApiResponse::error('Invalid or expired token', 401);
        }
        
        $GLOBALS['user'] = $user;
    }

    private static function getBearerToken() {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}