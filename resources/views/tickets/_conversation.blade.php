{{-- Requester conversation: messages with visibility 'reply'. Expects $ticket with comments.user eager-loaded. --}}
@php
    $messages = $ticket->comments->where('visibility', 'reply')->sortBy('created_at');
    $canConverse = auth()->user()->can('converse', $ticket);
    $isTech = auth()->user()->hasRole('admin') || auth()->user()->hasRole('agent');
    $replyTo = $ticket->source_email ?: $ticket->user?->email;
@endphp
@if (! $canConverse)
{{-- The requester conversation is private to the requester and the ONE
     technician the ticket is assigned to (its primary agent), plus admins for
     oversight. Every other technician — including collaborators the ticket was
     shared with — sees nothing. --}}
@else
<div class="luxury-card rounded-lg overflow-hidden w-full{{ ($center ?? false) ? ' max-w-4xl mx-auto' : '' }}">
<div class="bg-primary px-6 py-4 flex items-center justify-between">
<h2 class="text-[12px] font-bold uppercase tracking-widest text-white">
    {{ __('Conversation with requester') }} <span id="conversation-count" class="text-white/70 font-medium normal-case tracking-normal">({{ $messages->count() }})</span>
</h2>
<span class="px-2 py-0.5 bg-white/20 text-white text-[10px] font-semibold rounded uppercase tracking-wide">{{ __('Live') }}</span>
</div>
<div class="p-8">

<p id="conversation-empty" class="text-on-surface-variant text-[13px] font-light mb-6 {{ $messages->isEmpty() ? '' : 'hidden' }}">{{ __('No messages yet.') }}</p>
<ul id="conversation-list" data-comments-url="{{ url('tickets/'.$ticket->id.'/comments') }}" class="space-y-4 mb-6 {{ $messages->isEmpty() ? 'hidden' : '' }}">
@foreach ($messages as $message)
@php
    $fromTech = ($message->user->role ?? null) === 'agent' || ($message->user->role ?? null) === 'admin';
    $roleLabel = ($message->user->role ?? null) === 'agent' ? __('Technician')
        : (($message->user->role ?? null) === 'admin' ? __('Admin') : __('Requester'));
@endphp
<li class="flex {{ $fromTech ? 'justify-end' : 'justify-start' }}" data-comment-id="{{ $message->id }}">
<div class="max-w-[80%] rounded-lg px-4 py-2.5 border {{ $fromTech ? 'bg-primary/5 border-primary/20' : 'bg-surface-dim border-outline-variant' }}">
<div class="flex flex-wrap items-center gap-2 mb-1">
<span class="text-[12px] font-semibold text-on-surface">{{ $message->user->name ?? 'Unknown' }}</span>
<span class="text-[10px] font-semibold uppercase tracking-wide {{ $fromTech ? 'text-primary' : 'text-secondary' }}">{{ $roleLabel }}</span>
<span class="text-on-surface-variant/60 text-[10px]">{{ $message->created_at->translatedFormat('M j, Y g:ia') }}</span>
@can('delete', $message)
<button type="button" class="conversation-delete ms-auto text-on-surface-variant/50 hover:text-red-500 transition-colors" title="{{ __('Delete message') }}">
<span class="material-symbols-outlined text-[15px]">delete</span>
</button>
@endcan
</div>
<p class="whitespace-pre-line text-on-surface-variant text-[13px] leading-relaxed">{{ $message->body }}</p>
</div>
</li>
@endforeach
</ul>

<form id="conversation-form" method="POST" action="{{ route('tickets.reply', $ticket) }}">
@csrf
<label for="reply_body" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">
{{ $isTech ? __('Reply to requester') : __('Message the support team') }}
</label>
<textarea name="body" id="reply_body" rows="4" required maxlength="5000"
          placeholder="{{ $isTech ? __('Type your reply to the employee…') : __('Type your message…') }}"
          class="block w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-0 transition-colors">{{ old('body') }}</textarea>
<p id="conversation-error" class="mt-2 text-[12px] text-red-500 hidden"></p>
@error('body')<p class="mt-2 text-[12px] text-red-500">{{ $message }}</p>@enderror
<div class="mt-3 flex items-center justify-between gap-4">
@if ($isTech && $replyTo)
<p class="text-[11px] text-on-surface-variant font-light">{{ __('Emailed to') }} <span class="font-semibold text-on-surface" dir="ltr">{{ $replyTo }}</span>.</p>
@else
<span></span>
@endif
<button type="submit" id="conversation-send" class="px-8 py-3 text-[13px] font-medium bg-primary text-white hover:bg-primary/90 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">{{ __('Send') }}</button>
</div>
</form>
</div>
</div>

<script>
(function () {
    const form = document.getElementById('conversation-form');
    if (!form) return;

    const list = document.getElementById('conversation-list');
    const empty = document.getElementById('conversation-empty');
    const countEl = document.getElementById('conversation-count');
    const textarea = document.getElementById('reply_body');
    const sendBtn = document.getElementById('conversation-send');
    const errorEl = document.getElementById('conversation-error');

    const token = form.querySelector('input[name="_token"]').value;

    function updateCount() {
        if (countEl) countEl.textContent = '(' + list.children.length + ')';
        if (list.children.length === 0) {
            list.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }

    function appendMessage(msg) {
        const li = document.createElement('li');
        li.className = 'flex ' + (msg.from_tech ? 'justify-end' : 'justify-start');
        li.dataset.commentId = msg.id;

        const bubble = document.createElement('div');
        bubble.className = 'max-w-[80%] rounded-lg px-4 py-2.5 border ' +
            (msg.from_tech ? 'bg-primary/5 border-primary/20' : 'bg-surface-dim border-outline-variant');

        const meta = document.createElement('div');
        meta.className = 'flex flex-wrap items-center gap-2 mb-1';

        const name = document.createElement('span');
        name.className = 'text-[12px] font-semibold text-on-surface';
        name.textContent = msg.name;

        const role = document.createElement('span');
        role.className = 'text-[10px] font-semibold uppercase tracking-wide ' +
            (msg.from_tech ? 'text-primary' : 'text-secondary');
        role.textContent = msg.role_label;

        const time = document.createElement('span');
        time.className = 'text-on-surface-variant/60 text-[10px]';
        time.textContent = msg.created_at;

        meta.append(name, role, time);

        // The poster always owns the message they just sent, so they may delete it.
        const del = document.createElement('button');
        del.type = 'button';
        del.className = 'conversation-delete ms-auto text-on-surface-variant/50 hover:text-red-500 transition-colors';
        del.title = @json(__('Delete message'));
        del.innerHTML = '<span class="material-symbols-outlined text-[15px]">delete</span>';
        meta.append(del);

        const body = document.createElement('p');
        body.className = 'whitespace-pre-line text-on-surface-variant text-[13px] leading-relaxed';
        body.textContent = msg.body;

        bubble.append(meta, body);
        li.append(bubble);
        list.append(li);
    }

    // Delegated delete: works for both server-rendered and freshly-posted messages.
    list.addEventListener('click', async function (e) {
        const btn = e.target.closest('.conversation-delete');
        if (!btn) return;

        const li = btn.closest('li[data-comment-id]');
        if (!li || !confirm(@json(__('Delete this message?')))) return;

        btn.disabled = true;
        try {
            const res = await fetch(list.dataset.commentsUrl + '/' + li.dataset.commentId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: new URLSearchParams({ _method: 'DELETE' }),
            });
            if (res.ok) {
                li.remove();
                updateCount();
            } else {
                btn.disabled = false;
                alert(@json(__('Could not delete the message.')));
            }
        } catch (_) {
            btn.disabled = false;
            alert(@json(__('Network error. Please try again.')));
        }
    });

    // Enter sends the message; Shift+Enter inserts a newline (standard chat UX).
    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        const body = textarea.value.trim();
        if (!body) return;

        sendBtn.disabled = true;

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                },
                body: new FormData(form),
            });

            if (!res.ok) {
                let message = @json(__('Something went wrong. Please try again.'));
                try {
                    const data = await res.json();
                    if (data.errors && data.errors.body) message = data.errors.body[0];
                    else if (data.message) message = data.message;
                } catch (_) {}
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
                return;
            }

            const msg = await res.json();
            empty.classList.add('hidden');
            list.classList.remove('hidden');
            appendMessage(msg);
            if (countEl && typeof msg.count !== 'undefined') countEl.textContent = '(' + msg.count + ')';
            textarea.value = '';
            textarea.focus();
        } catch (_) {
            errorEl.textContent = @json(__('Network error. Please try again.'));
            errorEl.classList.remove('hidden');
        } finally {
            sendBtn.disabled = false;
        }
    });
})();
</script>
@endif
