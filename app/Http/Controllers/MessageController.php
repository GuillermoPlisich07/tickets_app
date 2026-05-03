<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\MessageService;
use App\Services\TicketService;
use OpenApi\Attributes as OA;

class MessageController extends Controller
{
    public function __construct(private TicketService $ticketService, private MessageService $messageService) {}

    #[OA\Post(
        path: '/api/tickets/{ticket}/messages',
        summary: 'Agregar un mensaje a un ticket',
        tags: ['Messages'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content', 'author', 'author_type'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'Hola, necesito ayuda'),
                    new OA\Property(property: 'author', type: 'string', example: 'Juan Pérez'),
                    new OA\Property(property: 'author_type', type: 'string', enum: ['customer', 'operator'], example: 'customer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Mensaje creado'),
            new OA\Response(response: 422, description: 'Ticket cerrado o validación fallida'),
            new OA\Response(response: 404, description: 'Ticket no encontrado'),
        ]
    )]
    public function store(StoreMessageRequest $request, Ticket $ticket)
    {
        $message = $this->ticketService->addMessage($ticket, $request->validated());
        return (new MessageResource($message))->response()->setStatusCode(201);
    }

    #[OA\Patch(
        path: '/api/tickets/{ticket}/messages/{message}',
        summary: 'Editar un mensaje',
        tags: ['Messages'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'message', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'Mensaje actualizado'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Mensaje actualizado'),
            new OA\Response(response: 422, description: 'Ticket cerrado o validación fallida'),
            new OA\Response(response: 404, description: 'Mensaje no encontrado'),
        ]
    )]
    public function update(UpdateMessageRequest $request, Ticket $ticket, Message $message)
    {
        $message = $this->messageService->updateMessage($message, $request->validated());
        return new MessageResource($message);
    }

    #[OA\Delete(
        path: '/api/tickets/{ticket}/messages/{message}',
        summary: 'Eliminar un mensaje',
        tags: ['Messages'],
        parameters: [
            new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'message', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Mensaje eliminado'),
            new OA\Response(response: 404, description: 'Mensaje no encontrado'),
        ]
    )]
    public function destroy(Ticket $ticket, Message $message)
    {
        $this->messageService->deleteMessage($message);
        return response()->noContent();
    }
}
