<?php
require_once __DIR__ . '/../config/database.php';

class TicketNote
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function create($ticketId, $userId, $note)
    {
        $stmt = $this->pdo->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)");
        $stmt->execute([$ticketId, $userId, $note]);
        return $this->pdo->lastInsertId();
    }
    public function getByTicket($ticketId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ticket_notes WHERE ticket_id = ?");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ticket_notes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM ticket_notes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function update($id, $note)
    {
        $stmt = $this->pdo->prepare("UPDATE ticket_notes SET note = ? WHERE id = ?");
        return $stmt->execute([$note, $id]);
    }

}