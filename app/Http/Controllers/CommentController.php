<?php

namespace App\Http\Controllers;

use App\Mail\TicketReplyMail;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorize('converse', $ticket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        // Persist the message to the on-site conversation thread.
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'visibility' => 'reply',
        ]);

        // When a technician/admin replies, email the requester. (When the
        // requester themselves posts, there's nobody to notify by email.)
        if (auth()->id() !== $ticket->user_id) {
            // For email-origin tickets the requester is the external sender;
            // for web tickets it's the owner's account email.
            $to = $ticket->source_email ?: $ticket->user?->email;

            if ($to) {
                // Sent synchronously so it delivers without a running queue worker;
                // the message is already saved, so a mail failure must not break
                // the post — log it and carry on.
                try {
                    Mail::to($to)->send(new TicketReplyMail($ticket, $validated['body'], auth()->user()->name));
                } catch (\Throwable $e) {
                    Log::warning('Failed to email ticket reply to requester', [
                        'ticket' => $ticket->id,
                        'to' => $to,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // The conversation posts over AJAX so the page doesn't reload; hand back
        // the rendered message so the client can append it in place.
        if ($request->expectsJson() || $request->ajax()) {
            $user = auth()->user();
            $fromTech = $user->hasRole('agent') || $user->hasRole('admin');
            $roleLabel = $user->hasRole('agent') ? __('Technician') : ($user->hasRole('admin') ? __('Admin') : __('Requester'));

            return response()->json([
                'id' => $comment->id,
                'name' => $user->name,
                'body' => $comment->body,
                'from_tech' => $fromTech,
                'role_label' => $roleLabel,
                'created_at' => $comment->created_at->translatedFormat('M j, Y g:ia'),
                'count' => $ticket->comments()->where('visibility', 'reply')->count(),
            ]);
        }

        return back()->with('status', __('Reply posted.'));
    }

    public function destroy(Request $request, Ticket $ticket, Comment $comment)
    {
        // Guard against a comment id from a different ticket being routed here.
        abort_unless($comment->ticket_id === $ticket->id, 404);

        $this->authorize('delete', $comment);

        $comment->delete();

        // The conversation deletes over AJAX so the page doesn't reload.
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'deleted' => true,
                'count' => $ticket->comments()->where('visibility', 'reply')->count(),
            ]);
        }

        return back()->with('status', 'Comment deleted.');
    }
}
