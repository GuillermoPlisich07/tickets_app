<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    // --- store ---

    public function test_can_add_message_to_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson("/api/tickets/{$ticket->id}/messages", [
            'content'     => 'Una respuesta',
            'author'      => 'Operador 1',
            'author_type' => 'operator',
        ])->assertCreated()
            ->assertJsonPath('data.content', 'Una respuesta')
            ->assertJsonPath('data.author_type', 'operator');

        $this->assertDatabaseHas('messages', ['content' => 'Una respuesta']);
    }

    public function test_adding_operator_message_changes_status_to_operator_reply(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson("/api/tickets/{$ticket->id}/messages", [
            'content'     => 'Respuesta del operador',
            'author'      => 'Operador 1',
            'author_type' => 'operator',
        ])->assertCreated();

        $this->assertDatabaseHas('tickets', [
            'id'     => $ticket->id,
            'status' => TicketStatus::OperatorReply->value,
        ]);
    }

    public function test_adding_customer_message_changes_status_to_customer_reply(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson("/api/tickets/{$ticket->id}/messages", [
            'content'     => 'Respuesta del cliente',
            'author'      => 'Cliente 1',
            'author_type' => 'customer',
        ])->assertCreated();

        $this->assertDatabaseHas('tickets', [
            'id'     => $ticket->id,
            'status' => TicketStatus::CustomerReply->value,
        ]);
    }

    public function test_cannot_add_message_to_closed_ticket(): void
    {
        $ticket = Ticket::factory()->closed()->create();

        $this->postJson("/api/tickets/{$ticket->id}/messages", [
            'content'     => 'Intento de mensaje',
            'author'      => 'Juan',
            'author_type' => 'customer',
        ])->assertUnprocessable();
    }

    public function test_add_message_requires_content(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson("/api/tickets/{$ticket->id}/messages", [
            'author'      => 'Juan',
            'author_type' => 'customer',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_add_message_requires_valid_author_type(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson("/api/tickets/{$ticket->id}/messages", [
            'content'     => 'Mensaje',
            'author'      => 'Juan',
            'author_type' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['author_type']);
    }

    // --- update ---

    public function test_can_update_message(): void
    {
        $ticket = Ticket::factory()->create();
        $message = Message::factory()->create(['ticket_id' => $ticket->id]);

        $this->putJson("/api/tickets/{$ticket->id}/messages/{$message->id}", [
            'content' => 'Contenido editado',
        ])->assertOk()
            ->assertJsonPath('data.content', 'Contenido editado');

        $this->assertDatabaseHas('messages', ['id' => $message->id, 'content' => 'Contenido editado']);
    }

    public function test_cannot_update_message_on_closed_ticket(): void
    {
        $ticket = Ticket::factory()->closed()->create();
        $message = Message::factory()->create(['ticket_id' => $ticket->id]);

        $this->putJson("/api/tickets/{$ticket->id}/messages/{$message->id}", [
            'content' => 'Intento de edicion',
        ])->assertUnprocessable();
    }

    public function test_update_message_requires_content(): void
    {
        $ticket = Ticket::factory()->create();
        $message = Message::factory()->create(['ticket_id' => $ticket->id]);

        $this->putJson("/api/tickets/{$ticket->id}/messages/{$message->id}", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    // --- destroy ---

    public function test_can_delete_message(): void
    {
        $ticket = Ticket::factory()->create();
        $message = Message::factory()->create(['ticket_id' => $ticket->id]);

        $this->deleteJson("/api/tickets/{$ticket->id}/messages/{$message->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    public function test_cannot_delete_root_message(): void
    {
        $ticket = Ticket::factory()->create();
        $message = Message::factory()->root()->create(['ticket_id' => $ticket->id]);

        $this->deleteJson("/api/tickets/{$ticket->id}/messages/{$message->id}")
            ->assertUnprocessable();
    }
}
