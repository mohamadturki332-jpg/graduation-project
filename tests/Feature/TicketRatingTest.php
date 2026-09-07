<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_rate_a_solved_ticket(): void
    {
        $owner = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->closed()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->post(route('my-tickets.rate', $ticket), ['rating' => 5, 'rating_comment' => 'Great help!'])
            ->assertRedirect(route('my-tickets.show', $ticket));

        $ticket->refresh();
        $this->assertSame(5, $ticket->rating);
        $this->assertSame('Great help!', $ticket->rating_comment);
        $this->assertNotNull($ticket->rated_at);
    }

    public function test_re_rating_overwrites_the_previous_rating(): void
    {
        $owner = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->closed()->create([
            'user_id' => $owner->id,
            'rating' => 2,
            'rating_comment' => 'Meh',
        ]);

        $this->actingAs($owner)
            ->post(route('my-tickets.rate', $ticket), ['rating' => 4]);

        $ticket->refresh();
        $this->assertSame(4, $ticket->rating);
        $this->assertNull($ticket->rating_comment);
    }

    public function test_cannot_rate_a_ticket_that_is_not_solved(): void
    {
        $owner = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->assigned()->create(['user_id' => $owner->id]); // in_progress

        $this->actingAs($owner)
            ->post(route('my-tickets.rate', $ticket), ['rating' => 5])
            ->assertForbidden();

        $this->assertNull($ticket->fresh()->rating);
    }

    public function test_cannot_rate_a_solved_ticket_with_no_technician(): void
    {
        $owner = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->create([
            'user_id' => $owner->id,
            'status' => 'closed',
            'agent_id' => null,
        ]);

        $this->actingAs($owner)
            ->post(route('my-tickets.rate', $ticket), ['rating' => 5])
            ->assertForbidden();
    }

    public function test_a_different_employee_cannot_rate_someone_elses_ticket(): void
    {
        $owner = User::factory()->create(['role' => 'employee']);
        $other = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->closed()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->post(route('my-tickets.rate', $ticket), ['rating' => 5])
            ->assertForbidden();

        $this->assertNull($ticket->fresh()->rating);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $owner = User::factory()->create(['role' => 'employee']);
        $ticket = Ticket::factory()->closed()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->post(route('my-tickets.rate', $ticket), ['rating' => 9])
            ->assertSessionHasErrors('rating');

        $this->assertNull($ticket->fresh()->rating);
    }

    public function test_primary_technician_and_admin_see_the_requester_feedback_card(): void
    {
        $primary = User::factory()->create(['role' => 'agent']);
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nctkap.com']);
        $ticket = Ticket::factory()->create([
            'agent_id' => $primary->id,
            'status' => 'closed',
            'rating' => 5,
            'rating_comment' => 'Exactly what I needed',
            'rated_at' => now(),
        ]);

        // The technician who handled the ticket sees the rating left on their work.
        $this->actingAs($primary)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Requester feedback')
            ->assertSee('Exactly what I needed');

        // The admin has oversight of every rating.
        $this->actingAs($admin)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Requester feedback')
            ->assertSee('Exactly what I needed');
    }

    public function test_peer_technician_cannot_see_the_requester_feedback_card(): void
    {
        // A rating is feedback on the handling technician's performance, so a peer
        // who merely opens the ticket for coordination must NOT see it.
        $primary = User::factory()->create(['role' => 'agent']);
        $peer = User::factory()->create(['role' => 'agent']);
        $ticket = Ticket::factory()->create([
            'agent_id' => $primary->id,
            'status' => 'closed',
            'rating' => 5,
            'rating_comment' => 'Exactly what I needed',
            'rated_at' => now(),
        ]);

        $this->actingAs($peer)->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertDontSee('Requester feedback')
            ->assertDontSee('Exactly what I needed');
    }

    public function test_dashboard_leaderboard_ranks_technicians_by_weighted_score(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nctkap.com']);
        $volume = User::factory()->create(['role' => 'agent', 'name' => 'Volume Tech']);
        $rated = User::factory()->create(['role' => 'agent', 'name' => 'Rated Tech']);

        // Volume Tech: 3 solved, no ratings.
        //   normalized resolved = 3/3 = 1.0 ; normalized rating = 0/5 = 0
        //   score = 1.0*0.5 + 0*0.5 = 0.50
        Ticket::factory()->count(3)->create(['agent_id' => $volume->id, 'status' => 'closed']);
        // An in-progress ticket does NOT count toward the resolved total.
        Ticket::factory()->create(['agent_id' => $volume->id, 'status' => 'in_progress']);

        // Rated Tech: 1 solved with a perfect rating.
        //   normalized resolved = 1/3 = 0.33 ; normalized rating = 5/5 = 1.0
        //   score = 0.33*0.5 + 1.0*0.5 = 0.67
        Ticket::factory()->create(['agent_id' => $rated->id, 'status' => 'closed', 'rating' => 5]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $response->assertSee('Technician leaderboard');
        $response->assertSee('Volume Tech');
        $response->assertSee('Rated Tech');

        // The weighted score puts Rated Tech (0.67) ahead of Volume Tech (0.50),
        // even though Volume Tech solved more tickets — productivity and
        // satisfaction are balanced 50/50.
        $body = $response->getContent();
        $this->assertLessThan(strpos($body, 'Volume Tech'), strpos($body, 'Rated Tech'));
    }

    public function test_dashboard_leaderboard_shows_technician_assist_count(): void
    {
        // Assists = tickets a technician collaborated on (shared with) without
        // being the primary. It's a productivity credit shown next to the rating,
        // which stays attributed to the primary technician only.
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nctkap.com']);
        $tech = User::factory()->create(['role' => 'agent', 'name' => 'Helper Tech']);

        // Primary on one resolved ticket, so they appear on the leaderboard...
        Ticket::factory()->create(['agent_id' => $tech->id, 'status' => 'closed']);

        // ...and a collaborator on two other technicians' tickets (two assists).
        $other = User::factory()->create(['role' => 'agent']);
        $t1 = Ticket::factory()->assigned()->create(['agent_id' => $other->id]);
        $t2 = Ticket::factory()->assigned()->create(['agent_id' => $other->id]);
        $t1->collaborators()->attach($tech->id);
        $t2->collaborators()->attach($tech->id);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $response->assertSee('Assists');

        $helper = $response->viewData('leaderboard')->firstWhere('id', $tech->id);
        $this->assertNotNull($helper);
        $this->assertSame(2, (int) $helper->assists_count);
    }

    public function test_technician_ratings_page_shows_average_and_comment(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $employee = User::factory()->create(['role' => 'employee']);

        Ticket::factory()->create([
            'user_id' => $employee->id,
            'agent_id' => $agent->id,
            'status' => 'closed',
            'rating' => 4,
            'rating_comment' => 'Sorted my laptop quickly',
            'rated_at' => now(),
        ]);

        $this->actingAs($agent)
            ->get(route('tickets.ratings'))
            ->assertOk()
            ->assertSee('My ratings')
            ->assertSee('Sorted my laptop quickly');
    }

    public function test_ratings_feedback_excludes_requester_identity(): void
    {
        // Feedback rows must not carry the requester relation or user_id, so the
        // comment can never leak who wrote it.
        $agent = User::factory()->create(['role' => 'agent']);
        $employee = User::factory()->create(['role' => 'employee']);
        Ticket::factory()->create([
            'user_id' => $employee->id,
            'agent_id' => $agent->id,
            'status' => 'closed',
            'rating' => 5,
            'rating_comment' => 'Great',
            'rated_at' => now(),
        ]);

        $view = $this->actingAs($agent)->get(route('tickets.ratings'));
        $feedback = $view->viewData('feedback');

        $this->assertNull($feedback->first()->user_id);
        $this->assertFalse($feedback->first()->relationLoaded('user'));
    }

    public function test_ratings_page_is_agent_only(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'employee']))
            ->get(route('tickets.ratings'))->assertForbidden();
    }

    public function test_admin_ratings_page_lists_technicians_and_feedback(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nctkap.com']);
        $tech = User::factory()->create(['role' => 'agent', 'name' => 'Ranked Tech']);
        $employee = User::factory()->create(['role' => 'employee', 'name' => 'Real Requester']);

        Ticket::factory()->create([
            'user_id' => $employee->id,
            'agent_id' => $tech->id,
            'status' => 'closed',
            'rating' => 5,
            'rating_comment' => 'Excellent and fast',
            'rated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.ratings'))
            ->assertOk()
            ->assertSee('Ranked Tech')
            ->assertSee('Excellent and fast')
            ->assertSee('Real Requester'); // admin sees requester identity
    }

    public function test_admin_ratings_page_is_admin_only(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'agent']))
            ->get(route('admin.ratings'))->assertForbidden();
    }

    public function test_admin_ratings_page_filters_feedback_by_technician(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nctkap.com']);
        $techA = User::factory()->create(['role' => 'agent', 'name' => 'Tech Alpha']);
        $techB = User::factory()->create(['role' => 'agent', 'name' => 'Tech Beta']);

        Ticket::factory()->create(['agent_id' => $techA->id, 'status' => 'closed', 'rating' => 5, 'rating_comment' => 'Alpha was great', 'rated_at' => now()]);
        Ticket::factory()->create(['agent_id' => $techB->id, 'status' => 'closed', 'rating' => 2, 'rating_comment' => 'Beta was slow', 'rated_at' => now()]);

        // Drilling into Tech Alpha shows only their comment.
        $this->actingAs($admin)
            ->get(route('admin.ratings', ['technician' => $techA->id]))
            ->assertOk()
            ->assertSee('Alpha was great')
            ->assertDontSee('Beta was slow');
    }

    public function test_resolving_a_ticket_stamps_resolved_at(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $ticket = Ticket::factory()->create(['agent_id' => $agent->id, 'status' => 'in_progress']);

        $this->assertNull($ticket->resolved_at);

        $this->actingAs($agent)
            ->patch(route('tickets.status', $ticket), ['status' => 'closed'])
            ->assertRedirect();

        $this->assertNotNull($ticket->fresh()->resolved_at);
    }

    public function test_report_page_renders_with_sla_and_is_admin_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nctkap.com']);
        $agent = User::factory()->create(['role' => 'agent', 'name' => 'Report Tech']);

        Ticket::factory()->create([
            'agent_id' => $agent->id,
            'status' => 'closed',
            'created_at' => now()->subHours(3),
            'resolved_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.report'))
            ->assertOk()
            ->assertSee('Operations Report')
            ->assertSee('Resolution time (SLA)')
            ->assertSee('Report Tech');

        // Agents cannot reach the admin report.
        $this->actingAs($agent)->get(route('admin.report'))->assertForbidden();
    }
}
