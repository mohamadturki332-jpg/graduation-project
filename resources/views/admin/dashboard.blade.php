@extends('layouts.nct')

@section('title', __('Dashboard') . ' — ' . config('app.name'))

@section('content')
@php
    $statuses = \App\Models\Ticket::STATUSES;
    $total = array_sum($stats);

    $blocked   = $stats['on_hold'] + $stats['waiting_for_resources'];
    $completed = $stats['resolved'] + $stats['closed'] + $stats['rejected'];
    $completion = $total ? round($completed / $total * 100) : 0;

    // Hex mirrors of the statusMeta palette — needed for the conic-gradient donut.
    $statusHex = [
        'open' => '#3b82f6', 'in_progress' => '#f59e0b', 'on_hold' => '#f97316',
        'waiting_for_resources' => '#6366f1', 'rejected' => '#ef4444',
        'closed' => '#a1a1aa', 'resolved' => '#10b981',
    ];

    if ($total === 0) {
        $donutGradient = '#e5e5e5 0% 100%';
    } else {
        $segments = [];
        $cursor = 0;
        foreach ($statuses as $s) {
            if ($stats[$s] === 0) { continue; }
            $start = round($cursor / $total * 100, 2);
            $cursor += $stats[$s];
            $end = round($cursor / $total * 100, 2);
            $segments[] = "{$statusHex[$s]} {$start}% {$end}%";
        }
        $donutGradient = implode(', ', $segments);
    }

    $priorityMax = max($priorityStats['high'], $priorityStats['medium'], $priorityStats['low'], 1);
    $pw = fn ($v) => (int) round($v / $priorityMax * 100);
@endphp

<!-- Welcome Section -->
<div class="flex flex-col md:flex-row justify-between items-baseline gap-4">
<div class="max-w-2xl">
<p class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-1">{{ __('Operations Overview') }}</p>
<h2 class="text-2xl font-headline font-light text-on-surface leading-tight">{{ __('Welcome back,') }} <span class="font-semibold">{{ auth()->user()->name }}</span></h2>
</div>
</div>

<!-- Metrics Grid -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
<div class="luxury-card p-5">
<div class="flex items-center gap-3 mb-3">
<span class="material-symbols-outlined text-blue-500" style="font-variation-settings: 'wght' 200; font-size: 22px">inbox</span>
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.15em]">{{ __('Open') }}</p>
</div>
<div class="flex items-baseline justify-between">
<span class="text-3xl font-headline font-light text-on-surface">{{ $stats['open'] }}</span>
<span class="text-[11px] text-on-surface-variant font-light">{{ __('Awaiting assignment') }}</span>
</div>
</div>
<div class="luxury-card p-5">
<div class="flex items-center gap-3 mb-3">
<span class="material-symbols-outlined text-amber-500" style="font-variation-settings: 'wght' 200; font-size: 22px">precision_manufacturing</span>
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.15em]">{{ __('In Progress') }}</p>
</div>
<div class="flex items-baseline justify-between">
<span class="text-3xl font-headline font-light text-on-surface">{{ $stats['in_progress'] }}</span>
<span class="text-[11px] text-on-surface-variant font-light">{{ __('Being worked on') }}</span>
</div>
</div>
<div class="luxury-card p-5">
<div class="flex items-center gap-3 mb-3">
<span class="material-symbols-outlined text-orange-500" style="font-variation-settings: 'wght' 200; font-size: 22px">pause_circle</span>
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.15em]">{{ __('Blocked') }}</p>
</div>
<div class="flex items-baseline justify-between">
<span class="text-3xl font-headline font-light text-on-surface">{{ $blocked }}</span>
<span class="text-[11px] text-on-surface-variant font-light">{{ __('On hold / waiting') }}</span>
</div>
</div>
<div class="luxury-card p-5 border-b-2 border-b-emerald-500/30">
<div class="flex items-center gap-3 mb-3">
<span class="material-symbols-outlined text-emerald-500" style="font-variation-settings: 'wght' 200; font-size: 22px">task_alt</span>
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.15em]">{{ __('Completed') }}</p>
</div>
<div class="flex items-baseline justify-between">
<span class="text-3xl font-headline font-light text-on-surface">{{ $completed }}</span>
<span class="text-[11px] text-on-surface-variant font-light">{{ __('Resolved / closed') }}</span>
</div>
</div>
</section>

<!-- Technician leaderboard -->
<section class="luxury-card p-4">
<div class="flex items-center gap-3 mb-3">
<h3 class="text-[11px] font-bold uppercase tracking-widest text-on-surface flex items-center gap-1.5 flex-1 min-w-0">
<span class="material-symbols-outlined text-[16px]" style="color: #c5a059; font-variation-settings: 'FILL' 1">emoji_events</span>{{ __('Technician leaderboard') }}
</h3>
<span class="w-14 text-center text-[10px] text-on-surface-variant uppercase tracking-wider shrink-0">{{ __('Resolved') }}</span>
<span class="w-14 text-center text-[10px] text-on-surface-variant uppercase tracking-wider shrink-0">{{ __('Assists') }}</span>
<span class="w-14 text-center text-[10px] text-on-surface-variant uppercase tracking-wider shrink-0">{{ __('Rating') }}</span>
</div>

@if ($leaderboard->isEmpty())
<p class="text-[12px] text-on-surface-variant font-light py-2 text-center">{{ __('No resolved tickets yet') }}</p>
@else
@php $rankColors = ['#c5a059', '#9ca3af', '#b45309']; @endphp
<ul class="divide-y divide-outline-variant">
@foreach ($leaderboard as $i => $tech)
<li class="flex items-center gap-3 py-2 first:pt-0 last:pb-0">
<span class="w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 {{ $i < 3 ? 'text-white' : 'text-on-surface-variant bg-surface-dim' }}"
      @if ($i < 3) style="background: {{ $rankColors[$i] }}" @endif>{{ $i + 1 }}</span>
<span class="text-[13px] text-on-surface font-medium truncate flex-1 min-w-0">{{ $tech->name }}</span>
<span class="inline-flex items-center justify-center gap-1 w-14 text-[12px] font-semibold text-emerald-600 shrink-0" title="{{ __('Resolved tickets') }}">
<span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1">task_alt</span>{{ $tech->resolved_count }}
</span>
<span class="inline-flex items-center justify-center gap-1 w-14 text-[12px] font-semibold shrink-0 {{ $tech->assists_count > 0 ? 'text-sky-600' : 'text-on-surface-variant/50' }}" title="{{ __('Tickets assisted on') }}">
<span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1">handshake</span>{{ $tech->assists_count }}
</span>
<span class="inline-flex items-center justify-center gap-1 w-14 shrink-0" title="{{ __('Average rating') }}">
@if ($tech->ratings_count > 0)
<span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1; color: #c5a059">star</span>
<span class="text-[12px] font-semibold text-on-surface">{{ number_format($tech->avg_rating, 1) }}</span>
@else
<span class="text-[11px] text-on-surface-variant/50">—</span>
@endif
</span>
</li>
@endforeach
</ul>
@endif
</section>

<!-- Details Grid -->
<section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
<div class="luxury-card p-5 lg:col-span-4 flex flex-col items-center">
<div class="w-full flex justify-between items-center mb-2">
<h3 class="text-[12px] font-bold uppercase tracking-widest text-on-surface">{{ __('Ticket status') }}</h3>
<div class="w-1 h-1 rounded-full bg-outline"></div>
</div>
<div class="chart-donut-minimal" style="background: conic-gradient({{ $donutGradient }}); transform: scale(0.8); margin: -14px 0;">
<div class="chart-inner-content">
<span class="text-4xl font-headline font-light text-on-surface">{{ $completion }}%</span>
<p class="text-[9px] text-on-surface-variant font-bold uppercase tracking-[0.2em] mt-1">{{ __('Completion') }}</p>
</div>
</div>
<div class="w-full mt-3 space-y-1.5">
@foreach ($statuses as $s)
@php $meta = \App\Models\Ticket::statusMeta($s); $pct = $total ? round($stats[$s] / $total * 100) : 0; @endphp
<div class="flex items-center justify-between group">
<div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full" style="background: {{ $statusHex[$s] }}"></div><span class="text-[11px] text-on-surface-variant group-hover:text-on-surface transition-colors">{{ $meta['label'] }}</span></div>
<span class="text-[11px] font-semibold">{{ $stats[$s] }} <span class="text-on-surface-variant font-normal">({{ $pct }}%)</span></span>
</div>
@endforeach
</div>
</div>
<div class="luxury-card p-5 lg:col-span-8">
<div class="flex justify-between items-center mb-5">
<h3 class="text-[12px] font-bold uppercase tracking-widest text-on-surface">{{ __('Distribution by priority') }}</h3>
<div class="flex items-center gap-2"><div class="w-1 h-1 rounded-full bg-primary"></div><span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">{{ __('Current') }}</span></div>
</div>
<div class="space-y-4">
<div class="group">
<div class="flex justify-between items-baseline mb-2"><span class="text-[13px] font-medium text-on-surface">{{ __('High') }}</span><span class="text-[11px] font-bold text-on-surface-variant">{{ $priorityStats['high'] }} {{ __('tickets') }}</span></div>
<div class="bar-track-minimal"><div class="bar-fill-minimal bg-primary" style="width: {{ $pw($priorityStats['high']) }}%"></div></div>
</div>
<div class="group">
<div class="flex justify-between items-baseline mb-2"><span class="text-[13px] font-medium text-on-surface">{{ __('Medium') }}</span><span class="text-[11px] font-bold text-on-surface-variant">{{ $priorityStats['medium'] }} {{ __('tickets') }}</span></div>
<div class="bar-track-minimal"><div class="bar-fill-minimal bg-secondary" style="width: {{ $pw($priorityStats['medium']) }}%"></div></div>
</div>
<div class="group">
<div class="flex justify-between items-baseline mb-2"><span class="text-[13px] font-medium text-on-surface">{{ __('Low') }}</span><span class="text-[11px] font-bold text-on-surface-variant">{{ $priorityStats['low'] }} {{ __('tickets') }}</span></div>
<div class="bar-track-minimal"><div class="bar-fill-minimal bg-on-surface-variant/20" style="width: {{ $pw($priorityStats['low']) }}%"></div></div>
</div>
</div>
</div>
</section>
@endsection
