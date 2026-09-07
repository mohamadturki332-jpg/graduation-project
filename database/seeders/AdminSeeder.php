<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@nctkap.com'],
            [
                'name'       => 'Admin User',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'department' => 'it',
            ],
        );

        User::firstOrCreate(
            ['email' => 'agent@nctkap.com'],
            [
                'name'       => 'Agent User',
                'password'   => Hash::make('password'),
                'role'       => 'agent',
                'department' => 'it',
            ],
        );

        User::firstOrCreate(
            ['email' => 'employee@nctkap.com'],
            [
                'name'       => 'Employee User',
                'password'   => Hash::make('password'),
                'role'       => 'employee',
                'department' => 'operations',
            ],
        );

        // Placeholder owner for tickets that arrive by email from unknown senders.
        User::firstOrCreate(
            ['email' => 'external@nctkap.com'],
            [
                'name'       => 'External Sender',
                'password'   => Hash::make(\Illuminate\Support\Str::random(40)),
                'role'       => 'employee',
                'department' => 'operations',
            ],
        );
    }
}
