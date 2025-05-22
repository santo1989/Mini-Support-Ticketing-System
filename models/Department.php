<?php
require_once __DIR__ . '/../config/database.php';

class Department
{
private $pdo;
public function getAll() {
    $stmt = $this->pdo->query("SELECT * FROM departments");
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

public function find($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

public function findByName($name) {
    $stmt = $this->pdo->prepare("SELECT * FROM departments WHERE name = ?");
    $stmt->execute([$name]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

public function create($name) {
    $stmt = $this->pdo->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->execute([$name]);
    return $this->pdo->lastInsertId();
}

public function update($id, $name) {
    $stmt = $this->pdo->prepare("UPDATE departments SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
    return $stmt->rowCount();
}

public function delete($id) {
    $stmt = $this->pdo->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->rowCount();
}

}