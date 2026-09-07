<?php

namespace App\Console\Commands;

use App\Mail\NewTicketAdminNotification;
use App\Mail\TicketReceivedMail;
use App\Models\Ticket;
use App\Models\User;
use App\Services\EmailTicketIngester;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Webklex\IMAP\Facades\Client;

class FetchTicketEmails extends Command
{
    protected $signature = 'tickets:fetch-emails {--interval=0 : If greater than 0, keep polling every N seconds (min 5) instead of a single pass}';

    protected $description = 'Pull unseen messages from the configured IMAP inbox and create tickets from them.';

    public function handle(EmailTicketIngester $ingester): int
    {
        $interval = (int) $this->option('interval');

        // Default (no interval): a single pass that runs once and returns, so the
        // admin "Fetch inbox now" button, the scheduler, and the tests all get an
        // immediate result. This is the original tickets:fetch-emails behaviour.
        if ($interval <= 0) {
            return $this->fetchOnce($ingester);
        }

        // Continuous mode (--interval=N): the near-real-time importer. Keeps
        // polling every N seconds (min 5) and drains the outbound mail queue each
        // cycle, since a background launcher runs this and no separate queue
        // worker exists. (This folded in the old separate tickets:listen command.)
        $interval = max(5, $interval);
        $this->info("Listening for ticket emails — checking every {$interval}s. Press Ctrl+C to stop.");

        while (true) {
            try {
                $this->fetchOnce($ingester);

                Artisan::call('queue:work', [
                    '--stop-when-empty' => true,
                    '--tries' => 3,
                    '--quiet' => true,
                ]);
            } catch (Throwable $e) {
                $this->warn('['.now()->format('H:i:s').'] check failed: '.$e->getMessage());
            }

            sleep($interval);
        }

        return self::SUCCESS;
    }

    /**
     * Run a single inbox pass: connect, import unseen messages as tickets, then
     * disconnect. Returns SUCCESS/FAILURE so a one-off caller gets a result.
     */
    private function fetchOnce(EmailTicketIngester $ingester): int
    {
        $account = config('imap.default', 'default');
        $username = config("imap.accounts.{$account}.username");

        if (empty($username)) {
            $this->info('IMAP not configured (IMAP_USERNAME is blank); skipping.');

            return self::SUCCESS;
        }

        try {
            // Client::account() caches the client per process; inside the
            // long-running --interval polling loop a dead SSL socket would then
            // be reused (and fail) forever. make() builds a FRESH client each run.
            $client = Client::make(config("imap.accounts.{$account}"));
            $client->connect();
        } catch (Throwable $e) {
            $this->error('IMAP connection failed: '.$e->getMessage());
            Log::error('IMAP connection failed', ['exception' => $e]);

            return self::FAILURE;
        }

        $folder = $client->getFolder(config('imap.accounts.'.$account.'.folder', env('IMAP_FOLDER', 'INBOX')));

        if (! $folder) {
            $this->error('IMAP folder not found.');

            return self::FAILURE;
        }

        $messages = $folder->messages()->unseen()->get();

        $count = 0;
        foreach ($messages as $message) {
            try {
                $from = optional($message->getFrom()[0] ?? null);
                $fromEmail = (string) ($from->mail ?? '');
                $fromName = (string) ($from->personal ?? '');

                if ($fromEmail === '') {
                    Log::warning('Skipping IMAP message with no From address', ['uid' => $message->getUid()]);
                    $message->setFlag('Seen');
                    continue;
                }

                // Never turn bounce/delivery-failure or automated no-reply mail
                // into a ticket — they're noise, not support requests. Mark them
                // read so they don't get re-scanned.
                if (self::isAutomatedSender($fromEmail)) {
                    Log::info('Skipping automated/bounce email', [
                        'from' => $fromEmail,
                        'subject' => (string) $message->getSubject(),
                    ]);
                    $message->setFlag('Seen');
                    continue;
                }

                $body = $message->getTextBody();
                if (empty($body)) {
                    $html = $message->getHTMLBody();
                    $body = $html ? trim(strip_tags($html)) : '';
                }

                $attachments = [];
                foreach ($message->getAttachments() as $attachment) {
                    $attachments[] = [
                        'name'      => (string) $attachment->getName(),
                        'content'   => (string) $attachment->getContent(),
                        'extension' => (string) $attachment->getExtension(),
                    ];
                }

                $ticket = $ingester->ingest($fromEmail, (string) $message->getSubject(), $body, $fromName, $attachments);
                $this->sendAcknowledgement($fromEmail, $ticket);
                $this->notifyAdmins($ticket, $fromEmail, $fromName);
                $message->setFlag('Seen');
                $count++;
            } catch (Throwable $e) {
                Log::error('Failed to ingest IMAP message', [
                    'uid' => method_exists($message, 'getUid') ? $message->getUid() : null,
                    'exception' => $e,
                ]);
                $this->warn('Skipped a message due to error: '.$e->getMessage());
            }
        }

        // Close the socket politely so Gmail doesn't accumulate half-dead
        // connections from the 5-second polling loop.
        try {
            $client->disconnect();
        } catch (Throwable) {
            // Ignore — the connection may already be gone.
        }

        $this->info("Imported {$count} ticket(s).");

        return self::SUCCESS;
    }

    /**
     * True for bounce / delivery-failure / automated no-reply senders that
     * should never become tickets or receive an acknowledgement.
     */
    public static function isAutomatedSender(string $email): bool
    {
        $lower = strtolower(trim($email));

        return str_contains($lower, 'mailer-daemon')
            || str_contains($lower, 'postmaster')
            || str_contains($lower, 'no-reply')
            || str_contains($lower, 'noreply')
            // The synthetic placeholder used to attribute unknown senders — it is
            // never a real person, so it must never spawn a ticket or an ack.
            // (Real @nctkap.com employees are NOT matched here so their emails
            // still become tickets.)
            || $lower === 'external@nctkap.com';
    }

    /**
     * Email the sender a confirmation that their message became a ticket.
     * Skips automated/placeholder addresses to avoid mail loops, and never
     * lets a delivery failure abort the import.
     */
    private function sendAcknowledgement(string $email, Ticket $ticket): void
    {
        $email = trim($email);
        $lower = strtolower($email);

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        // NEVER reply to our own inbox/from address — an ack landing back in the
        // helpdesk mailbox would be re-imported as a ticket, then acked again,
        // looping forever. (The account emailing itself is not a real request.)
        $account = config('imap.default', 'default');
        $selfAddresses = array_filter(array_map(
            fn ($a) => strtolower(trim((string) $a)),
            [config("imap.accounts.{$account}.username"), config('mail.from.address')]
        ));

        if (in_array($lower, $selfAddresses, true) || self::isAutomatedSender($email)) {
            return;
        }

        try {
            Mail::to($email)->send(new TicketReceivedMail($ticket));
        } catch (Throwable $e) {
            Log::warning('Failed to send ticket acknowledgement', [
                'ticket' => $ticket->id,
                'to' => $email,
                'error' => $e->getMessage(),
            ]);
            $this->warn("Ack email to {$email} failed: ".$e->getMessage());
        }
    }

    /**
     * Notify every admin that a new ticket arrived by email. A delivery
     * failure is logged but never aborts the import.
     */
    private function notifyAdmins(Ticket $ticket, string $senderEmail, ?string $senderName): void
    {
        // A configured real inbox wins over the seeded admin emails, which are
        // often non-deliverable .local addresses.
        $notify = config('mail.admin_notify_address');
        $admins = $notify
            ? [$notify]
            : User::where('role', 'admin')->pluck('email')->filter()->all();

        if (empty($admins)) {
            return;
        }

        try {
            Mail::to($admins)->send(new NewTicketAdminNotification($ticket, $senderEmail, $senderName ?: null));
        } catch (Throwable $e) {
            Log::warning('Failed to send admin new-ticket notification', [
                'ticket' => $ticket->id,
                'error' => $e->getMessage(),
            ]);
            $this->warn('Admin notification failed: '.$e->getMessage());
        }
    }
}
