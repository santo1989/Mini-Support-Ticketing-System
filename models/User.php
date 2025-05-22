<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function register($name, $email, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $passwordHash]);
        return $this->pdo->lastInsertId();
    }

    // public function findByEmail($email)
    // {
    //     $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
    //     $stmt->execute([$email]);
    //     return $stmt->fetch(PDO::FETCH_OBJ);
    // }

    // Additional methods: findById, updateRole, etc.

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
