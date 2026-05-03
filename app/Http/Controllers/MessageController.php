<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\MessageService;
use App\Services\TicketService;

class MessageController extends Controller
{
    public function __construct(private TicketService $ticketService, private MessageService $messageService)
    {

    }

    public function store (StoreMessageRequest $request, Ticket $ticket) {
        $message = $this->ticketService->addMessage($ticket, $request->validated());
        return (new MessageResource($message))->response()->setStatusCode(201);
    }

    public function update (UpdateMessageRequest $request, Ticket $ticket, Message $message) {
        $message = $this->messageService->updateMessage($message, $request->validated());
        return new MessageResource($message);
    }

    public function destroy (Ticket $ticket, Message $message) {
        $this->messageService->deleteMessage($message);
        return response()->noContent();
    }
}
