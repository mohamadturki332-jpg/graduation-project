<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(3),
            'category' => fake()->randomElement(['network', 'hardware', 'software', 'access_request']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => 'open',
            'user_id' => User::factory()->state(['role' => 'employee']),
            'agent_id' => null,
        ];
    }

    public function assigned(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
            'agent_id' => User::factory()->state(['role' => 'agent']),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'closed',
            'agent_id' => User::factory()->state(['role' => 'agent']),
        ]);
    }
}
