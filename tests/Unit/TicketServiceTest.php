<?php

namespace Tests\Unit;

use App\Enums\AuthorType;
use App\Enums\TicketStatus;
use App\Exceptions\TicketClosedException;
use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\TicketService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    private TicketService $service;
    private MockInterface $ticketRepo;
    private MockInterface $messageRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ticketRepo  = Mockery::mock(TicketRepositoryInterface::class);
        $this->messageRepo = Mockery::mock(MessageRepositoryInterface::class);
        $this->service     = new TicketService($this->ticketRepo, $this->messageRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_add_message_throws_when_ticket_is_closed(): void
    {
        $ticket = Ticket::make(['status' => TicketStatus::Closed]);

        $this->expectException(TicketClosedException::class);

        $this->service->addMessage($ticket, [
            'content'     => 'Mensaje',
            'author'      => 'Juan',
            'author_type' => 'customer',
        ]);
    }

    public function test_add_message_updates_status_to_operator_reply(): void
    {
        $ticket = Ticket::make(['status' => TicketStatus::Open]);

        $message = new Message();

        $this->messageRepo->shouldReceive('createForTicket')->once()->andReturn($message);
        $this->ticketRepo->shouldReceive('updateStatus')
            ->once()
            ->with($ticket, TicketStatus::OperatorReply)
            ->andReturn($ticket);

        $this->service->addMessage($ticket, [
            'content'     => 'Respuesta',
            'author'      => 'Operador',
            'author_type' => AuthorType::Operator->value,
        ]);

        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
    }

    public function test_add_message_updates_status_to_customer_reply(): void
    {
        $ticket = Ticket::make(['status' => TicketStatus::Open]);

        $message = new Message();

        $this->messageRepo->shouldReceive('createForTicket')->once()->andReturn($message);
        $this->ticketRepo->shouldReceive('updateStatus')
            ->once()
            ->with($ticket, TicketStatus::CustomerReply)
            ->andReturn($ticket);

        $this->service->addMessage($ticket, [
            'content'     => 'Consulta',
            'author'      => 'Cliente',
            'author_type' => AuthorType::Customer->value,
        ]);

        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
    }

    public function test_update_ticket_throws_when_closed(): void
    {
        $ticket = Ticket::make(['status' => TicketStatus::Closed]);

        $this->expectException(TicketClosedException::class);

        $this->service->updateTicket($ticket, ['title' => 'Nuevo titulo']);
    }

    public function test_update_status_throws_when_already_closed(): void
    {
        $ticket = new Ticket();
        $ticket->setRawAttributes(['status' => TicketStatus::Closed->value]);

        $this->expectException(TicketClosedException::class);

        $this->service->updateStatus($ticket, TicketStatus::Open);
    }
}
