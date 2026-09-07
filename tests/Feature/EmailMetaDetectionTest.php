<?php

namespace Tests\Feature;

use App\Services\EmailTicketIngester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailMetaDetectionTest extends TestCase
{
    use RefreshDatabase;

    private function ingest(string $subject, string $body = 'some body')
    {
        return app(EmailTicketIngester::class)->ingest('sender@example.com', $subject, $body);
    }

    public function test_arabic_urgent_tag_sets_high_priority_and_is_stripped_from_title(): void
    {
        $ticket = $this->ingest('[عاجل] الطابعة لا تعمل');

        $this->assertSame('high', $ticket->priority);
        $this->assertSame('الطابعة لا تعمل', $ticket->title);
    }

    public function test_english_bracket_tags_set_priority_and_category(): void
    {
        $ticket = $this->ingest('[network][high] internet down');

        $this->assertSame('high', $ticket->priority);
        $this->assertSame('network', $ticket->category);
        $this->assertSame('internet down', $ticket->title);
    }

    public function test_arabic_explicit_lines_in_body_set_priority_and_category(): void
    {
        $ticket = $this->ingest('مشكلة بالنظام', "وصف المشكلة\nالأهمية: عالية\nالفئة: أجهزة");

        $this->assertSame('high', $ticket->priority);
        $this->assertSame('hardware', $ticket->category);
    }

    public function test_english_explicit_lines_set_priority_and_category(): void
    {
        $ticket = $this->ingest('Printer broken', "It stopped working.\nPriority: low\nCategory: hardware");

        $this->assertSame('low', $ticket->priority);
        $this->assertSame('hardware', $ticket->category);
    }

    public function test_access_request_arabic_alias(): void
    {
        $ticket = $this->ingest('طلب جديد', 'الفئة: صلاحية');

        $this->assertSame('access_request', $ticket->category);
    }

    public function test_bare_urgent_word_in_subject_bumps_priority(): void
    {
        $ticket = $this->ingest('urgent help please');

        $this->assertSame('high', $ticket->priority);
    }

    public function test_plain_email_keeps_defaults(): void
    {
        $ticket = $this->ingest('normal email', 'hello there');

        $this->assertSame('medium', $ticket->priority);
        $this->assertSame('uncategorized', $ticket->category);
        $this->assertSame('normal email', $ticket->title);
    }

    public function test_undetected_category_falls_back_to_uncategorized(): void
    {
        // With no tag, no explicit "Category:" line, and no recognisable keyword,
        // the system must not guess a category — it leaves the ticket
        // 'uncategorized' for a technician to classify.
        $ticket = $this->ingest('اجتماع الفريق بكرة', 'نبي نتفق على موعد الاجتماع');

        $this->assertSame('uncategorized', $ticket->category);
    }

    public function test_unrecognised_bracket_tag_is_left_in_title(): void
    {
        $ticket = $this->ingest('[ERP] login fails');

        $this->assertSame('[ERP] login fails', $ticket->title);
        $this->assertSame('medium', $ticket->priority);
    }

    public function test_plain_wifi_word_in_subject_guesses_network(): void
    {
        $ticket = $this->ingest('الواي فاي مقطوع');

        $this->assertSame('network', $ticket->category);
        $this->assertSame('الواي فاي مقطوع', $ticket->title);
    }

    public function test_plain_laptop_word_in_subject_guesses_hardware(): void
    {
        $ticket = $this->ingest('اللاب توب ما يشتغل');

        $this->assertSame('hardware', $ticket->category);
    }

    public function test_plain_permission_word_in_subject_guesses_access_request(): void
    {
        $ticket = $this->ingest('أحتاج صلاحية على النظام');

        $this->assertSame('access_request', $ticket->category);
    }

    public function test_hamza_spelling_variant_still_guesses_network(): void
    {
        // "فأي" with hamza vs the keyword list's "فاي" — normalization folds them.
        $ticket = $this->ingest('عاجل الواي فأي مقطوع');

        $this->assertSame('network', $ticket->category);
        $this->assertSame('high', $ticket->priority);
    }

    public function test_explicit_tag_beats_subject_keyword_guess(): void
    {
        // Subject mentions الطابعة (hardware word) but the explicit tag wins.
        $ticket = $this->ingest('[شبكة] الطابعة ما تطبع عن بعد');

        $this->assertSame('network', $ticket->category);
    }
}
