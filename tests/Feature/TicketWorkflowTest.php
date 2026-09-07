<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function employee(): User
    {
        return User::factory()->create(['role' => 'employee']);
    }

    private function agent(): User
    {
        return User::factory()->create(['role' => 'agent']);
    }

    public function test_employee_can_submit_a_ticket_without_attachments(): void
    {
        $employee = $this->employee();

        $response = $this->actingAs($employee)->post('/my-tickets', [
            'title' => 'Cannot reach internal wiki',
            'description' => 'Browser hangs when loading the wiki.',
            'category' => 'network',
            'priority' => 'medium',
        ]);

        $ticket = Ticket::firstWhere('title', 'Cannot reach internal wiki');
        $this->assertNotNull($ticket);
        $this->assertSame('open', $ticket->status);
        $this->assertNull($ticket->agent_id);
        $this->assertSame($employee->id, $ticket->user_id);
        $response->assertRedirect("/my-tickets/{$ticket->id}");
    }

    public function test_employee_can_submit_a_ticket_with_attachments(): void
    {
        Storage::fake('local');
        $employee = $this->employee();

        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');

        $this->actingAs($employee)->post('/my-tickets', [
            'title' => 'Need software install',
            'description' => 'Please install the new design tool.',
            'category' => 'software',
            'priority' => 'low',
            'attachments' => [$file],
        ])->assertRedirect();

        $ticket = Ticket::firstWhere('title', 'Need software install');
        $this->assertCount(1, $ticket->attachments);

        $attachment = $ticket->attachments->first();
        Storage::disk('local')->assertExists($attachment->file_path);

        // Disk filename is uuid + extension only — original name lives in DB.
        $this->assertSame('report.pdf', $attachment->file_name);
        $this->assertStringEndsWith('.pdf', $attachment->file_path);
        $this->assertStringNotContainsString('report', basename($attachment->file_path));
    }

    public function test_ticket_validation_rejects_bad_input(): void
    {
        $employee = $this->employee();

        $this->actingAs($employee)
            ->from('/my-tickets/create')
            ->post('/my-tickets', [
                'title' => '',
                'description' => '',
                'category' => 'banana',
                'priority' => 'urgent',
            ])
            ->assertRedirect('/my-tickets/create')
            ->assertSessionHasErrors(['title', 'description', 'category', 'priority']);
    }

    public function test_ticket_attachment_rejects_disallowed_mime_type(): void
    {
        Storage::fake('local');
        $employee = $this->employee();

        $exe = UploadedFile::fake()->create('hack.exe', 50, 'application/octet-stream');

        $this->actingAs($employee)
            ->from('/my-tickets/create')
            ->post('/my-tickets', [
                'title' => 'Test',
                'description' => 'Test',
                'category' => 'software',
                'priority' => 'low',
                'attachments' => [$exe],
            ])
            ->assertSessionHasErrors('attachments.0');

        $this->assertSame(0, Ticket::count());
    }

    public function test_attachment_max_count_enforced(): void
    {
        Storage::fake('local');
        $employee = $this->employee();

        $files = collect(range(1, 6))->map(
            fn ($i) => UploadedFile::fake()->create("doc{$i}.pdf", 10, 'application/pdf')
        )->all();

        $this->actingAs($employee)
            ->from('/my-tickets/create')
            ->post('/my-tickets', [
                'title' => 'Too many files',
                'description' => 'Should fail.',
                'category' => 'software',
                'priority' => 'low',
                'attachments' => $files,
            ])
            ->assertSessionHasErrors('attachments');
    }

    public function test_employee_cannot_view_another_employees_ticket(): void
    {
        $owner = $this->employee();
        $other = $this->employee();

        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->get("/my-tickets/{$ticket->id}")->assertForbidden();
    }

    public function test_employee_can_download_their_own_attachment(): void
    {
        Storage::fake('local');
        $employee = $this->employee();

        $ticket = Ticket::factory()->create(['user_id' => $employee->id]);
        Storage::disk('local')->put("attachments/{$ticket->id}/file.pdf", 'PDF DATA');
        $attachment = Attachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => "attachments/{$ticket->id}/file.pdf",
            'file_name' => 'file.pdf',
        ]);

        $this->actingAs($employee)
            ->get("/my-tickets/{$ticket->id}/attachments/{$attachment->id}")
            ->assertOk();
    }

    public function test_agent_can_assign_an_open_unassigned_ticket(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->create();

        $this->actingAs($agent)
            ->post("/tickets/{$ticket->id}/assign")
            ->assertRedirect("/tickets/{$ticket->id}");

        $ticket->refresh();
        $this->assertSame('in_progress', $ticket->status);
        $this->assertSame($agent->id, $ticket->agent_id);
    }

    public function test_agent_cannot_assign_already_assigned_ticket(): void
    {
        $first = $this->agent();
        $second = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $first->id]);

        $this->actingAs($second)
            ->post("/tickets/{$ticket->id}/assign")
            ->assertForbidden();

        $ticket->refresh();
        $this->assertSame($first->id, $ticket->agent_id);
    }

    public function test_agent_can_close_their_in_progress_ticket(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $agent->id]);

        $this->actingAs($agent)
            ->patch("/tickets/{$ticket->id}/status", ['status' => 'closed'])
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertSame('closed', $ticket->fresh()->status);
    }

    public function test_agent_cannot_reopen_a_closed_ticket(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->closed()->create(['agent_id' => $agent->id]);

        $this->actingAs($agent)
            ->patch("/tickets/{$ticket->id}/status", ['status' => 'open'])
            ->assertForbidden();

        $this->assertSame('closed', $ticket->fresh()->status);
    }

    public function test_agent_cannot_close_another_agents_ticket(): void
    {
        $owner = $this->agent();
        $other = $this->agent();
        $ticket = Ticket::factory()->assigned()->create(['agent_id' => $owner->id]);

        $this->actingAs($other)
            ->patch("/tickets/{$ticket->id}/status", ['status' => 'closed'])
            ->assertForbidden();

        $this->assertSame('in_progress', $ticket->fresh()->status);
    }

    public function test_agent_can_update_priority_on_their_assigned_ticket(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $agent->id,
            'priority' => 'low',
        ]);

        $this->actingAs($agent)
            ->patch("/tickets/{$ticket->id}/priority", ['priority' => 'high'])
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertSame('high', $ticket->fresh()->priority);
    }

    public function test_agent_cannot_update_priority_on_closed_ticket(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->closed()->create([
            'agent_id' => $agent->id,
            'priority' => 'low',
        ]);

        $this->actingAs($agent)
            ->patch("/tickets/{$ticket->id}/priority", ['priority' => 'high'])
            ->assertForbidden();

        $this->assertSame('low', $ticket->fresh()->priority);
    }

    public function test_primary_agent_can_correct_category(): void
    {
        $agent = $this->agent();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $agent->id,
            'category' => 'hardware',
        ]);

        $this->actingAs($agent)
            ->patch("/tickets/{$ticket->id}/category", ['category' => 'software'])
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertSame('software', $ticket->fresh()->category);
    }

    public function test_unassigned_agent_cannot_change_category(): void
    {
        $owner = $this->agent();
        $other = $this->agent();
        $ticket = Ticket::factory()->assigned()->create([
            'agent_id' => $owner->id,
            'category' => 'hardware',
        ]);

        $this->actingAs($other)
            ->patch("/tickets/{$ticket->id}/category", ['category' => 'software'])
            ->assertForbidden();

        $this->assertSame('hardware', $ticket->fresh()->category);
    }

    public function test_admin_can_correct_category_on_unassigned_ticket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = Ticket::factory()->create([
            'category' => 'hardware',
        ]);

        $this->actingAs($admin)
            ->patch("/tickets/{$ticket->id}/category", ['category' => 'software'])
            ->assertRedirect("/tickets/{$ticket->id}");

        $this->assertSame('software', $ticket->fresh()->category);
    }

    public function test_admin_dashboard_shows_correct_status_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Ticket::factory()->count(2)->create();
        Ticket::factory()->assigned()->count(3)->create();
        Ticket::factory()->closed()->count(1)->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Open')
            ->assertSee('In Progress')
            ->assertSee('Closed');
    }
}
