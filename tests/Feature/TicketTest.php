<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    // --- index ---

    public function test_can_list_tickets(): void
    {
        Ticket::factory()->count(3)->create();

        $this->getJson('/api/tickets')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_list_tickets_is_empty_when_no_tickets(): void
    {
        $this->getJson('/api/tickets')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    // --- show ---

    public function test_can_show_ticket_with_messages(): void
    {
        $ticket = Ticket::factory()->create();
        Message::factory()->root()->create(['ticket_id' => $ticket->id]);
        Message::factory()->count(2)->create(['ticket_id' => $ticket->id]);

        $this->getJson("/api/tickets/{$ticket->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ticket->id)
            ->assertJsonCount(3, 'data.messages');
    }

    public function test_show_returns_404_for_nonexistent_ticket(): void
    {
        $this->getJson('/api/tickets/999')
            ->assertNotFound();
    }

    // --- store ---

    public function test_can_create_ticket(): void
    {
        $response = $this->postJson('/api/tickets', [
            'title'       => 'Mi problema',
            'content'     => 'Descripcion del problema',
            'author'      => 'Juan',
            'author_type' => 'customer',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Mi problema')
            ->assertJsonPath('data.status', TicketStatus::Open->value);

        $this->assertDatabaseHas('tickets', ['title' => 'Mi problema']);
        $this->assertDatabaseHas('messages', ['is_root' => 1, 'author' => 'Juan']);
    }

    public function test_create_ticket_requires_title(): void
    {
        $this->postJson('/api/tickets', [
            'content'     => 'Descripcion',
            'author'      => 'Juan',
            'author_type' => 'customer',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_create_ticket_requires_content(): void
    {
        $this->postJson('/api/tickets', [
            'title'       => 'Titulo',
            'author'      => 'Juan',
            'author_type' => 'customer',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_create_ticket_requires_valid_author_type(): void
    {
        $this->postJson('/api/tickets', [
            'title'       => 'Titulo',
            'content'     => 'Contenido',
            'author'      => 'Juan',
            'author_type' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['author_type']);
    }

    // --- update ---

    public function test_can_update_ticket_title(): void
    {
        $ticket = Ticket::factory()->create(['title' => 'Titulo viejo']);

        $this->putJson("/api/tickets/{$ticket->id}", ['title' => 'Titulo nuevo'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Titulo nuevo');

        $this->assertDatabaseHas('tickets', ['title' => 'Titulo nuevo']);
    }

    public function test_cannot_update_closed_ticket(): void
    {
        $ticket = Ticket::factory()->closed()->create();

        $this->putJson("/api/tickets/{$ticket->id}", ['title' => 'Nuevo titulo'])
            ->assertUnprocessable();
    }

    // --- updateStatus ---

    public function test_can_update_ticket_status_manually(): void
    {
        $ticket = Ticket::factory()->create();

        $this->patchJson("/api/tickets/{$ticket->id}/status", ['status' => 'closed'])
            ->assertOk()
            ->assertJsonPath('data.status', TicketStatus::Closed->value);

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'closed']);
    }

    public function test_update_status_requires_valid_status(): void
    {
        $ticket = Ticket::factory()->create();

        $this->patchJson("/api/tickets/{$ticket->id}/status", ['status' => 'invalid'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    // --- destroy ---

    public function test_can_delete_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->deleteJson("/api/tickets/{$ticket->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_deleting_ticket_also_deletes_messages(): void
    {
        $ticket = Ticket::factory()->create();
        Message::factory()->count(2)->create(['ticket_id' => $ticket->id]);

        $this->deleteJson("/api/tickets/{$ticket->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('messages', ['ticket_id' => $ticket->id]);
    }
}
