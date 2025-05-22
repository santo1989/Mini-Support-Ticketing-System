<?php
require_once __DIR__ . '/../config/database.php';

class Ticket
{
private $pdo;
    public function getByUser($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getByAgent($agentId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE assigned_agent_id = ?");
        $stmt->execute([$agentId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tickets 
        (title, description, user_id, department_id) 
        VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['user_id'],
            $data['department_id']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function assignAgent($ticketId, $agentId)
    {
        $stmt = $this->pdo->prepare("UPDATE tickets 
        SET assigned_agent_id = ?, status = 'in_progress' 
        WHERE id = ?");
        $stmt->execute([$agentId, $ticketId]);
        return $stmt->rowCount();
    }

    public function updateStatus($ticketId, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $stmt->execute([$status, $ticketId]);
        return $stmt->rowCount();
    }
    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}