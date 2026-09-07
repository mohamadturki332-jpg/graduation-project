@extends('layouts.nct')

@section('title', __('Operations Report') . ' — ' . config('app.name'))

@push('head')
<style>
    @media print {
        /* Force a light, ink-friendly look no matter the on-screen theme. */
        :root, html.dark {
            --surface: #ffffff; --surface-dim: #f5f5f5; --surface-bright: #ffffff;
            --on-surface: #111111; --on-surface-variant: #555555;
            --outline: #cccccc; --outline-variant: #dddddd;
            --background: #ffffff; --on-background: #111111;
        }
        aside, footer, .no-print { display: none !important; }
        .ml-20, .mr-20 { margin: 0 !important; }
        main { padding: 0 !important; max-width: 100% !important; gap: 1rem !important; }
        .luxury-card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
        section { break-inside: avoid; }
        body { background: #fff !important; }
    }
    @media print {
        @page { margin: 1.4cm; }
    }
</style>
@endpush

@section('content')
@php
    $label = 'text-[11px] font-bold text-on-surface-variant uppercase tracking-widest';
    $periodText = $from && $to
        ? $from->translatedFormat('M j, Y') . ' – ' . $to->translatedFormat('M j, Y')
        : ($from ? __('From') . ' ' . $from->translatedFormat('M j, Y')
            : ($to ? __('To') . ' ' . $to->translatedFormat('M j, Y') : __('All time')));
@endphp

{{-- Controls (screen only) --}}
<div class="no-print flex flex-wrap items-end justify-between gap-4">
<form method="GET" action="{{ route('admin.report') }}" class="flex flex-wrap items-end gap-3">
<div>
<label class="block {{ $label }} mb-1">{{ __('From') }}</label>
<input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border border-outline bg-white px-3 py-2 text-[13px] text-on-surface">
</div>
<div>
<label class="block {{ $label }} mb-1">{{ __('To') }}</label>
<input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border border-outline bg-white px-3 py-2 text-[13px] text-on-surface">
</div>
<button type="submit" class="px-5 py-2 rounded-lg bg-surface-dim border border-outline text-[13px] font-medium text-on-surface hover:bg-surface-bright transition">{{ __('Apply') }}</button>
@if (request('from') || request('to'))
<a href="{{ route('admin.report') }}" class="text-[12px] text-primary font-semibold hover:underline">{{ __('RESET') }}</a>
@endif
</form>
<button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-white text-[13px] font-medium hover:bg-primary/90 transition shadow-sm">
<span class="material-symbols-outlined text-[18px]">print</span>{{ __('Print') }}
</button>
</div>

{{-- Report header --}}
<div class="luxury-card p-6 border-t-2 border-secondary flex items-center justify-between gap-4">
<div>
<p class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-1">{{ config('app.name') }}</p>
<h1 class="text-2xl font-headline font-light text-on-surface">{{ __('Operations Report') }}</h1>
<p class="text-[13px] text-on-surface-variant mt-1">{{ __('Period') }}: <span class="font-semibold text-on-surface">{{ $periodText }}</span></p>
</div>
<div class="text-end text-[12px] text-on-surface-variant">
<p>{{ __('Generated') }}</p>
<p class="font-semibold text-on-surface">{{ now()->translatedFormat('M j, Y g:ia') }}</p>
</div>
</div>

{{-- 1. Summary tiles --}}
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
@php
    $tiles = [
        [__('Total tickets'), $total, 'confirmation_number', 'text-primary'],
        [__('Completed'), $completed, 'task_alt', 'text-emerald-600'],
        [__('Backlog'), $backlog, 'pending_actions', 'text-amber-500'],
        [__('Completion rate'), $completion . '%', 'donut_large', 'text-primary'],
    ];
@endphp
@foreach ($tiles as [$t, $v, $icon, $color])
<div class="luxury-card p-5">
<div class="flex items-center gap-2 mb-2"><span class="material-symbols-outlined text-[18px] {{ $color }}">{{ $icon }}</span><p class="{{ $label }}">{{ $t }}</p></div>
<p class="text-3xl font-headline font-light text-on-surface">{{ $v }}</p>
</div>
@endforeach
</section>

{{-- 2. Resolution time (SLA) --}}
<section class="luxury-card p-6">
<h2 class="{{ $label }} border-b border-outline-variant pb-2 mb-4">{{ __('Resolution time (SLA)') }}</h2>
@if ($resolvedCount === 0)
<p class="text-[13px] text-on-surface-variant font-light">{{ __('No resolution data for this period yet.') }}</p>
@else
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
<div><p class="text-2xl font-headline font-light text-on-surface">{{ \App\Models\Ticket::humanDuration($avgResolutionSeconds) }}</p><p class="text-[11px] text-on-surface-variant mt-1">{{ __('Average') }}</p></div>
<div><p class="text-2xl font-headline font-light text-emerald-600">{{ \App\Models\Ticket::humanDuration($fastestSeconds) }}</p><p class="text-[11px] text-on-surface-variant mt-1">{{ __('Fastest') }}</p></div>
<div><p class="text-2xl font-headline font-light text-amber-500">{{ \App\Models\Ticket::humanDuration($slowestSeconds) }}</p><p class="text-[11px] text-on-surface-variant mt-1">{{ __('Slowest') }}</p></div>
<div><p class="text-2xl font-headline font-light text-on-surface">{{ $resolvedCount }}</p><p class="text-[11px] text-on-surface-variant mt-1">{{ __('Resolved tickets') }}</p></div>
</div>
<p class="text-[11px] text-on-surface-variant/70 mt-4 italic">{{ __('Measured from submission to the first solved state; older tickets without tracking are excluded.') }}</p>
@endif
</section>

{{-- 3. Breakdowns --}}
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
@php
    $breakdowns = [
        [__('By status'), collect($statusStats)->map(fn ($v, $k) => [\App\Models\Ticket::statusMeta($k)['label'], $v])],
        [__('By priority'), collect($priorityStats)->map(fn ($v, $k) => [\App\Models\Ticket::priorityLabel($k), $v])],
        [__('By category'), collect($categoryStats)->map(fn ($v, $k) => [\App\Models\Ticket::categoryLabel($k), $v])],
    ];
@endphp
@foreach ($breakdowns as [$heading, $rows])
<div class="luxury-card p-5">
<h2 class="{{ $label }} border-b border-outline-variant pb-2 mb-3">{{ $heading }}</h2>
<ul class="space-y-2">
@foreach ($rows as [$name, $val])
<li class="flex items-center justify-between text-[13px]">
<span class="text-on-surface-variant">{{ $name }}</span>
<span class="font-semibold text-on-surface">{{ $val }} <span class="text-on-surface-variant/60 font-normal">({{ $total ? round($val / $total * 100) : 0 }}%)</span></span>
</li>
@endforeach
</ul>
</div>
@endforeach
</section>

{{-- 4. Channel --}}
<section class="luxury-card p-5">
<h2 class="{{ $label }} border-b border-outline-variant pb-2 mb-3">{{ __('Channel') }}</h2>
<div class="grid grid-cols-2 gap-4">
<div class="flex items-center gap-3"><span class="material-symbols-outlined text-secondary">mail</span><div><p class="text-xl font-headline font-light text-on-surface">{{ $emailCount }}</p><p class="text-[11px] text-on-surface-variant">{{ __('By email') }}</p></div></div>
<div class="flex items-center gap-3"><span class="material-symbols-outlined text-primary">public</span><div><p class="text-xl font-headline font-light text-on-surface">{{ $webCount }}</p><p class="text-[11px] text-on-surface-variant">{{ __('Web form') }}</p></div></div>
</div>
</section>

{{-- 5. Technician performance --}}
<section class="luxury-card w-full overflow-x-auto">
<div class="px-5 py-4 border-b border-outline-variant"><h2 class="{{ $label }}">{{ __('Technician performance') }}</h2></div>
<table class="min-w-full text-[13px]">
<thead>
<tr class="border-b border-outline-variant text-on-surface-variant">
<th class="px-5 py-3 text-start text-[10px] font-bold uppercase tracking-widest">{{ __('Technician') }}</th>
<th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-widest">{{ __('Resolved') }}</th>
<th class="px-5 py-3 text-end text-[10px] font-bold uppercase tracking-widest">{{ __('Rating') }}</th>
</tr>
</thead>
<tbody>
@foreach ($technicians as $tech)
<tr class="border-b border-outline-variant">
<td class="px-5 py-3 text-on-surface font-medium">{{ $tech->name }}</td>
<td class="px-5 py-3 text-center font-semibold text-emerald-600">{{ $tech->resolved_count }}</td>
<td class="px-5 py-3 text-end">
@if ($tech->ratings_count > 0)
<span class="font-semibold text-on-surface">{{ number_format($tech->avg_rating, 1) }}</span> <span class="text-on-surface-variant/60 text-[11px]">({{ $tech->ratings_count }})</span>
@else
<span class="text-on-surface-variant/50 text-[12px]">{{ __('No ratings yet') }}</span>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</section>

{{-- 6. Customer satisfaction --}}
<section class="luxury-card p-6">
<div class="flex items-center justify-between border-b border-outline-variant pb-2 mb-4">
<h2 class="{{ $label }}">{{ __('Customer satisfaction') }}</h2>
@if ($ratedCount > 0)
<span class="inline-flex items-center gap-1 text-[13px]"><span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1; color: #c5a059">star</span><span class="font-bold text-on-surface">{{ number_format($avgRating, 1) }}</span><span class="text-on-surface-variant">/ 5 · {{ $ratedCount }}</span></span>
@endif
</div>
@if ($feedback->isEmpty())
<p class="text-[13px] text-on-surface-variant font-light">{{ __('No feedback yet') }}</p>
@else
<ul class="space-y-3">
@foreach ($feedback as $fb)
<li class="flex items-start gap-3 pb-3 border-b border-outline-variant last:border-0 last:pb-0">
<div class="flex items-center gap-0.5 shrink-0" dir="ltr">
@for ($i = 1; $i <= 5; $i++)
<span class="material-symbols-outlined text-[13px]" style="font-variation-settings: 'FILL' 1; color: {{ $i <= $fb->rating ? '#c5a059' : '#d1d5db' }}">star</span>
@endfor
</div>
<div class="min-w-0 flex-1">
<p class="text-[13px] text-on-surface">"{{ $fb->rating_comment }}"</p>
<p class="text-[11px] text-on-surface-variant mt-0.5">#{{ $fb->id }} · {{ $fb->user?->name }}@if ($fb->agent) → {{ $fb->agent->name }}@endif</p>
</div>
</li>
@endforeach
</ul>
@endif
</section>
@endsection
