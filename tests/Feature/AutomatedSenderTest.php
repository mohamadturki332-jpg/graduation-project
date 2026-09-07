<?php

namespace Tests\Feature;

use App\Console\Commands\FetchTicketEmails;
use Tests\TestCase;

class AutomatedSenderTest extends TestCase
{
    public function test_bounce_and_automated_senders_are_recognised(): void
    {
        $automated = [
            'mailer-daemon@googlemail.com',
            'MAILER-DAEMON@example.com',
            'postmaster@corp.com',
            'no-reply@accounts.google.com',
            'noreply@github.com',
            'external@nctkap.com',
        ];

        foreach ($automated as $email) {
            $this->assertTrue(
                FetchTicketEmails::isAutomatedSender($email),
                "{$email} should be treated as an automated sender"
            );
        }
    }

    public function test_real_people_are_not_treated_as_automated(): void
    {
        $humans = [
            'ahmed@company.com',
            'mohamad.turki@gmail.com',
            'sara@nct.com.sa',
            // A real company employee emailing in must still create a ticket —
            // the @nctkap.com domain itself is NOT an automated-sender signal.
            'omar@nctkap.com',
        ];

        foreach ($humans as $email) {
            $this->assertFalse(
                FetchTicketEmails::isAutomatedSender($email),
                "{$email} should NOT be treated as an automated sender"
            );
        }
    }
}
