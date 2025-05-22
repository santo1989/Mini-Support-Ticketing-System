<?php
require_once __DIR__ . '/../config/database.php';

class ApiToken
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public static function validateToken($token)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM api_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch(PDO::FETCH_OBJ);
        if ($tokenData) {
            $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $userStmt->execute([$tokenData->user_id]);
            return $userStmt->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    public function createToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $this->pdo->prepare("INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expiresAt]);
        return $token;
    }

    // public function deleteToken($token)
    // {
    //     $stmt = $this->pdo->prepare("DELETE FROM api_tokens WHERE token = ?");
    //     return $stmt->execute([$token]);
    // }

    public function deleteToken($token)
    {
        $stmt = $this->pdo->prepare("DELETE FROM api_tokens WHERE token = ?");
        return $stmt->execute([$token]);
    }
}
