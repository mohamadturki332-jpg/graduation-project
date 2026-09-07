<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Same filter set as the agent queue, scoped to the employee's own tickets.
        $query = Ticket::with('agent')->where('user_id', auth()->id());

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

        $tickets = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('my-tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = DB::transaction(function () use ($request) {
            $ticket = Ticket::create([
                'title' => $request->validated('title'),
                'description' => $request->validated('description'),
                'category' => $request->validated('category'),
                'priority' => $request->validated('priority'),
                'status' => 'open',
                'user_id' => auth()->id(),
            ]);

            foreach ($request->file('attachments') ?? [] as $file) {
                // Disk filename is UUID + safe extension only — original name is
                // user-controlled, so we keep it in the DB for display but never
                // let it touch the filesystem.
                $extension = strtolower($file->getClientOriginalExtension());
                $diskName = Str::uuid().'.'.$extension;
                $path = $file->storeAs("attachments/{$ticket->id}", $diskName, 'local');

                Attachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            return $ticket;
        });

        return redirect()
            ->route('my-tickets.show', $ticket)
            ->with('status', 'Ticket submitted.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['attachments', 'comments.user', 'agent']);

        return view('my-tickets.show', compact('ticket'));
    }

    public function rate(Request $request, Ticket $ticket)
    {
        $this->authorize('rate', $ticket);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'rating_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $ticket->update([
            'rating' => $validated['rating'],
            'rating_comment' => $validated['rating_comment'] ?? null,
            'rated_at' => now(),
        ]);

        return redirect()
            ->route('my-tickets.show', $ticket)
            ->with('status', __('Thanks for your feedback.'));
    }

    public function downloadAttachment(Ticket $ticket, Attachment $attachment): StreamedResponse
    {
        $this->authorize('view', $ticket);
        abort_unless($attachment->ticket_id === $ticket->id, 404);

        // Inline disposition lets the browser render images/PDFs in-page instead
        // of forcing a download. Filename is still set so "Save as" works.
        // nosniff stops the browser from re-interpreting the bytes as HTML/script
        // if the stored Content-Type is ever wrong (defence behind the MIME allow-list).
        return Storage::disk('local')->response($attachment->file_path, $attachment->file_name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
