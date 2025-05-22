<?php
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/TicketNote.php';
require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/AuthMiddleware.php';

class TicketsController
{
    public static function handleRequest($method, $segments)
    {
        switch ($method) {
            case 'GET':
                if (empty($segments[1])) {
                    self::getAllTickets();
                } else {
                    $ticketId = $segments[1];
                    if (empty($segments[2])) {
                        self::getTicket($ticketId);
                    } elseif ($segments[2] === 'notes') {
                        self::getTicketNotes($ticketId);
                    }
                }
                break;

            case 'POST':
                if (empty($segments[1])) {
                    self::createTicket();
                } elseif (!empty($segments[1]) && $segments[1] === 'assign') {
                    self::assignToSelf($segments[2]);
                }
                break;

            case 'PUT':
                if (!empty($segments[1])) {
                    $ticketId = $segments[1];
                    if (!empty($segments[2]) && $segments[2] === 'status') {
                        self::updateStatus($ticketId);
                    }
                }
                break;

            case 'DELETE':
                // Add delete logic if needed
                break;

            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }

    private static function getAllTickets()
    {
        global $user;
        $ticket = new Ticket();

        if ($user->role === 'agent') {
            $tickets = $ticket->getByAgent($user->id);
        } else {
            $tickets = $ticket->getByUser($user->id);
        }

        ApiResponse::success($tickets);
    }

    private static function getTicket($id)
    {
        $ticket = new Ticket();
        $result = $ticket->find($id);
        if ($result) {
            ApiResponse::success($result);
        }
        ApiResponse::error('Ticket not found', 404);
    }

    private static function createTicket()
    {
        global $user;
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['title']) || empty($data['description']) || empty($data['department_id'])) {
            ApiResponse::error('Missing required fields', 400);
        }

        $ticket = new Ticket();
        $ticketId = $ticket->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => $user->id,
            'department_id' => $data['department_id']
        ]);

        ApiResponse::success(['ticket_id' => $ticketId], 'Ticket created', 201);
    }

    private static function assignToSelf($ticketId)
    {
        global $user;
        if ($user->role !== 'agent') {
            ApiResponse::error('Only agents can assign tickets', 403);
        }

        $ticket = new Ticket();
        $updated = $ticket->assignAgent($ticketId, $user->id);
        ApiResponse::success(['assigned' => $updated]);
    }

    private static function updateStatus($ticketId)
    {
        global $user;
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['status'])) {
            ApiResponse::error('Status is required', 400);
        }

        $ticket = new Ticket();
        $validStatuses = ['open', 'in_progress', 'closed'];

        if (!in_array($data['status'], $validStatuses)) {
            ApiResponse::error('Invalid status', 400);
        }

        // Verify ownership/assignment
        $existing = $ticket->find($ticketId);
        if ($user->role === 'user' && $existing->user_id !== $user->id) {
            ApiResponse::error('Unauthorized', 403);
        }

        $updated = $ticket->updateStatus($ticketId, $data['status']);
        ApiResponse::success(['affected' => $updated]);
    }

    private static function getTicketNotes($ticketId)
    {
        $note = new TicketNote();
        $notes = $note->getByTicket($ticketId);
        ApiResponse::success($notes);
    }

    // Add methods for adding notes, file uploads, etc.
}
