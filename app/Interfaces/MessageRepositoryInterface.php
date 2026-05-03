<?php

namespace App\Interfaces;

use App\Models\Message;
use App\Models\Ticket;

interface MessageRepositoryInterface
{
    public function findById(int $id): Message;

    public function createForTicket(Ticket $ticket, array $data): Message;

    public function update(Message $message, array $data): Message;

    public function delete(Message $message): bool;


}
