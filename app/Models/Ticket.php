<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    const CATEGORIES = ['network', 'hardware', 'software', 'access_request', 'uncategorized'];

    const PRIORITIES = ['low', 'medium', 'high'];

    const STATUSES = ['open', 'in_progress', 'on_hold', 'waiting_for_resources', 'rejected', 'closed', 'resolved'];

    // Terminal states — a ticket in one of these is considered finished: no
    // further status/priority edits, sharing, or reassignment.
    const TERMINAL_STATUSES = ['rejected', 'closed', 'resolved'];

    // Solved terminal states — the requester may rate the technician only for
    // these (a rejected ticket wasn't actually solved, so no rating there).
    const RATEABLE_STATUSES = ['closed', 'resolved'];

    /**
     * Presentation metadata for a status value: a human label, a pill class
     * set (bg/text/border), and a dot colour. Single source of truth for how
     * every view renders a status.
     */
    public static function statusMeta(string $status): array
    {
        return match ($status) {
            'open' => ['label' => __('Open'), 'badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500'],
            'in_progress' => ['label' => __('In Progress'), 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
            'on_hold' => ['label' => __('On Hold'), 'badge' => 'bg-orange-50 text-orange-700 border-orange-200', 'dot' => 'bg-orange-500'],
            'waiting_for_resources' => ['label' => __('Waiting for Resources'), 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'dot' => 'bg-indigo-500'],
            'rejected' => ['label' => __('Rejected'), 'badge' => 'bg-red-50 text-red-600 border-red-200', 'dot' => 'bg-red-500'],
            'closed' => ['label' => __('Closed'), 'badge' => 'bg-zinc-100 text-zinc-600 border-zinc-200', 'dot' => 'bg-zinc-400'],
            'resolved' => ['label' => __('Resolved'), 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
            default => ['label' => ucwords(str_replace('_', ' ', $status)), 'badge' => 'bg-slate-100 text-slate-600 border-slate-200', 'dot' => 'bg-slate-400'],
        };
    }

    /** Translated display label for a priority value. */
    public static function priorityLabel(string $priority): string
    {
        return match ($priority) {
            'low' => __('Low'),
            'medium' => __('Medium'),
            'high' => __('High'),
            default => ucwords($priority),
        };
    }

    /** Translated display label for a category value. */
    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'network' => __('Network'),
            'hardware' => __('Hardware'),
            'software' => __('Software'),
            'access_request' => __('Access Request'),
            'uncategorized' => __('Uncategorized'),
            default => ucwords(str_replace('_', ' ', $category)),
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }

    /** Human-readable duration (e.g. "2d 4h", "3h 15m", "45m") for reports. */
    public static function humanDuration(?int $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }

        if ($seconds < 60) {
            return $seconds.'s';
        }

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return $hours > 0 ? "{$days}d {$hours}h" : "{$days}d";
        }
        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        return "{$minutes}m";
    }

    /**
     * A ticket can be rated by its requester once it has been solved (closed or
     * resolved) and a technician actually handled it. Re-rating is allowed —
     * this only gates whether the rating UI/endpoint is available at all.
     */
    public function canBeRated(): bool
    {
        return in_array($this->status, self::RATEABLE_STATUSES, true)
            && ! is_null($this->agent_id);
    }

    public function isRated(): bool
    {
        return ! is_null($this->rating);
    }

    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'status',
        'user_id',
        'agent_id',
        'rating',
        'rating_comment',
        'rated_at',
        'resolved_at',
        'source_email',
        'source_name',
    ];

    protected function casts(): array
    {
        return [
            'category' => 'string',
            'priority' => 'string',
            'status' => 'string',
            'rating' => 'integer',
            'rated_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function collaborators(): BelongsToMany
    {
        // Pivot has only created_at (DB default fills it on insert); don't enable
        // Laravel's withTimestamps() because it expects both columns.
        return $this->belongsToMany(User::class, 'ticket_agent', 'ticket_id', 'user_id')
            ->withPivot('created_at')
            ->orderBy('users.name');
    }

    public function isAssignedTo(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->agent_id === $user->id
            || $this->collaborators->contains('id', $user->id);
    }
}
