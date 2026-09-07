<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_deleting_employee_also_deletes_their_tickets(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->create(['user_id' => $employee->id]);

        // A comment and an attachment on the ticket must cascade away with it.
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->admin()->id,
            'body' => 'looking into it',
            'visibility' => 'reply',
        ]);
        $attachment = Attachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => "attachments/{$ticket->id}/file.pdf",
            'file_name' => 'file.pdf',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('users.destroy', $employee))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $employee->id]);
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }

    public function test_deleting_agent_releases_active_tickets_back_to_open_queue(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = Ticket::factory()->create([
            'agent_id' => $agent->id,
            'status' => 'in_progress',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('users.destroy', $agent))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $agent->id]);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'agent_id' => null,
            'status' => 'open',
        ]);
    }

    public function test_deleting_agent_keeps_terminal_tickets_terminal(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = Ticket::factory()->create([
            'agent_id' => $agent->id,
            'status' => 'closed',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('users.destroy', $agent))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'agent_id' => null,
            'status' => 'closed',
        ]);
    }

    public function test_deleting_agent_with_comments_and_collaborations_succeeds(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = Ticket::factory()->create();

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'body' => 'note',
            'visibility' => 'internal',
        ]);
        DB::table('ticket_agent')->insert([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('users.destroy', $agent))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $agent->id]);
        $this->assertDatabaseMissing('comments', ['user_id' => $agent->id]);
        $this->assertDatabaseMissing('ticket_agent', ['user_id' => $agent->id]);
    }
}
