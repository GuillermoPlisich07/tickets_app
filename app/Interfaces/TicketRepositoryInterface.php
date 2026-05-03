<?php

namespace App\Interfaces;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): Ticket;

    public function create(array $data): Ticket;

    public function update(Ticket $ticket, array $data): Ticket;

    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket;

    public function delete(Ticket $ticket): bool;

}
