<?php

namespace Tests\Feature;

use App\Mail\TicketReplyMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TicketCollaborationTest extends TestCase
{
    use RefreshDatabase;

    private function agent(): User
    {
        return User::factory()->create(['role' => 'agent']);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function employee(): User
    {
        return User::factory()->create(['role' => 'employee']);
    }

    // --- Sharing -----------------------------------------------------------

    public function test_primary_technician_can_share_with_another_agent(): void
    {
        $primary = $this->agent();
        $other = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);

        $this->actingAs($primary)
            ->post("/tickets/{$ticket->id}/share", ['user_id' => $other->id])
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertDatabaseHas('ticket_agent', [
            'ticket_id' => $ticket->id,
            'user_id' => $other->id,
        ]);
    }

    public function test_non_primary_agent_cannot_share(): void
    {
        $primary = $this->agent();
        $intruder = $this->agent();
        $target = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);

        $this->actingAs($intruder)
            ->post("/tickets/{$ticket->id}/share", ['user_id' => $target->id])
            ->assertForbidden();

        $this->assertDatabaseMissing('ticket_agent', ['ticket_id' => $ticket->id]);
    }

    public function test_primary_can_unshare_a_collaborator(): void
    {
        $primary = $this->agent();
        $collaborator = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);
        $ticket->collaborators()->attach($collaborator->id);

        $this->actingAs($primary)
            ->delete("/tickets/{$ticket->id}/share/{$collaborator->id}")
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertDatabaseMissing('ticket_agent', [
            'ticket_id' => $ticket->id,
            'user_id' => $collaborator->id,
        ]);
    }

    // --- Reassignment ------------------------------------------------------

    public function test_admin_can_reassign_and_old_primary_loses_conversation_access(): void
    {
        $oldPrimary = $this->agent();
        $newPrimary = $this->agent();
        $admin = $this->admin();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $oldPrimary->id]);

        $this->actingAs($admin)
            ->patch("/tickets/{$ticket->id}/reassign", ['user_id' => $newPrimary->id])
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertSame($newPrimary->id, $ticket->fresh()->agent_id);

        // The old primary can still view the ticket, but is no longer its
        // technician, so the private requester conversation no longer renders.
        $response = $this->actingAs($oldPrimary)->get("/tickets/{$ticket->id}");
        $response->assertOk();
        $response->assertDontSee('Conversation with requester');
    }

    public function test_reassigning_to_a_collaborator_detaches_them_from_the_pivot(): void
    {
        $primary = $this->agent();
        $collaborator = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);
        $ticket->collaborators()->attach($collaborator->id);

        $this->actingAs($primary)
            ->patch("/tickets/{$ticket->id}/reassign", ['user_id' => $collaborator->id])
            ->assertRedirect();

        $this->assertSame($collaborator->id, $ticket->fresh()->agent_id);
        $this->assertDatabaseMissing('ticket_agent', [
            'ticket_id' => $ticket->id,
            'user_id' => $collaborator->id,
        ]);
    }

    // --- Release back to the queue -----------------------------------------

    public function test_primary_technician_can_release_ticket_back_to_queue(): void
    {
        $primary = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);

        $this->actingAs($primary)
            ->patch("/tickets/{$ticket->id}/release")
            ->assertRedirect("/tickets/{$ticket->id}");

        $ticket->refresh();
        $this->assertNull($ticket->agent_id);
        $this->assertSame('open', $ticket->status);
    }

    public function test_releasing_detaches_collaborators(): void
    {
        $primary = $this->agent();
        $collaborator = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);
        $ticket->collaborators()->attach($collaborator->id);

        $this->actingAs($primary)->patch("/tickets/{$ticket->id}/release")->assertRedirect();

        $this->assertDatabaseMissing('ticket_agent', [
            'ticket_id' => $ticket->id,
            'user_id' => $collaborator->id,
        ]);
    }

    public function test_non_primary_agent_cannot_release(): void
    {
        $primary = $this->agent();
        $peer = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);

        $this->actingAs($peer)->patch("/tickets/{$ticket->id}/release")->assertForbidden();

        $this->assertSame($primary->id, $ticket->fresh()->agent_id);
    }

    public function test_cannot_release_a_terminal_ticket(): void
    {
        $primary = $this->agent();
        $ticket = Ticket::factory()->create(['agent_id' => $primary->id, 'status' => 'closed']);

        $this->actingAs($primary)->patch("/tickets/{$ticket->id}/release")->assertForbidden();

        $this->assertSame($primary->id, $ticket->fresh()->agent_id);
    }

    // --- Admin hand-assign -------------------------------------------------

    public function test_admin_can_hand_assign_an_open_ticket(): void
    {
        $admin = $this->admin();
        $agent = $this->agent();
        $ticket = Ticket::factory()->create(); // open, unassigned

        $this->actingAs($admin)
            ->post("/tickets/{$ticket->id}/admin-assign", ['user_id' => $agent->id])
            ->assertRedirect("/tickets/{$ticket->id}");

        $ticket->refresh();
        $this->assertSame($agent->id, $ticket->agent_id);
        $this->assertSame('in_progress', $ticket->status);
    }

    public function test_admin_cannot_hand_assign_an_already_assigned_ticket(): void
    {
        $admin = $this->admin();
        $primary = $this->agent();
        $other = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $primary->id]);

        $this->actingAs($admin)
            ->post("/tickets/{$ticket->id}/admin-assign", ['user_id' => $other->id])
            ->assertForbidden();

        $this->assertSame($primary->id, $ticket->fresh()->agent_id);
    }

    // --- Least-privilege view ---------------------------------------------

    public function test_agent_can_view_a_peers_ticket_but_not_the_conversation(): void
    {
        $primary = $this->agent();
        $peer = $this->agent();
        $owner = $this->employee();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $primary->id,
            'user_id' => $owner->id,
        ]);

        // A peer technician can open the ticket to see its details and who holds it...
        $response = $this->actingAs($peer)->get("/tickets/{$ticket->id}");
        $response->assertOk();
        $response->assertSee($primary->name);

        // ...but the private requester conversation must not render for them.
        $response->assertDontSee('Conversation with requester');
    }

    public function test_agent_can_view_an_open_unassigned_ticket(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->create(); // open, unassigned

        $this->actingAs($agent)->get("/tickets/{$ticket->id}")->assertOk();
    }

    // --- Requester conversation -------------------------------------------

    public function test_primary_technician_reply_emails_the_requester(): void
    {
        Mail::fake();
        $primary = $this->agent();
        $owner = $this->employee();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $primary->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($primary)
            ->post("/tickets/{$ticket->id}/reply", ['body' => 'Working on it now.'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $primary->id,
            'visibility' => 'reply',
        ]);
        Mail::assertSent(TicketReplyMail::class);
    }

    public function test_collaborator_agent_can_converse_and_emails_requester(): void
    {
        // A collaborator the ticket was shared with is part of the handling team:
        // they can read the requester conversation and reply, and — not being the
        // ticket owner — their reply emails the requester like the primary's does.
        Mail::fake();
        $primary = $this->agent();
        $collaborator = $this->agent();
        $owner = $this->employee();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $primary->id,
            'user_id' => $owner->id,
        ]);
        $ticket->collaborators()->attach($collaborator->id);

        // They see the conversation card on the ticket page.
        $this->actingAs($collaborator)
            ->get("/tickets/{$ticket->id}")
            ->assertOk()
            ->assertSee('Conversation with requester');

        // And they can post to it, which emails the requester.
        $this->actingAs($collaborator)
            ->post("/tickets/{$ticket->id}/reply", ['body' => 'Happy to help.'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $collaborator->id,
            'visibility' => 'reply',
        ]);
        Mail::assertSent(TicketReplyMail::class);
    }

    public function test_peer_technician_cannot_post_to_the_conversation(): void
    {
        // The card is hidden from a peer in the UI; this proves the write endpoint
        // itself rejects them, so the private chat can't be reached via the API.
        Mail::fake();
        $primary = $this->agent();
        $peer = $this->agent();
        $owner = $this->employee();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $primary->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($peer)
            ->post("/tickets/{$ticket->id}/reply", ['body' => 'Peeking in.'])
            ->assertForbidden();

        $this->assertDatabaseMissing('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $peer->id,
        ]);
        Mail::assertNothingSent();
    }

    public function test_demoted_primary_cannot_post_to_the_conversation_after_reassign(): void
    {
        // After a reassign the old primary keeps view access but is no longer the
        // ticket's technician, so the conversation endpoint must reject them too.
        Mail::fake();
        $oldPrimary = $this->agent();
        $newPrimary = $this->agent();
        $owner = $this->employee();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $oldPrimary->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($oldPrimary)
            ->patch("/tickets/{$ticket->id}/reassign", ['user_id' => $newPrimary->id])
            ->assertRedirect();

        $this->actingAs($oldPrimary)
            ->post("/tickets/{$ticket->id}/reply", ['body' => 'Still here?'])
            ->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_employee_reply_posts_but_sends_no_email(): void
    {
        Mail::fake();
        $primary = $this->agent();
        $owner = $this->employee();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $primary->id,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post("/tickets/{$ticket->id}/reply", ['body' => 'Thanks, standing by.'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'visibility' => 'reply',
        ]);
        // The requester posting to their own thread must not trigger an email.
        Mail::assertNotSent(TicketReplyMail::class);
    }
}
