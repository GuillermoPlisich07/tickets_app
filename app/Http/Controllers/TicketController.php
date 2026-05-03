<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketStatusRequest;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService){}

    public function index() {
        return new  TicketCollection($this->ticketService->getAll());
    }

    public function show(Ticket $ticket) {
        return new TicketResource($ticket->load('messages'));
    }

    public function store(StoreTicketRequest $request) {
        $ticket = $this->ticketService->createTicket($request->validated());
        return (new TicketResource($ticket))->response()->setStatusCode(201);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket) {
        $ticket = $this->ticketService->updateTicket($ticket, $request->validated());
        return new TicketResource($ticket);
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket) {
        $status = TicketStatus::from($request->validated()['status']);
        $ticket = $this->ticketService->updateStatus($ticket, $status);
        return new TicketResource($ticket);
    }

    public function destroy(Ticket $ticket) {
        $this->ticketService->deleteTicket($ticket);
        return response()->noContent();
    }
}
