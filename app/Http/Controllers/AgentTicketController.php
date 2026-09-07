<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentTicketController extends Controller
{
    use AuthorizesRequests;

    // CASE WHEN ordering instead of MySQL FIELD() — keeps these queries portable
    // to SQLite for the test suite.
    private const PRIORITY_ORDER = "CASE priority WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END";

    private const STATUS_ORDER = "CASE status WHEN 'in_progress' THEN 0 WHEN 'open' THEN 1 ELSE 2 END";

    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'agent']);

        // Keyword — matches title, description, or numeric ticket id (e.g. "TK-42" -> 42).
        $keyword = trim((string) $request->input('keyword', ''));
        if ($keyword !== '') {
            $digits = preg_replace('/\D/', '', $keyword);
            $query->where(function ($q) use ($keyword, $digits) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
                if ($digits !== '') {
                    $q->orWhere('id', (int) $digits);
                }
            });
        }

        // Multi-select filters — each intersected with the allowed enum values.
        $statuses = array_intersect((array) $request->input('status', []), Ticket::STATUSES);
        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        $priorities = array_intersect((array) $request->input('priority', []), Ticket::PRIORITIES);
        if ($priorities) {
            $query->whereIn('priority', $priorities);
        }

        $categories = array_intersect((array) $request->input('category', []), Ticket::CATEGORIES);
        if ($categories) {
            $query->whereIn('category', $categories);
        }

        // Newest tickets always first, ordered by id so the id column reads
        // cleanly (backdated demo/email tickets have created_at values that
        // don't match insertion order); status/priority only break ties, which
        // is effectively never since id is unique.
        $tickets = $query
            ->latest('id')
            ->orderByRaw(self::STATUS_ORDER)
            ->orderByRaw(self::PRIORITY_ORDER)
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    // Tiny JSON endpoint polled by the ticket lists so they can auto-refresh
    // the moment a new ticket lands (e.g. from the email listener).
    public function latestId()
    {
        return response()->json(['id' => (int) Ticket::max('id')]);
    }

    public function mine()
    {
        // Agent's "my tickets" — PRD §9.5. Includes tickets where the agent
        // is the primary OR is on the ticket_agent collaborator pivot.
        $userId = auth()->id();

        $tickets = Ticket::with(['user', 'agent'])
            ->where(function ($q) use ($userId) {
                $q->where('agent_id', $userId)
                    ->orWhereHas('collaborators', fn ($c) => $c->where('users.id', $userId));
            })
            ->latest('id')
            ->orderByRaw(self::STATUS_ORDER)
            ->orderByRaw(self::PRIORITY_ORDER)
            ->paginate(15);

        return view('tickets.mine', compact('tickets'));
    }

    /**
     * The agent's own satisfaction ratings: overall average, star distribution,
     * and the anonymous requester comments. Feedback rows deliberately select
     * ONLY non-identifying columns (no user_id / source_*) so the technician
     * can read the comments but never see who wrote them.
     */
    public function ratings()
    {
        $userId = auth()->id();
        $ratedQuery = Ticket::where('agent_id', $userId)->whereNotNull('rating');

        $avg = (float) $ratedQuery->clone()->avg('rating');
        $count = $ratedQuery->clone()->count();

        $distribution = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $distribution[$star] = (clone $ratedQuery)->where('rating', $star)->count();
        }

        $feedback = $ratedQuery->clone()
            ->whereNotNull('rating_comment')
            ->where('rating_comment', '!=', '')
            ->latest('rated_at')
            ->paginate(15, ['rating', 'rating_comment', 'rated_at']);

        return view('tickets.ratings', compact('avg', 'count', 'distribution', 'feedback'));
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['user', 'agent', 'attachments', 'comments.user', 'collaborators']);

        // Two lists for the Team card, both excluding the current primary.
        // Share excludes existing collaborators too; reassign keeps them
        // (a collaborator getting promoted is fine).
        $shareableAgents = collect();
        $reassignableAgents = collect();

        if (auth()->user()->can('share', $ticket)) {
            $excludedIds = $ticket->collaborators->pluck('id')
                ->push($ticket->agent_id)
                ->filter()
                ->all();

            $shareableAgents = User::where('role', 'agent')
                ->whereNotIn('id', $excludedIds)
                ->orderBy('name')
                ->get();
        }

        if (auth()->user()->can('reassign', $ticket)) {
            $reassignableAgents = User::where('role', 'agent')
                ->where('id', '!=', $ticket->agent_id)
                ->orderBy('name')
                ->get();
        }

        // Admin-only list for hand-assigning an open, unassigned ticket.
        $assignableAgents = collect();

        if (auth()->user()->can('assignToAgent', $ticket)) {
            $assignableAgents = User::where('role', 'agent')
                ->orderBy('name')
                ->get();
        }

        return view('tickets.show', compact('ticket', 'shareableAgents', 'reassignableAgents', 'assignableAgents'));
    }

    public function assign(Ticket $ticket)
    {
        $this->authorize('assign', $ticket);

        DB::transaction(function () use ($ticket) {
            // Re-check inside the transaction so two agents racing for the same
            // open ticket don't both succeed.
            $fresh = Ticket::whereKey($ticket->id)
                ->where('status', 'open')
                ->whereNull('agent_id')
                ->lockForUpdate()
                ->first();

            abort_if(is_null($fresh), 409, 'Ticket has already been assigned.');

            $fresh->update([
                'agent_id' => auth()->id(),
                'status' => 'in_progress',
            ]);
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', 'Ticket assigned to you.');
    }

    public function adminAssign(Request $request, Ticket $ticket)
    {
        $this->authorize('assignToAgent', $ticket);

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'agent')),
            ],
        ]);

        DB::transaction(function () use ($ticket, $validated) {
            // Re-check under a lock so an agent self-assigning at the same moment
            // doesn't collide with the admin hand-assigning.
            $fresh = Ticket::whereKey($ticket->id)
                ->where('status', 'open')
                ->whereNull('agent_id')
                ->lockForUpdate()
                ->first();

            abort_if(is_null($fresh), 409, 'Ticket has already been assigned.');

            $fresh->update([
                'agent_id' => $validated['user_id'],
                'status' => 'in_progress',
            ]);
        });

        $name = User::whereKey($validated['user_id'])->value('name');

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', "Assigned to {$name}.");
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('updateStatus', $ticket);

        // Any workflow state except 'open' (that's the pre-assignment state).
        $request->validate([
            'status' => ['required', Rule::in(array_values(array_diff(Ticket::STATUSES, ['open'])))],
        ]);

        $newStatus = $request->string('status')->value();

        $attributes = ['status' => $newStatus];
        // Stamp the resolution time the first time it reaches a solved state, so
        // reports can measure how long it took. Once set it stays put.
        if (in_array($newStatus, Ticket::RATEABLE_STATUSES, true) && is_null($ticket->resolved_at)) {
            $attributes['resolved_at'] = now();
        }

        $ticket->update($attributes);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', 'Status updated to '.Ticket::statusMeta($newStatus)['label'].'.');
    }

    public function viewAttachment(Ticket $ticket, Attachment $attachment): StreamedResponse
    {
        $this->authorize('view', $ticket);
        abort_unless($attachment->ticket_id === $ticket->id, 404);

        // nosniff stops the browser from re-interpreting the bytes as HTML/script
        // if the stored Content-Type is ever wrong — a second line of defence
        // behind the upload/ingest MIME allow-list.
        return Storage::disk('local')->response($attachment->file_path, $attachment->file_name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function updatePriority(Request $request, Ticket $ticket)
    {
        $this->authorize('updatePriority', $ticket);

        $request->validate([
            'priority' => ['required', Rule::in(Ticket::PRIORITIES)],
        ]);

        $ticket->update(['priority' => $request->string('priority')]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', 'Priority updated.');
    }

    public function updateCategory(Request $request, Ticket $ticket)
    {
        $this->authorize('updateCategory', $ticket);

        $request->validate([
            'category' => ['required', Rule::in(Ticket::CATEGORIES)],
        ]);

        $ticket->update(['category' => $request->string('category')]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', __('Category updated.'));
    }

    public function share(Request $request, Ticket $ticket)
    {
        $this->authorize('share', $ticket);

        $validated = $request->validate([
            // Must be an agent, not the primary, not already on the pivot.
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'agent')),
                Rule::notIn([$ticket->agent_id]),
                Rule::notIn($ticket->collaborators()->pluck('users.id')->all()),
            ],
        ]);

        $ticket->collaborators()->attach($validated['user_id']);

        $name = User::whereKey($validated['user_id'])->value('name');

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', "Shared with {$name}.");
    }

    public function unshare(Ticket $ticket, User $user)
    {
        $this->authorize('share', $ticket);

        $ticket->collaborators()->detach($user->id);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', "Removed {$user->name} from this ticket.");
    }

    public function reassign(Request $request, Ticket $ticket)
    {
        $this->authorize('reassign', $ticket);

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'agent')),
                Rule::notIn([$ticket->agent_id]),
            ],
        ]);

        DB::transaction(function () use ($ticket, $validated) {
            // New primary must not also appear as a collaborator. The old primary
            // is dropped entirely (per the user's chosen transfer semantics).
            $ticket->collaborators()->detach($validated['user_id']);
            $ticket->update(['agent_id' => $validated['user_id']]);
        });

        $name = User::whereKey($validated['user_id'])->value('name');

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', "Reassigned to {$name}.");
    }

    public function release(Ticket $ticket)
    {
        $this->authorize('release', $ticket);

        DB::transaction(function () use ($ticket) {
            // Back to the shared queue: drop the primary and any collaborators,
            // and reopen it so it re-enters the unassigned triage queue for any
            // technician to pick up.
            $ticket->collaborators()->detach();
            $ticket->update(['agent_id' => null, 'status' => 'open']);
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', __('Ticket returned to the queue.'));
    }
}
