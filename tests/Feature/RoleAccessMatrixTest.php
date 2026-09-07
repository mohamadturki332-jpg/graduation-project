<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_guest_is_redirected_to_login_from_protected_routes(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/tickets')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/tickets')->assertRedirect('/login');
        $this->get('/my-tickets')->assertRedirect('/login');
    }

    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_admin_can_reach_admin_routes(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/tickets')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/users/create')->assertOk();
    }

    public function test_admin_is_blocked_from_agent_and_employee_routes(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/tickets')->assertForbidden();
        $this->actingAs($admin)->get('/tickets/mine')->assertForbidden();
        $this->actingAs($admin)->get('/my-tickets')->assertForbidden();
        $this->actingAs($admin)->get('/my-tickets/create')->assertForbidden();
    }

    public function test_agent_can_reach_agent_routes(): void
    {
        $agent = $this->user('agent');
        $ticket = Ticket::factory()->create();

        $this->actingAs($agent)->get('/tickets')->assertOk();
        $this->actingAs($agent)->get('/tickets/mine')->assertOk();
        $this->actingAs($agent)->get("/tickets/{$ticket->id}")->assertOk();
    }

    public function test_agent_is_blocked_from_admin_and_employee_routes(): void
    {
        $agent = $this->user('agent');

        $this->actingAs($agent)->get('/dashboard')->assertForbidden();
        $this->actingAs($agent)->get('/admin/tickets')->assertForbidden();
        $this->actingAs($agent)->get('/admin/users')->assertForbidden();
        $this->actingAs($agent)->get('/my-tickets')->assertForbidden();
        $this->actingAs($agent)->get('/my-tickets/create')->assertForbidden();
    }

    public function test_employee_can_reach_employee_routes(): void
    {
        $employee = $this->user('employee');

        $this->actingAs($employee)->get('/my-tickets')->assertOk();
        $this->actingAs($employee)->get('/my-tickets/create')->assertOk();
    }

    public function test_employee_is_blocked_from_admin_and_agent_routes(): void
    {
        $employee = $this->user('employee');

        $this->actingAs($employee)->get('/dashboard')->assertForbidden();
        $this->actingAs($employee)->get('/admin/tickets')->assertForbidden();
        $this->actingAs($employee)->get('/admin/users')->assertForbidden();
        $this->actingAs($employee)->get('/tickets')->assertForbidden();
        $this->actingAs($employee)->get('/tickets/mine')->assertForbidden();
    }

    public function test_login_page_is_public(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_logout_requires_auth(): void
    {
        $this->post('/logout')->assertRedirect('/login');
    }

    public function test_post_login_redirects_by_role(): void
    {
        // @nctkap.com addresses are OTP-exempt, so these log straight in.
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'matrix-admin@nctkap.com', 'password' => bcrypt('password')]);
        $agent = User::factory()->create(['role' => 'agent', 'email' => 'matrix-agent@nctkap.com', 'password' => bcrypt('password')]);
        $employee = User::factory()->create(['role' => 'employee', 'email' => 'matrix-employee@nctkap.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->post('/logout');

        $this->post('/login', ['email' => $agent->email, 'password' => 'password'])
            ->assertRedirect('/tickets');
        $this->post('/logout');

        $this->post('/login', ['email' => $employee->email, 'password' => 'password'])
            ->assertRedirect('/my-tickets');
    }
}
