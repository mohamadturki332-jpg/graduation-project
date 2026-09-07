<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const DEPARTMENTS = ['it', 'hr', 'finance', 'sales', 'operations', 'support'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'department' => 'string',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /** Tickets this user handles as the primary technician. */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'agent_id');
    }

    /**
     * Tickets this user was brought onto as a collaborator (shared with, not the
     * primary). Used for the admin "assists" counter — recognition for helping on
     * a ticket without owning it. Kept separate from the star rating, which stays
     * attributed to the primary technician only.
     */
    public function collaborations(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_agent', 'user_id', 'ticket_id');
    }

    /**
     * Display job title: admins and agents have fixed titles; employees get a
     * department-specific one (e.g. finance employee => Accountant).
     */
    public function jobTitle(): string
    {
        if ($this->role === 'admin') {
            return __('System Administrator');
        }

        if ($this->role === 'agent') {
            return __('Technician');
        }

        return match ($this->department) {
            'hr' => __('HR Specialist'),
            'finance' => __('Accountant'),
            'sales' => __('Sales Representative'),
            'operations' => __('Operator'),
            'support' => __('Support Officer'),
            default => __('Employee'),
        };
    }
}
