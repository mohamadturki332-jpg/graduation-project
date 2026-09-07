@extends('layouts.nct')

@section('title', __('Ticket') . ' #' . $ticket->id . ' — ' . $ticket->title)

@section('content')
@php
    $backRoute = auth()->user()->hasRole('admin') ? route('admin.tickets.index') : route('tickets.index');
    $statusMeta = \App\Models\Ticket::statusMeta($ticket->status);
    $priorityColor = match ($ticket->priority) {
        'high' => 'text-red-600', 'medium' => 'text-amber-500', 'low' => 'text-gray-500', default => 'text-gray-500',
    };
    $inputClass = 'block rounded-lg border border-outline bg-white px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors';
    $primaryBtn = 'inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-white text-[13px] font-medium hover:bg-primary/90 transition-all shadow-sm';
    $backLink = 'text-[13px] text-on-surface-variant hover:text-on-surface transition-colors';
    $notice = 'rounded-lg border border-outline-variant bg-surface-dim px-4 py-3 text-[13px] text-on-surface-variant';
    $sectionLabel = 'text-[11px] font-bold text-on-surface-variant uppercase tracking-widest';
    $assigned = ! is_null($ticket->agent_id);
    $canShare = $assigned && auth()->user()->can('share', $ticket);
    $canReassign = $assigned && auth()->user()->can('reassign', $ticket);
@endphp

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">
<a href="{{ $backRoute }}" class="hover:text-primary transition-colors">{{ __('Tickets') }}</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-primary">#{{ $ticket->id }}</span>
</div>

{{-- Header card (gold top accent) --}}
<div class="luxury-card rounded-lg p-8 border-t-2 border-secondary flex flex-col md:flex-row md:items-center justify-between gap-6">
<div class="min-w-0">
<div class="flex flex-wrap items-center gap-3 mb-3">
<h1 class="text-2xl font-headline font-light text-on-surface">
<span class="text-on-surface-variant/50">#{{ $ticket->id }}</span> {{ $ticket->title }}
</h1>
<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-dim border border-outline-variant text-[11px] font-bold uppercase tracking-wider text-on-surface">
<span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }}"></span>{{ $statusMeta['label'] }}
</span>
</div>
<div class="flex flex-wrap gap-x-8 gap-y-2 text-[13px] text-on-surface-variant">
<span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">priority_high</span>{{ __('Priority') }}: <span class="font-semibold {{ $priorityColor }}">{{ \App\Models\Ticket::priorityLabel($ticket->priority) }}</span></span>
<span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">category</span>{{ __('Category') }}: <span class="font-semibold text-primary">{{ \App\Models\Ticket::categoryLabel($ticket->category) }}</span></span>
<span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">calendar_today</span>{{ __('Submitted') }}: <span class="font-semibold text-on-surface">{{ $ticket->created_at->translatedFormat('M j, Y g:ia') }}</span></span>
</div>
</div>
</div>

@if ($ticket->source_email)
<div class="luxury-card rounded-lg px-5 py-3.5 flex items-center gap-4">
<div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-secondary text-[20px]">mail</span>
</div>
<div class="min-w-0">
<p class="text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">{{ __('Received by email') }}</p>
@if ($ticket->source_name)
<p class="text-[14px] font-semibold text-on-surface truncate text-left"><bdi>{{ $ticket->source_name }}</bdi></p>
@endif
<p class="text-[13px] text-on-surface-variant truncate" dir="ltr">{{ $ticket->source_email }}</p>
</div>
</div>
@endif

{{-- Two-column workspace --}}
<div class="grid grid-cols-12 gap-8 items-start">

{{-- LEFT: details + conversation --}}
<div class="col-span-12 lg:col-span-8 flex flex-col gap-8">

{{-- Description + attachments --}}
<div class="luxury-card rounded-lg p-8">
<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4">{{ __('Detailed description') }}</h2>
<p class="whitespace-pre-line text-on-surface-variant text-[14px] leading-relaxed">{{ $ticket->description }}</p>

<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4 mt-8">{{ __('Attachments') }} ({{ $ticket->attachments->count() }})</h2>
@if ($ticket->attachments->isEmpty())
<p class="text-on-surface-variant text-[13px] font-light">{{ __('No attachments.') }}</p>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
@foreach ($ticket->attachments as $attachment)
@php
    $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
    $url = route('tickets.attachment', [$ticket, $attachment]);
@endphp
<a href="{{ $url }}" target="_blank" class="luxury-card rounded-lg overflow-hidden group block">
@if ($isImage)
<img src="{{ $url }}" alt="{{ $attachment->file_name }}" class="w-full aspect-video object-cover">
@else
<div class="w-full aspect-video flex items-center justify-center bg-surface-dim">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">description</span>
</div>
@endif
<p class="text-[11px] text-on-surface-variant font-medium truncate px-3 py-2">{{ $attachment->file_name }}</p>
</a>
@endforeach
</div>
@endif
</div>

{{-- Conversation --}}
@include('tickets._conversation', ['ticket' => $ticket])
</div>

{{-- RIGHT: technician control + team --}}
<div class="col-span-12 lg:col-span-4 flex flex-col gap-8">

{{-- Technician control (state-aware actions) --}}
<div class="luxury-card rounded-lg p-6">
<h2 class="{{ $sectionLabel }} mb-4">{{ __('Technician control') }}</h2>

@if (! $assigned)
<div class="space-y-4">
@can('assign', $ticket)
<form method="POST" action="{{ route('tickets.assign', $ticket) }}">
@csrf
<button type="submit" class="{{ $primaryBtn }} w-full py-3">
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1">person_check</span>{{ __('Assign to me') }}
</button>
</form>
@else
<div class="{{ $notice }}">{{ __('Unassigned') }}</div>
@endcan

@can('assignToAgent', $ticket)
<form method="POST" action="{{ route('tickets.admin-assign', $ticket) }}" class="pt-4 border-t border-outline-variant space-y-3">
@csrf
<label for="assign_user_id" class="block {{ $sectionLabel }}">{{ __('Assign to a Technician') }}</label>
<select name="user_id" id="assign_user_id" required class="{{ $inputClass }} w-full">
<option value="">{{ __('— Choose a Technician —') }}</option>
@foreach ($assignableAgents as $agent)
<option value="{{ $agent->id }}" @selected(old('user_id') == $agent->id)>{{ $agent->name }}</option>
@endforeach
</select>
@error('user_id')<p class="text-red-500 text-[12px]">{{ $message }}</p>@enderror
<button type="submit" class="{{ $primaryBtn }} w-full">{{ __('Assign') }}</button>
</form>
@endcan
</div>

@elseif ($ticket->isAssignedTo(auth()->user()) && ! $ticket->isTerminal())
<div class="space-y-4">
<form method="POST" action="{{ route('tickets.status', $ticket) }}" class="space-y-2">
@csrf
@method('PATCH')
<label for="status" class="block {{ $sectionLabel }}">{{ __('Status') }}</label>
<div class="flex gap-2">
<select name="status" id="status" class="{{ $inputClass }} flex-1">
@foreach (\App\Models\Ticket::STATUSES as $s)
@continue($s === 'open')
<option value="{{ $s }}" @selected($ticket->status === $s)>{{ \App\Models\Ticket::statusMeta($s)['label'] }}</option>
@endforeach
</select>
<button type="submit" class="{{ $primaryBtn }}">{{ __('Update') }}</button>
</div>
</form>
<form method="POST" action="{{ route('tickets.priority', $ticket) }}" class="space-y-2 pt-4 border-t border-outline-variant">
@csrf
@method('PATCH')
<label for="priority" class="block {{ $sectionLabel }}">{{ __('Priority') }}</label>
<div class="flex gap-2">
<select name="priority" id="priority" class="{{ $inputClass }} flex-1">
<option value="low" @selected($ticket->priority === 'low')>{{ __('Low') }}</option>
<option value="medium" @selected($ticket->priority === 'medium')>{{ __('Medium') }}</option>
<option value="high" @selected($ticket->priority === 'high')>{{ __('High') }}</option>
</select>
<button type="submit" class="{{ $primaryBtn }}">{{ __('Update') }}</button>
</div>
</form>
</div>

@elseif ($ticket->isAssignedTo(auth()->user()))
<div class="{{ $notice }}">{{ __('This ticket is :status.', ['status' => $statusMeta['label']]) }}</div>

@else
<div class="{{ $notice }}">{{ __('Assigned to :name.', ['name' => $ticket->agent->name]) }}</div>
@endif

@can('updateCategory', $ticket)
<form method="POST" action="{{ route('tickets.category', $ticket) }}" class="space-y-2 mt-4 pt-4 border-t border-outline-variant">
@csrf
@method('PATCH')
<label for="category" class="block {{ $sectionLabel }}">{{ __('Category') }}</label>
<div class="flex gap-2">
<select name="category" id="category" class="{{ $inputClass }} flex-1">
@foreach (\App\Models\Ticket::CATEGORIES as $c)
<option value="{{ $c }}" @selected($ticket->category === $c)>{{ \App\Models\Ticket::categoryLabel($c) }}</option>
@endforeach
</select>
<button type="submit" class="{{ $primaryBtn }}">{{ __('Update') }}</button>
</div>
</form>
@endcan

@can('release', $ticket)
<form method="POST" action="{{ route('tickets.release', $ticket) }}" class="mt-4 pt-4 border-t border-outline-variant" onsubmit="return confirm(@json(__('Return this ticket to the queue? You will no longer be its technician.')));">
@csrf
@method('PATCH')
<button type="submit" class="w-full py-2 text-[13px] font-medium border border-outline-variant text-on-surface-variant hover:text-primary hover:border-primary transition-colors rounded flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-[16px]">undo</span>{{ __('Return to queue') }}
</button>
</form>
@endcan
</div>

{{-- Requester feedback (rating left by the employee). A rating is feedback on the
     handling technician's performance, so only the admin (oversight) or the primary
     technician themselves may see it — a peer viewing the ticket for coordination
     must not. --}}
@if ($ticket->isRated() && (auth()->user()->hasRole('admin') || $ticket->agent_id === auth()->id()))
<div class="luxury-card rounded-lg p-6">
<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4">{{ __('Requester feedback') }}</h2>
<div class="flex items-center gap-1 mb-3" dir="ltr">
@for ($i = 1; $i <= 5; $i++)
<span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1; color: {{ $i <= $ticket->rating ? '#c5a059' : '#d1d5db' }}">star</span>
@endfor
<span class="ms-2 text-[13px] font-semibold text-on-surface">{{ $ticket->rating }}/5</span>
</div>
@if ($ticket->rating_comment)
<p class="whitespace-pre-line text-[13px] text-on-surface-variant leading-relaxed">"{{ $ticket->rating_comment }}"</p>
@endif
</div>
@endif

{{-- Team & collaboration --}}
@if ($assigned)
<div class="luxury-card rounded-lg p-6">
<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4">{{ __('Team & collaboration') }}</h2>

<span class="text-[10px] text-on-surface-variant font-bold uppercase tracking-wider mb-2 block">{{ __('Technician') }}</span>
<div class="flex items-center gap-3 rounded-lg border border-outline-variant bg-surface-dim px-4 py-3 mb-4">
<div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary text-[20px]">person</span>
</div>
<div class="min-w-0">
<p class="font-semibold text-on-surface text-[14px] truncate">{{ $ticket->agent->name }}</p>
<p class="text-[11px] text-primary font-semibold uppercase tracking-wide">{{ __('Primary') }}</p>
</div>
</div>

@if ($ticket->collaborators->isNotEmpty())
<span class="text-[10px] text-on-surface-variant font-bold uppercase tracking-wider mb-2 block">{{ __('Collaborators') }}</span>
<ul class="space-y-2 mb-4">
@foreach ($ticket->collaborators as $collaborator)
<li class="flex items-center justify-between rounded-lg border border-outline-variant px-4 py-2.5">
<div class="flex items-center gap-2 min-w-0">
<span class="material-symbols-outlined text-on-surface-variant text-[18px]">group</span>
<span class="font-medium text-on-surface text-[13px] truncate">{{ $collaborator->name }}</span>
</div>
@if ($canShare)
<form method="POST" action="{{ route('tickets.unshare', [$ticket, $collaborator]) }}">
@csrf
@method('DELETE')
<button type="submit" class="text-[12px] font-medium text-red-500 hover:text-red-600 transition-colors">{{ __('Remove') }}</button>
</form>
@endif
</li>
@endforeach
</ul>
@endif

@if ($canShare)
@if ($shareableAgents->isNotEmpty())
<form method="POST" action="{{ route('tickets.share', $ticket) }}" class="space-y-2">
@csrf
<label for="share_user_id" class="block {{ $sectionLabel }}">{{ __('Share with another Technician') }}</label>
<select name="user_id" id="share_user_id" required class="{{ $inputClass }} w-full">
<option value="">{{ __('— Choose a Technician —') }}</option>
@foreach ($shareableAgents as $agent)
<option value="{{ $agent->id }}">{{ $agent->name }}</option>
@endforeach
</select>
<button type="submit" class="{{ $primaryBtn }} w-full">{{ __('Share') }}</button>
</form>
@else
<p class="text-on-surface-variant text-[13px] font-light">{{ __('No other Technicians available to share with.') }}</p>
@endif
@endif

@if ($canReassign && $reassignableAgents->isNotEmpty())
<form method="POST" action="{{ route('tickets.reassign', $ticket) }}" class="space-y-2 mt-4 pt-4 border-t border-outline-variant">
@csrf
@method('PATCH')
<label for="reassign_user_id" class="block {{ $sectionLabel }}">{{ __('Reassign technician') }}</label>
<select name="user_id" id="reassign_user_id" required class="{{ $inputClass }} w-full">
<option value="">{{ __('— Choose new technician —') }}</option>
@foreach ($reassignableAgents as $agent)
<option value="{{ $agent->id }}">{{ $agent->name }}</option>
@endforeach
</select>
<button type="submit" onclick="return confirm(@json(__('Transfer this ticket to the selected Technician? The current primary will lose access.')));"
        class="inline-flex items-center justify-center w-full px-6 py-2.5 rounded-lg bg-secondary text-white text-[13px] font-medium hover:opacity-90 transition-all shadow-sm">{{ __('Reassign') }}</button>
</form>
@endif

@error('user_id')<p class="text-red-500 text-[12px] mt-3">{{ $message }}</p>@enderror
</div>
@endif

</div>
</div>
@endsection
