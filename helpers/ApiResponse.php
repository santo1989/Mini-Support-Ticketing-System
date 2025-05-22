<?php
class ApiResponse
{
    public static function success($data = null, $message = 'Success', $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public static function error($message = 'Error', $statusCode = 400, $errors = null)
    {
        http_response_code($statusCode);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ]);
        exit;
    }
}
