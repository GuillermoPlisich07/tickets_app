<?php

namespace App\Repositories;

use App\Interfaces\MessageRepositoryInterface;
use App\Models\Message;
use App\Models\Ticket;

class MessageRepository implements MessageRepositoryInterface
{
    #[\Override] 
    public function findById(int $id): Message
    {
        return Message::findOrFail($id);
    }

    #[\Override] 
    public function createForTicket(Ticket $ticket, array $data): Message
    {
        $message = new Message($data);
        $message->ticket()->associate($ticket);
        $message->save();

        return $message;
    }

    #[\Override] 
    public function update(Message $message, array $data): Message
    {
        $message->update($data);
        return $message;
    }

    #[\Override] 
    public function delete(Message $message): bool
    {
        return $message->delete();
    }

}
