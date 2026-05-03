<?php

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class TicketRepository implements TicketRepositoryInterface
{
    #[\Override]
    public function getAll(): Collection
    {
        return Ticket::all();
    }

    #[\Override]
    public function findById(int $id): Ticket
    {
        return Ticket::findOrFail($id);
    }

    #[\Override]
    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    #[\Override]
    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update($data);
        return $ticket;
    }

    #[\Override]
    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        $ticket->status = $status;
        $ticket->save();
        return $ticket;
    }

    #[\Override]
    public function delete(Ticket $ticket): bool
    {
        return $ticket->delete();
    }


}
