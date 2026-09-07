<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailTicketIngester
{
    /** Same allow-list the web upload form enforces. */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    /** Real MIME types the extensions above must resolve to (magic-byte check). */
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];

    /** Aliases (Arabic + English) a sender may use to pick a priority. */
    private const PRIORITY_TAGS = [
        'high' => ['high', 'urgent', 'critical', 'عاجل', 'عاجلة', 'عالية', 'عالي', 'مرتفعة', 'مهم'],
        'medium' => ['medium', 'normal', 'متوسطة', 'متوسط', 'عادية', 'عادي'],
        'low' => ['low', 'minor', 'منخفضة', 'منخفض', 'بسيطة', 'بسيط'],
    ];

    /**
     * Plain words scanned in the SUBJECT as a last-resort category guess when
     * the sender used no explicit tag/line. Checked in array order — the more
     * specific buckets first, so e.g. "صلاحية" wins before generic words.
     */
    private const CATEGORY_KEYWORDS = [
        'access_request' => ['صلاحية', 'صلاحيات', 'طلب وصول', 'وصول للنظام', 'حساب جديد', 'access', 'permission'],
        'network' => ['النت', 'شبكة', 'الشبكة', 'انترنت', 'الانترنت', 'إنترنت', 'الإنترنت', 'واي فاي', 'الواي فاي', 'وايفاي', 'راوتر', 'wifi', 'internet', 'network', 'vpn'],
        'hardware' => ['جهاز', 'الجهاز', 'أجهزة', 'اجهزة', 'طابعة', 'الطابعة', 'لابتوب', 'لاب توب', 'اللاب', 'كمبيوتر', 'الكمبيوتر', 'شاشة', 'الشاشة', 'ماوس', 'كيبورد', 'printer', 'laptop', 'computer', 'monitor', 'screen', 'mouse', 'keyboard', 'hardware'],
    ];

    /** Aliases (Arabic + English) a sender may use to pick a category. */
    private const CATEGORY_TAGS = [
        'network' => ['network', 'wifi', 'internet', 'شبكة', 'الشبكة', 'انترنت', 'إنترنت', 'الانترنت', 'الإنترنت', 'النت', 'نت', 'واي فاي', 'الواي فاي'],
        'hardware' => ['hardware', 'device', 'printer', 'أجهزة', 'اجهزة', 'جهاز', 'طابعة', 'عتاد'],
        'software' => ['software', 'program', 'app', 'برامج', 'برمجيات', 'برنامج', 'تطبيق'],
        'access_request' => ['access', 'access request', 'access_request', 'permission', 'وصول', 'صلاحية', 'صلاحيات', 'طلب وصول'],
    ];

    /**
     * @param  array<int, array{name?: string, content?: string, extension?: string}>  $attachments
     */
    public function ingest(string $fromEmail, ?string $subject, ?string $body, ?string $fromName = null, array $attachments = []): Ticket
    {
        $fromEmail = trim($fromEmail);

        $owner = User::whereRaw('LOWER(email) = ?', [strtolower($fromEmail)])->first()
            ?? $this->externalSender();

        // Let the sender pick priority/category from the email itself:
        // subject tags like "[عاجل]" / "[network]", or explicit lines such as
        // "الأهمية: عالية" / "Priority: High" / "Category: Hardware" in the body.
        [$priority, $category] = $this->detectMeta((string) $subject, (string) $body);

        // Recognised [tags] are stripped from the subject so titles stay clean.
        $cleanSubject = $this->stripKnownTags((string) $subject);

        $title = Str::limit(trim($cleanSubject), 250, '');
        if ($title === '') {
            $title = '(no subject)';
        }

        $description = trim((string) $body);
        if ($description === '') {
            $description = '(no body)';
        }

        $fromName = trim((string) $fromName);

        return DB::transaction(function () use ($title, $description, $owner, $fromEmail, $fromName, $attachments, $priority, $category) {
            $ticket = Ticket::create([
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'priority' => $priority,
                'status' => 'open',
                'user_id' => $owner->id,
                'agent_id' => null,
                // Always record the real sender so admins can see who emailed,
                // even when the ticket is attributed to the External Sender.
                'source_email' => $fromEmail !== '' ? $fromEmail : null,
                'source_name' => $fromName !== '' ? $fromName : null,
            ]);

            foreach ($attachments as $attachment) {
                $this->storeAttachment($ticket, $attachment);
            }

            return $ticket;
        });
    }

    /**
     * @param  array{name?: string, content?: string, extension?: string}  $attachment
     */
    private function storeAttachment(Ticket $ticket, array $attachment): void
    {
        $content = $attachment['content'] ?? null;
        if ($content === null || $content === '') {
            return;
        }

        $originalName = trim((string) ($attachment['name'] ?? '')) ?: 'attachment';
        $extension = strtolower((string) ($attachment['extension'] ?? pathinfo($originalName, PATHINFO_EXTENSION)));

        // Mirror the web form: only image/PDF attachments are accepted; anything
        // else (inline signatures, .eml parts, etc.) is silently skipped.
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return;
        }

        // Defence in depth: the web form validates the real MIME type, but email
        // senders are unauthenticated, so verify the actual bytes here too. This
        // stops a renamed HTML/script file (e.g. "invoice.pdf" whose content is
        // HTML) from being stored and later served inline as a stored-XSS payload.
        $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content) ?: '';
        if (! in_array($detectedMime, self::ALLOWED_MIME_TYPES, true)) {
            return;
        }

        // Disk filename is UUID + safe extension only — the original (sender
        // controlled) name is kept in the DB for display/download but never
        // touches the filesystem.
        $diskName = Str::uuid().'.'.$extension;
        $path = "attachments/{$ticket->id}/{$diskName}";

        Storage::disk('local')->put($path, $content);

        Attachment::create([
            'ticket_id' => $ticket->id,
            'file_path' => $path,
            'file_name' => $originalName,
        ]);
    }

    /**
     * Work out the priority + category the sender asked for.
     * Precedence: explicit "label: value" lines, then [bracket] tags in the
     * subject, then the word "urgent/عاجل" anywhere in the subject.
     * Falls back to medium priority and the 'uncategorized' category, so a
     * ticket we couldn't classify is left for a technician to categorise
     * rather than guessed into 'software'.
     *
     * @return array{0: string, 1: string} [$priority, $category]
     */
    private function detectMeta(string $subject, string $body): array
    {
        $priority = null;
        $category = null;

        // Explicit lines — search the subject plus the start of the body.
        $haystack = $subject."\n".Str::limit($body, 3000, '');

        if (preg_match('/(?:priority|(?:ال)?[أا]همية|(?:ال)?[أا]ولوية)\s*[:：]\s*([^\r\n]+)/iu', $haystack, $m)) {
            $priority = $this->matchTag($m[1], self::PRIORITY_TAGS);
        }

        if (preg_match('/(?:category|(?:ال)?فئة|(?:ال)?تصنيف|(?:ال)?نوع)\s*[:：]\s*([^\r\n]+)/iu', $haystack, $m)) {
            $category = $this->matchTag($m[1], self::CATEGORY_TAGS);
        }

        // [Bracket] tags in the subject, e.g. "[عاجل][شبكة] النت مقطوع".
        if (preg_match_all('/[\[(]([^\]\)]{1,30})[\])]/u', $subject, $all)) {
            foreach ($all[1] as $tag) {
                $priority ??= $this->matchTag($tag, self::PRIORITY_TAGS);
                $category ??= $this->matchTag($tag, self::CATEGORY_TAGS);
            }
        }

        // A bare urgency word in the subject still bumps the priority.
        if ($priority === null && preg_match('/urgent|asap|critical|عاجل|طارئ|ضروري/iu', $subject)) {
            $priority = 'high';
        }

        // Last resort: guess the category from plain words in the subject
        // ("الواي فاي مقطوع" → network) — no brackets or labels needed.
        if ($category === null) {
            $category = $this->guessCategoryFromText($subject);
        }

        return [$priority ?? 'medium', $category ?? 'uncategorized'];
    }

    /** Scan free text for known category keywords; first bucket that hits wins. */
    private function guessCategoryFromText(string $text): ?string
    {
        $text = $this->normalizeArabic(mb_strtolower($text));
        if (trim($text) === '') {
            return null;
        }

        foreach (self::CATEGORY_KEYWORDS as $category => $words) {
            foreach ($words as $word) {
                if (mb_strpos($text, $this->normalizeArabic(mb_strtolower($word))) !== false) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Fold Arabic spelling variants so "فأي"/"فاي" or "إنترنت"/"انترنت"
     * compare equal: hamza-alef forms to bare alef, alef maqsura to ya,
     * and strip tatweel.
     */
    private function normalizeArabic(string $text): string
    {
        return str_replace(['أ', 'إ', 'آ', 'ى', 'ـ'], ['ا', 'ا', 'ا', 'ي', ''], $text);
    }

    /** Map a free-text value onto an enum key via its alias list. */
    private function matchTag(string $value, array $map): ?string
    {
        $value = $this->normalizeArabic(mb_strtolower(trim($value)));
        if ($value === '') {
            return null;
        }

        foreach ($map as $key => $aliases) {
            foreach ($aliases as $alias) {
                $alias = $this->normalizeArabic(mb_strtolower($alias));
                if ($value === $alias || str_starts_with($value, $alias.' ') || str_starts_with($value, $alias)) {
                    return $key;
                }
            }
        }

        return null;
    }

    /** Remove recognised [priority]/[category] tags from a subject line. */
    private function stripKnownTags(string $subject): string
    {
        $stripped = preg_replace_callback('/\s*[\[(]([^\]\)]{1,30})[\])]\s*/u', function ($m) {
            $known = $this->matchTag($m[1], self::PRIORITY_TAGS) !== null
                || $this->matchTag($m[1], self::CATEGORY_TAGS) !== null;

            return $known ? ' ' : $m[0];
        }, $subject);

        return trim(preg_replace('/\s{2,}/u', ' ', (string) $stripped));
    }

    private function externalSender(): User
    {
        return User::firstOrCreate(
            ['email' => 'external@nctkap.com'],
            [
                'name' => 'External Sender',
                'password' => Hash::make(Str::random(40)),
                'role' => 'employee',
                'department' => 'operations',
            ],
        );
    }
}
