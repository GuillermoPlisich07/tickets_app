<?php

namespace Tests\Unit;

use App\Enums\TicketStatus;
use App\Exceptions\CannotDeleteRootMessageException;
use App\Exceptions\TicketClosedException;
use App\Interfaces\MessageRepositoryInterface;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\MessageService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    private MessageService $service;
    private MockInterface $messageRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageRepo = Mockery::mock(MessageRepositoryInterface::class);
        $this->service     = new MessageService($this->messageRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_update_message_throws_when_ticket_is_closed(): void
    {
        $ticket = Ticket::make(['status' => TicketStatus::Closed]);

        $message = new Message();
        $message->setRelation('ticket', $ticket);

        $this->expectException(TicketClosedException::class);

        $this->service->updateMessage($message, ['content' => 'Nuevo contenido']);
    }

    public function test_update_message_succeeds_when_ticket_is_open(): void
    {
        $ticket = Ticket::make(['status' => TicketStatus::Open]);

        $message = new Message();
        $message->setRelation('ticket', $ticket);

        $updated = new Message();
        $updated->content = 'Nuevo contenido';

        $this->messageRepo->shouldReceive('update')
            ->once()
            ->with($message, ['content' => 'Nuevo contenido'])
            ->andReturn($updated);

        $result = $this->service->updateMessage($message, ['content' => 'Nuevo contenido']);

        $this->assertEquals('Nuevo contenido', $result->content);
    }

    public function test_delete_message_throws_when_is_root(): void
    {
        $message = new Message();
        $message->is_root = true;

        $this->expectException(CannotDeleteRootMessageException::class);

        $this->service->deleteMessage($message);
    }

    public function test_delete_message_succeeds_when_not_root(): void
    {
        $message = new Message();
        $message->is_root = false;

        $this->messageRepo->shouldReceive('delete')
            ->once()
            ->with($message)
            ->andReturn(true);

        $result = $this->service->deleteMessage($message);

        $this->assertTrue($result);
    }
}
