<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($keyword = trim((string) $request->input('keyword'))) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");

                if (ctype_digit($keyword)) {
                    $q->orWhere('id', (int) $keyword);
                }
            });
        }

        $roles = array_intersect((array) $request->input('role', []), ['admin', 'agent', 'employee']);
        if ($roles !== []) {
            $query->whereIn('role', $roles);
        }

        $departments = array_intersect((array) $request->input('department', []), User::DEPARTMENTS);
        if ($departments !== []) {
            $query->whereIn('department', $departments);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('users.index')->with('status', __('User created.'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('status', __('User updated.'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        DB::transaction(function () use ($user) {
            // Tickets this user submitted are deleted along with them. Remove the
            // stored attachment files from disk first — the DB rows (attachments,
            // comments, collaborator pivots) cascade automatically when the
            // ticket is deleted, but the files on disk would otherwise be orphaned.
            $ownedTicketIds = Ticket::where('user_id', $user->id)->pluck('id');

            if ($ownedTicketIds->isNotEmpty()) {
                foreach (Attachment::whereIn('ticket_id', $ownedTicketIds)->pluck('file_path') as $path) {
                    Storage::disk('local')->delete($path);
                }

                // Mass delete relies on the DB-level cascade FKs to clear
                // attachments / comments / ticket_agent rows for these tickets.
                Ticket::whereKey($ownedTicketIds->all())->delete();
            }

            // Tickets where this user is the assigned technician (but did NOT
            // submit) go back to the open queue; otherwise agent_id nulls out
            // while the status stays in_progress and the ticket strands.
            Ticket::where('agent_id', $user->id)
                ->whereNotIn('status', Ticket::TERMINAL_STATUSES)
                ->update(['agent_id' => null, 'status' => 'open']);

            // Restrict-on-delete FKs still pointing at the user from rows we keep
            // (their comments / collaborator pivots on OTHER people's tickets).
            DB::table('ticket_agent')->where('user_id', $user->id)->delete();
            Comment::where('user_id', $user->id)->delete();

            $user->delete();
        });

        return redirect()->route('users.index')->with('status', __('User deleted.'));
    }
}
