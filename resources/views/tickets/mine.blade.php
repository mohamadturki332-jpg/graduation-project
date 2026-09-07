@extends('layouts.nct')

@section('title', __('My tickets') . ' — ' . config('app.name'))

@section('content')
{{-- Density header: breadcrumb + heading + total --}}
<div class="flex items-center justify-between gap-4">
<div>
<div class="flex items-center gap-2 text-[10px] uppercase font-bold text-on-surface-variant mb-2 tracking-[0.2em]">
<span>{{ __('Tickets') }}</span>
<span class="material-symbols-outlined text-[12px]">chevron_right</span>
<span class="text-primary">{{ __('Assigned to me') }}</span>
</div>
<h2 class="text-3xl font-headline font-light text-on-surface leading-tight">@if (app()->getLocale() === 'ar')<span class="font-semibold">تذاكري</span>@else My <span class="font-semibold">tickets</span>@endif</h2>
</div>
<div class="flex items-center gap-3 shrink-0">
<div class="text-end">
<p class="text-[10px] text-on-surface-variant font-bold uppercase tracking-widest">{{ __('Total') }}</p>
<p class="text-sm font-bold text-primary">{{ $tickets->total() }} {{ __('tickets') }}</p>
</div>
</div>
</div>

@if ($tickets->isEmpty())
<div class="luxury-card p-16 text-center">
<p class="text-on-surface-variant font-light mb-5">{{ __("You haven't picked any tickets yet.") }}</p>
<a href="{{ route('tickets.index') }}" class="inline-block px-8 py-3 text-[13px] font-medium bg-primary text-white hover:bg-primary/90 transition-all shadow-sm">{{ __('Browse unassigned tickets') }}</a>
</div>
@else
<div class="flex flex-col lg:flex-row gap-6 items-start">
{{-- Dense tickets table --}}
<div class="luxury-card flex-1 min-w-0 w-full overflow-x-auto">
<table class="w-full table-fixed text-start border-collapse">
<thead>
<tr class="border-b border-outline-variant bg-surface-dim text-[10px] font-bold text-primary uppercase tracking-widest">
<th class="px-4 py-3 w-14 text-start">{{ __('ID') }}</th>
<th class="px-4 py-3 text-start">{{ __('Subject') }}</th>
<th class="px-4 py-3 text-start">{{ __('Requester') }}</th>
<th class="px-4 py-3 w-20 text-start">{{ __('Site') }}</th>
<th class="px-4 py-3 text-start">{{ __('Assigned to') }}</th>
<th class="px-4 py-3 w-32 text-start">{{ __('Category') }}</th>
<th class="px-4 py-3 w-24 text-start">{{ __('Priority') }}</th>
<th class="px-4 py-3 w-40 text-start">{{ __('Status') }}</th>
<th class="px-4 py-3 w-28 text-start">{{ __('Submitted') }}</th>
</tr>
</thead>
<tbody>
@foreach ($tickets as $ticket)
@php
    $priorityBadge = match ($ticket->priority) {
        'high' => ['bg-red-50 text-red-600 border-red-200', 'bg-red-500'],
        'medium' => ['bg-amber-50 text-amber-700 border-amber-200', 'bg-amber-500'],
        default => ['bg-slate-100 text-slate-600 border-slate-200', 'bg-slate-400'],
    };
    $statusMeta = \App\Models\Ticket::statusMeta($ticket->status);
    $agentName = $ticket->agent?->name;
    $initials = $agentName
        ? collect(explode(' ', $agentName))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('')
        : null;
    $dept = $ticket->user?->department;
    $site = $dept ? (in_array($dept, ['it', 'hr']) ? strtoupper($dept) : ucfirst($dept)) : '—';
    // Email tickets are attributed to the "External Sender" placeholder — show the
    // real sender's name (or email) captured on the ticket instead.
    $requester = $ticket->source_email
        ? ($ticket->source_name ?: $ticket->source_email)
        : ($ticket->user?->name ?? '—');
@endphp
<tr class="group border-b border-outline-variant/60 text-[12px] hover:bg-surface-dim transition-colors">
<td class="px-4 py-2.5 font-mono text-on-surface-variant">#{{ $ticket->id }}</td>
<td class="px-4 py-2.5">
<a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-primary hover:underline block truncate">{{ $ticket->title }}</a>
</td>
<td class="px-4 py-2.5 text-on-surface-variant overflow-hidden">
<span class="block truncate">{{ $requester }}</span>
@if ($ticket->source_email)
<span class="block truncate text-[10px] text-on-surface-variant/60">{{ $ticket->source_email }} · {{ __('by email') }}</span>
@endif
</td>
<td class="px-4 py-2.5 text-on-surface-variant truncate">{{ $site }}</td>
<td class="px-4 py-2.5 overflow-hidden">
@if ($agentName)
<div class="flex items-center gap-2 min-w-0">
<div class="w-5 h-5 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-[10px] font-bold uppercase shrink-0">{{ $initials }}</div>
<span class="truncate">{{ $agentName }}</span>
</div>
@else
<span class="text-on-surface-variant">{{ __('Unassigned') }}</span>
@endif
</td>
<td class="px-4 py-2.5 text-on-surface-variant truncate">{{ \App\Models\Ticket::categoryLabel($ticket->category) }}</td>
<td class="px-4 py-2.5">
<span class="px-2 py-0.5 rounded-full border text-[10px] font-bold flex items-center gap-1 w-fit {{ $priorityBadge[0] }}">
<span class="w-1.5 h-1.5 rounded-full {{ $priorityBadge[1] }}"></span>{{ \App\Models\Ticket::priorityLabel($ticket->priority) }}
</span>
</td>
<td class="px-4 py-2.5">
<span class="px-2 py-0.5 rounded-full border text-[10px] font-bold w-fit inline-block whitespace-nowrap {{ $statusMeta['badge'] }}">{{ $statusMeta['label'] }}</span>
</td>
<td class="px-4 py-2.5 text-on-surface-variant whitespace-nowrap">{{ $ticket->created_at->diffForHumans() }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>

{{-- Operational filters (visual — wiring optional) --}}
<aside class="luxury-card w-full lg:w-72 shrink-0 flex flex-col">
<div class="px-4 py-3 border-b border-outline-variant flex items-center justify-between">
<span class="text-[11px] font-bold uppercase text-primary tracking-widest flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">filter_list</span>{{ __('Filters') }}
</span>
<button type="button" class="text-[10px] text-primary font-bold hover:underline">{{ __('RESET') }}</button>
</div>
<div class="p-4 space-y-6">
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Keyword') }}</label>
<div class="relative">
<input type="text" placeholder="{{ __('Title or ID…') }}" class="w-full border-outline-variant rounded bg-white text-xs py-2 pe-8 focus:ring-1 focus:ring-primary focus:border-primary" />
<span class="material-symbols-outlined absolute end-2 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">search</span>
</div>
</div>
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Status') }}</label>
<div class="space-y-2">
@foreach (\App\Models\Ticket::STATUSES as $status)
<label class="flex items-center gap-3 cursor-pointer">
<input type="checkbox" class="rounded text-primary focus:ring-primary" />
<span class="text-xs font-medium">{{ \App\Models\Ticket::statusMeta($status)['label'] }}</span>
</label>
@endforeach
</div>
</div>
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Priority') }}</label>
<div class="grid grid-cols-3 gap-1">
@foreach (\App\Models\Ticket::PRIORITIES as $priority)
<button type="button" class="py-2 border border-outline-variant rounded bg-white text-[10px] font-bold hover:border-primary hover:bg-primary/5 transition-colors">{{ \App\Models\Ticket::priorityLabel($priority) }}</button>
@endforeach
</div>
</div>
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Category') }}</label>
<div class="flex flex-wrap gap-2">
@foreach (\App\Models\Ticket::CATEGORIES as $category)
<span class="px-2 py-1 bg-white border border-outline-variant rounded text-[10px] font-bold cursor-pointer hover:bg-primary hover:text-white hover:border-primary transition-all">{{ \App\Models\Ticket::categoryLabel($category) }}</span>
@endforeach
</div>
</div>
</div>
<div class="p-4 border-t border-outline-variant mt-auto">
<button type="button" class="w-full py-2 bg-primary text-white rounded text-xs font-bold hover:brightness-110 transition">{{ __('APPLY FILTERS') }}</button>
</div>
</aside>
</div>

<div class="mt-2">{!! $tickets->links() !!}</div>
@endif
@endsection
