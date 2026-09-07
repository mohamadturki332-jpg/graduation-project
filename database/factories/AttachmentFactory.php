<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->word().'.pdf';

        return [
            'ticket_id' => Ticket::factory(),
            'file_path' => 'attachments/'.fake()->uuid().'/'.$name,
            'file_name' => $name,
        ];
    }
}
