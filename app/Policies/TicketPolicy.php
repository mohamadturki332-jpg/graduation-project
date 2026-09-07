<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        // Admins have full oversight of every ticket.
        if ($user->hasRole('admin')) {
            return true;
        }

        // A technician may open any ticket to see its details and who is handling
        // it — useful for coordination and pick-ups from the queue. The private
        // requester conversation stays gated separately by converse(), so a
        // technician who isn't the primary can view the ticket but not read the
        // chat between the requester and the assigned technician.
        if ($user->hasRole('agent')) {
            return true;
        }

        if ($user->hasRole('employee')) {
            return $ticket->user_id === $user->id;
        }

        return false;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('agent')
            && $ticket->status === 'open'
            && is_null($ticket->agent_id);
    }

    public function assignToAgent(User $user, Ticket $ticket): bool
    {
        // Admin hand-assigns an open, unassigned ticket to a chosen technician
        // (distinct from an agent self-assigning via assign()).
        return $user->hasRole('admin')
            && $ticket->status === 'open'
            && is_null($ticket->agent_id);
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        // Once assigned, the agent (or a collaborator) can move the ticket
        // between the active workflow states until it reaches a terminal one
        // (rejected / closed / resolved), after which it's locked.
        return $user->hasRole('agent')
            && $ticket->isAssignedTo($user)
            && ! $ticket->isTerminal();
    }

    public function updatePriority(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('agent')
            && $ticket->isAssignedTo($user)
            && ! $ticket->isTerminal();
    }

    public function updateCategory(User $user, Ticket $ticket): bool
    {
        // The category an email ticket is auto-detected with can be wrong, so it
        // needs correcting. Only the admin (any ticket, e.g. during triage) or the
        // primary technician handling it may change it, and never once it's locked.
        if ($ticket->isTerminal()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('agent') && $ticket->agent_id === $user->id;
    }

    public function share(User $user, Ticket $ticket): bool
    {
        // Only the primary assignee (or an admin) can bring others in,
        // and only while the ticket is still active.
        if ($ticket->isTerminal()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('agent') && $ticket->agent_id === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('admin');
    }

    public function converse(User $user, Ticket $ticket): bool
    {
        // The requester conversation is between the employee who opened the ticket
        // and the technician team handling it — the primary agent PLUS any
        // collaborators the ticket was shared with (they can read the thread and
        // reply to the requester too). Technicians who aren't on the ticket still
        // see nothing. Admins keep access for oversight.
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('agent')) {
            return $ticket->isAssignedTo($user);
        }

        if ($user->hasRole('employee')) {
            return $ticket->user_id === $user->id;
        }

        return false;
    }

    public function rate(User $user, Ticket $ticket): bool
    {
        // Only the employee who opened the ticket may rate it, and only once it
        // has been solved (closed/resolved) with a technician who handled it.
        return $user->hasRole('employee')
            && $ticket->user_id === $user->id
            && $ticket->canBeRated();
    }

    public function reassign(User $user, Ticket $ticket): bool
    {
        // Same gate as share: must already have a primary, must not be terminal,
        // only admin or the current primary can transfer ownership.
        if (is_null($ticket->agent_id) || $ticket->isTerminal()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('agent') && $ticket->agent_id === $user->id;
    }

    public function release(User $user, Ticket $ticket): bool
    {
        // Hand a ticket back to the shared queue instead of to a specific peer:
        // it must currently have a primary and not be terminal, and only the
        // current primary technician (or an admin) may let go of it.
        if (is_null($ticket->agent_id) || $ticket->isTerminal()) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('agent') && $ticket->agent_id === $user->id;
    }
}
