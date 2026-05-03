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
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService) {}

    #[OA\Get(
        path: '/api/tickets',
        summary: 'Listar todos los tickets',
        tags: ['Tickets'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de tickets'),
        ]
    )]
    public function index()
    {
        return new TicketCollection($this->ticketService->getAll());
    }

    #[OA\Get(
        path: '/api/tickets/{id}',
        summary: 'Ver un ticket con sus mensajes',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ticket encontrado'),
            new OA\Response(response: 404, description: 'Ticket no encontrado'),
        ]
    )]
    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket->load('messages'));
    }

    #[OA\Post(
        path: '/api/tickets',
        summary: 'Crear un ticket',
        tags: ['Tickets'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content', 'author', 'author_type'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Error en login'),
                    new OA\Property(property: 'content', type: 'string', example: 'No puedo iniciar sesión'),
                    new OA\Property(property: 'author', type: 'string', example: 'Juan Pérez'),
                    new OA\Property(property: 'author_type', type: 'string', enum: ['customer', 'operator'], example: 'customer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ticket creado'),
            new OA\Response(response: 422, description: 'Validación fallida'),
        ]
    )]
    public function store(StoreTicketRequest $request)
    {
        $ticket = $this->ticketService->createTicket($request->validated());
        return (new TicketResource($ticket))->response()->setStatusCode(201);
    }

    #[OA\Patch(
        path: '/api/tickets/{id}',
        summary: 'Editar título de un ticket',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Nuevo título'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ticket actualizado'),
            new OA\Response(response: 422, description: 'Ticket cerrado o validación fallida'),
            new OA\Response(response: 404, description: 'Ticket no encontrado'),
        ]
    )]
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $ticket = $this->ticketService->updateTicket($ticket, $request->validated());
        return new TicketResource($ticket);
    }

    #[OA\Patch(
        path: '/api/tickets/{id}/status',
        summary: 'Cambiar el estado de un ticket',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['open', 'operator_reply', 'customer_reply', 'closed'], example: 'operator_reply'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Estado actualizado'),
            new OA\Response(response: 422, description: 'Transición de estado inválida o ticket cerrado'),
            new OA\Response(response: 404, description: 'Ticket no encontrado'),
        ]
    )]
    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket)
    {
        $status = TicketStatus::from($request->validated()['status']);
        $ticket = $this->ticketService->updateStatus($ticket, $status);
        return new TicketResource($ticket);
    }

    #[OA\Delete(
        path: '/api/tickets/{id}',
        summary: 'Eliminar un ticket',
        tags: ['Tickets'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Ticket eliminado'),
            new OA\Response(response: 404, description: 'Ticket no encontrado'),
        ]
    )]
    public function destroy(Ticket $ticket)
    {
        $this->ticketService->deleteTicket($ticket);
        return response()->noContent();
    }
}
