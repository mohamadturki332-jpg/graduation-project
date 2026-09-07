<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $statusCounts = Ticket::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // One entry per workflow status (0 when none), in the canonical order.
        $stats = collect(Ticket::STATUSES)
            ->mapWithKeys(fn ($s) => [$s => (int) ($statusCounts[$s] ?? 0)])
            ->all();

        $priorityCounts = Ticket::selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        $priorityStats = [
            'high' => (int) ($priorityCounts['high'] ?? 0),
            'medium' => (int) ($priorityCounts['medium'] ?? 0),
            'low' => (int) ($priorityCounts['low'] ?? 0),
        ];

        $categoryCounts = Ticket::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categoryStats = collect(Ticket::CATEGORIES)
            ->mapWithKeys(fn ($c) => [$c => (int) ($categoryCounts[$c] ?? 0)])
            ->all();

        // Technician leaderboard: ranked by a weighted score that balances
        // productivity and satisfaction equally. Each technician's resolved
        // count is normalised against the busiest technician (0–1) and their
        // average rating is normalised against the 5-star scale (0–1); the two
        // are combined 50/50. Filter/sort in PHP (agent count is small) so it
        // stays DB-agnostic.
        $agents = User::where('role', 'agent')
            ->withCount([
                'assignedTickets as resolved_count' => fn ($q) => $q->whereIn('status', Ticket::RATEABLE_STATUSES),
                'assignedTickets as ratings_count' => fn ($q) => $q->whereNotNull('rating'),
                // Tickets this technician assisted on as a collaborator (shared
                // with, not primary) — a productivity credit shown alongside the
                // rating, which stays with the primary technician.
                'collaborations as assists_count',
            ])
            ->withAvg(['assignedTickets as avg_rating' => fn ($q) => $q->whereNotNull('rating')], 'rating')
            ->get()
            ->filter(fn ($u) => $u->resolved_count > 0);

        // Highest resolved count sets the productivity scale (>= 1 here since
        // every remaining technician has resolved at least one ticket).
        $maxResolved = (int) $agents->max('resolved_count');

        $leaderboard = $agents
            ->map(function ($u) use ($maxResolved) {
                $normalizedResolved = $maxResolved > 0 ? $u->resolved_count / $maxResolved : 0;
                $normalizedRating = ((float) $u->avg_rating) / 5;
                $u->score = ($normalizedResolved * 0.5) + ($normalizedRating * 0.5);

                return $u;
            })
            ->sortByDesc('score')
            ->take(5)
            ->values();

        return view('admin.dashboard', compact('stats', 'priorityStats', 'categoryStats', 'leaderboard'));
    }

    public function ratings(Request $request)
    {
        // Full technician performance list: every agent with their resolved
        // count, average rating, and number of ratings. Rated technicians sort
        // to the top (highest average first), unrated ones fall to the bottom.
        $technicians = User::where('role', 'agent')
            ->withCount([
                'assignedTickets as resolved_count' => fn ($q) => $q->whereIn('status', Ticket::RATEABLE_STATUSES),
                'assignedTickets as ratings_count' => fn ($q) => $q->whereNotNull('rating'),
            ])
            ->withAvg(['assignedTickets as avg_rating' => fn ($q) => $q->whereNotNull('rating')], 'rating')
            ->get()
            ->sortByDesc(fn ($u) => [$u->ratings_count > 0 ? 1 : 0, round((float) $u->avg_rating, 2), $u->ratings_count])
            ->values();

        // Optional drill-down: ?technician=<id> narrows the feedback to one
        // technician (ignored if it isn't a real agent on the list).
        $selectedId = $request->integer('technician') ?: null;
        $selected = $selectedId ? $technicians->firstWhere('id', $selectedId) : null;
        $selectedId = $selected?->id;

        // Every written comment, newest first. Unlike the technician view, the
        // admin sees the requester's identity (oversight).
        $feedback = Ticket::whereNotNull('rating')
            ->whereNotNull('rating_comment')
            ->where('rating_comment', '!=', '')
            ->when($selectedId, fn ($q) => $q->where('agent_id', $selectedId))
            ->with(['user', 'agent'])
            ->latest('rated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.ratings', compact('technicians', 'feedback', 'selected'));
    }

    public function report(Request $request)
    {
        // Optional date window applied to when tickets were submitted.
        $from = $request->date('from');
        $to = $request->date('to');

        // A reusable range constraint so every figure covers the same window.
        $inRange = function ($query) use ($from, $to) {
            if ($from) {
                $query->where('created_at', '>=', $from->copy()->startOfDay());
            }
            if ($to) {
                $query->where('created_at', '<=', $to->copy()->endOfDay());
            }

            return $query;
        };

        $base = fn () => $inRange(Ticket::query());

        $total = $base()->count();

        $statusStats = collect(Ticket::STATUSES)
            ->mapWithKeys(fn ($s) => [$s => (int) $base()->where('status', $s)->count()])
            ->all();

        $priorityStats = collect(Ticket::PRIORITIES)
            ->mapWithKeys(fn ($p) => [$p => (int) $base()->where('priority', $p)->count()])
            ->all();

        $categoryStats = collect(Ticket::CATEGORIES)
            ->mapWithKeys(fn ($c) => [$c => (int) $base()->where('category', $c)->count()])
            ->all();

        $completed = $base()->whereIn('status', Ticket::TERMINAL_STATUSES)->count();
        $backlog = $base()->whereNotIn('status', Ticket::TERMINAL_STATUSES)->count();
        $completion = $total ? round($completed / $total * 100) : 0;

        // Channel: email intake vs. the web form.
        $emailCount = $base()->whereNotNull('source_email')->count();
        $webCount = $total - $emailCount;

        // Resolution time (SLA): average created_at → resolved_at over the tickets
        // we actually stamped. Computed in PHP so it runs on SQLite too.
        $resolvedTickets = $base()->whereNotNull('resolved_at')->get(['created_at', 'resolved_at']);
        $resolvedCount = $resolvedTickets->count();
        $avgResolutionSeconds = $resolvedCount
            ? (int) round($resolvedTickets->avg(fn ($t) => $t->created_at->diffInSeconds($t->resolved_at)))
            : null;
        $fastestSeconds = $resolvedCount
            ? (int) $resolvedTickets->min(fn ($t) => $t->created_at->diffInSeconds($t->resolved_at))
            : null;
        $slowestSeconds = $resolvedCount
            ? (int) $resolvedTickets->max(fn ($t) => $t->created_at->diffInSeconds($t->resolved_at))
            : null;

        // Technician performance within the same window.
        $technicians = User::where('role', 'agent')
            ->withCount([
                'assignedTickets as resolved_count' => fn ($q) => $inRange($q->whereIn('status', Ticket::RATEABLE_STATUSES)),
                'assignedTickets as ratings_count' => fn ($q) => $inRange($q->whereNotNull('rating')),
            ])
            ->withAvg(['assignedTickets as avg_rating' => fn ($q) => $inRange($q->whereNotNull('rating'))], 'rating')
            ->get()
            ->sortByDesc(fn ($u) => [$u->resolved_count, round((float) $u->avg_rating, 2)])
            ->values();

        // Written feedback within the window.
        $feedback = $base()
            ->whereNotNull('rating')
            ->whereNotNull('rating_comment')
            ->where('rating_comment', '!=', '')
            ->with(['user', 'agent'])
            ->latest('rated_at')
            ->limit(50)
            ->get();

        $ratedCount = $base()->whereNotNull('rating')->count();
        $avgRating = $ratedCount ? round((float) $base()->whereNotNull('rating')->avg('rating'), 2) : null;

        return view('admin.report', compact(
            'from', 'to', 'total', 'statusStats', 'priorityStats', 'categoryStats',
            'completed', 'backlog', 'completion', 'emailCount', 'webCount',
            'resolvedCount', 'avgResolutionSeconds', 'fastestSeconds', 'slowestSeconds',
            'technicians', 'feedback', 'ratedCount', 'avgRating'
        ));
    }

    public function tickets(Request $request)
    {
        // CASE WHEN over MySQL FIELD() so the same query runs on SQLite (used in
        // the test suite) without engine-specific functions.
        $statusOrder = "CASE status WHEN 'in_progress' THEN 0 WHEN 'open' THEN 1 ELSE 2 END";
        $priorityOrder = "CASE priority WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END";

        $query = Ticket::with(['user', 'agent']);

        // Same filter set as the agent queue: keyword matches title, description,
        // numeric id, or the sender (name/email/source fields for email tickets).
        $keyword = trim((string) $request->input('keyword', ''));
        if ($keyword !== '') {
            $digits = preg_replace('/\D/', '', $keyword);
            $query->where(function ($q) use ($keyword, $digits) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('source_name', 'like', "%{$keyword}%")
                  ->orWhere('source_email', 'like', "%{$keyword}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$keyword}%"));
                if ($digits !== '') {
                    $q->orWhere('id', (int) $digits);
                }
            });
        }

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

        // Newest first (by id so the id column reads cleanly) — fresh
        // submissions always land on page 1.
        $tickets = $query
            ->latest('id')
            ->orderByRaw($statusOrder)
            ->orderByRaw($priorityOrder)
            ->paginate(20)
            ->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function destroyTicket(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        // DB cascade clears the attachments rows; we still need to wipe the
        // physical files off the local disk.
        Storage::disk('local')->deleteDirectory("attachments/{$ticket->id}");

        $ticket->delete();

        return redirect()
            ->route('admin.tickets.index')
            ->with('status', "Ticket #{$ticket->id} deleted.");
    }
}
