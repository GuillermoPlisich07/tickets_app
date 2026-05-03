<?php

namespace App\Services;

use App\Enums\AuthorType;
use App\Enums\TicketStatus;
use App\Exceptions\TicketClosedException;
use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TicketService
{

    public function __construct(private TicketRepositoryInterface $ticketRepository, private MessageRepositoryInterface $messageRepository)
    {

    }

    public function getAll(): Collection
    {
        return $this->ticketRepository->getAll();
    }

    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data){
            $ticket = $this->ticketRepository->create(
                [
                    'title' => $data['title'],
                    'status' => TicketStatus::Open,
                ]
            );

            $this->messageRepository->createForTicket($ticket,
                [
                    'content' => $data['content'],
                    'author' => $data['author'],
                    'author_type' => $data['author_type'],
                    'is_root' => true,
                ]
            );

            return $ticket;
        });
    }

    public function addMessage(Ticket $ticket, array $data): Message
    {
        if($ticket->status->isClosed()){
            throw new TicketClosedException();
        }

        $message = $this->messageRepository->createForTicket($ticket, $data);

        $authorType = AuthorType::from($data['author_type']);
        $this->ticketRepository->updateStatus($ticket, TicketStatus::fromAuthorType($authorType));

        return $message;
    }

    public function updateTicket(Ticket $ticket, array $data): Ticket
    {
        if ($ticket->status->isClosed()) {
            throw new TicketClosedException();
        }
        return $this->ticketRepository->update($ticket, $data);
    }

    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        if ($ticket->status->isClosed()) {
            throw new TicketClosedException();
        }

        return $this->ticketRepository->updateStatus($ticket, $status);
    }

    public function deleteTicket(Ticket $ticket): bool
    {
        return $this->ticketRepository->delete($ticket);
    }
}
