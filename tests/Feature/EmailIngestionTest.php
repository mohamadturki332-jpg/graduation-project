<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\EmailTicketIngester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailIngestionTest extends TestCase
{
    use RefreshDatabase;

    private const PDF_BYTES = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";

    private function ingester(): EmailTicketIngester
    {
        return app(EmailTicketIngester::class);
    }

    public function test_known_sender_email_is_attributed_to_their_account(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'email' => 'jane@corp.test']);

        $ticket = $this->ingester()->ingest('JANE@corp.test', 'Screen flickers', 'Details here', 'Jane');

        // Sender is matched case-insensitively; the real sender is still recorded.
        $this->assertSame($employee->id, $ticket->user_id);
        $this->assertSame('jane@corp.test', strtolower($ticket->source_email));
        $this->assertSame('open', $ticket->status);
    }

    public function test_unknown_sender_falls_back_to_external_placeholder(): void
    {
        $ticket = $this->ingester()->ingest('stranger@outside.test', 'Help', 'Body', 'Stranger');

        $this->assertDatabaseHas('users', ['email' => 'external@nctkap.com']);
        $this->assertSame('stranger@outside.test', $ticket->source_email);
        $this->assertSame(
            User::whereRaw('LOWER(email) = ?', ['external@nctkap.com'])->value('id'),
            $ticket->user_id
        );
    }

    public function test_valid_pdf_attachment_is_stored(): void
    {
        Storage::fake('local');

        $ticket = $this->ingester()->ingest('user@corp.test', 'Scan', 'See attached', 'User', [
            ['name' => 'scan.pdf', 'content' => self::PDF_BYTES, 'extension' => 'pdf'],
        ]);

        $this->assertCount(1, $ticket->attachments);
        $attachment = $ticket->attachments->first();
        Storage::disk('local')->assertExists($attachment->file_path);

        // Original (sender-controlled) name kept in DB; disk name is uuid + ext only.
        $this->assertSame('scan.pdf', $attachment->file_name);
        $this->assertStringEndsWith('.pdf', $attachment->file_path);
        $this->assertStringNotContainsString('scan', basename($attachment->file_path));
    }

    public function test_attachment_with_disallowed_extension_is_skipped(): void
    {
        Storage::fake('local');

        $ticket = $this->ingester()->ingest('user@corp.test', 'x', 'y', 'User', [
            ['name' => 'malware.exe', 'content' => 'MZ binary payload', 'extension' => 'exe'],
        ]);

        $this->assertCount(0, $ticket->attachments);
    }

    public function test_html_renamed_as_pdf_is_rejected_by_mime_check(): void
    {
        Storage::fake('local');

        // Extension is whitelisted but the bytes are HTML/script — the finfo
        // magic-byte check must reject it so it can never be served as stored XSS.
        $ticket = $this->ingester()->ingest('user@corp.test', 'x', 'y', 'User', [
            ['name' => 'invoice.pdf', 'content' => '<html><body><script>alert(1)</script></body></html>', 'extension' => 'pdf'],
        ]);

        $this->assertCount(0, $ticket->attachments);
    }
}
