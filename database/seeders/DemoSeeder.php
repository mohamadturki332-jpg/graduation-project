<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $agents = collect([
            ['name' => 'Tariq Al-Sharif', 'email' => 'tariq@nctkap.com', 'department' => 'it'],
            ['name' => 'Bandar Al-Dosari', 'email' => 'bandar@nctkap.com', 'department' => 'it'],
            ['name' => 'Reem Al-Faisal',   'email' => 'reem@nctkap.com',   'department' => 'support'],
        ])->map(fn ($a) => User::firstOrCreate(
            ['email' => $a['email']],
            ['name' => $a['name'], 'password' => Hash::make('password'), 'role' => 'agent', 'department' => $a['department']],
        ));

        $employees = collect([
            ['name' => 'Nawaf Al-Shehri', 'email' => 'nawaf@nctkap.com', 'department' => 'finance'],
            ['name' => 'Maha Al-Juhani',  'email' => 'maha@nctkap.com',  'department' => 'hr'],
            ['name' => 'Saud Al-Amri',    'email' => 'saud@nctkap.com',  'department' => 'sales'],
            ['name' => 'Hind Al-Rashid',  'email' => 'hind@nctkap.com',  'department' => 'operations'],
            ['name' => 'Turki Al-Malki',  'email' => 'turki.m@nctkap.com', 'department' => 'operations'],
        ])->map(fn ($e) => User::firstOrCreate(
            ['email' => $e['email']],
            ['name' => $e['name'], 'password' => Hash::make('password'), 'role' => 'employee', 'department' => $e['department']],
        ));

        // Idempotency: only create demo tickets the first time. Detected via a
        // sentinel title — re-running the seeder won't duplicate the dataset.
        if (Ticket::where('title', 'VPN connection drops every 30 minutes')->exists()) {
            return;
        }

        $tickets = [
            ['title' => 'VPN connection drops every 30 minutes',           'category' => 'network',        'priority' => 'high',   'status' => 'open',        'agent' => null],
            ['title' => 'Cannot access the shared drive',                  'category' => 'access_request', 'priority' => 'medium', 'status' => 'open',        'agent' => null],
            ['title' => 'Printer on 3rd floor jamming constantly',         'category' => 'hardware',       'priority' => 'low',    'status' => 'open',        'agent' => null],
            ['title' => 'Outlook crashes when opening attachments',        'category' => 'software',       'priority' => 'high',   'status' => 'open',        'agent' => null],
            ['title' => 'Need access to finance reporting tool',           'category' => 'access_request', 'priority' => 'low',    'status' => 'open',        'agent' => null],

            ['title' => 'Laptop battery swelling — needs replacement',     'category' => 'hardware',       'priority' => 'high',   'status' => 'in_progress', 'agent' => 0],
            ['title' => 'Slack integration with Jira broken',              'category' => 'software',       'priority' => 'medium', 'status' => 'in_progress', 'agent' => 0],
            ['title' => 'Wi-Fi very slow in conference room B',            'category' => 'network',        'priority' => 'medium', 'status' => 'in_progress', 'agent' => 1],
            ['title' => 'Windows update keeps failing',                    'category' => 'software',       'priority' => 'low',    'status' => 'in_progress', 'agent' => 1],
            ['title' => 'Need admin rights for local Docker setup',        'category' => 'access_request', 'priority' => 'medium', 'status' => 'in_progress', 'agent' => 2],

            ['title' => 'Monitor flickering — replaced and resolved',      'category' => 'hardware',       'priority' => 'medium', 'status' => 'closed',      'agent' => 0],
            ['title' => 'Forgot password for staging environment',         'category' => 'access_request', 'priority' => 'low',    'status' => 'closed',      'agent' => 1],
            ['title' => 'Email signature not loading',                     'category' => 'software',       'priority' => 'low',    'status' => 'closed',      'agent' => 2],
            ['title' => 'Old keyboard not working — replaced with new',    'category' => 'hardware',       'priority' => 'low',    'status' => 'closed',      'agent' => 0],
        ];

        foreach ($tickets as $i => $t) {
            Ticket::create([
                'title' => $t['title'],
                'description' => "Demo ticket — {$t['title']}.\n\nReported via the help desk portal as a sample dataset for the dashboard and queue views.",
                'category' => $t['category'],
                'priority' => $t['priority'],
                'status' => $t['status'],
                'user_id' => $employees[$i % $employees->count()]->id,
                'agent_id' => is_null($t['agent']) ? null : $agents[$t['agent']]->id,
            ]);
        }
    }
}
