@extends('layouts.nct')

@section('title', __('Ratings') . ' — ' . config('app.name'))

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between gap-4">
<div>
<p class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-1">{{ __('Operations Overview') }}</p>
<h2 class="text-3xl font-headline font-light text-on-surface leading-tight">@if (app()->getLocale() === 'ar')<span class="font-semibold">التقييمات</span>@else <span class="font-semibold">Ratings</span>@endif</h2>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

{{-- Technicians performance --}}
<div class="luxury-card lg:col-span-5 w-full overflow-x-auto">
<div class="px-5 py-4 border-b border-outline-variant">
<h3 class="text-[12px] font-bold uppercase tracking-widest text-on-surface flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px]" style="color: #c5a059; font-variation-settings: 'FILL' 1">emoji_events</span>{{ __('Technicians') }}
</h3>
</div>
<table class="min-w-full text-[13px]">
<thead>
<tr class="border-b border-outline-variant text-on-surface-variant">
<th class="px-5 py-3 text-start text-[10px] font-bold uppercase tracking-widest">{{ __('Technician') }}</th>
<th class="px-5 py-3 text-center text-[10px] font-bold uppercase tracking-widest">{{ __('Resolved') }}</th>
<th class="px-5 py-3 text-end text-[10px] font-bold uppercase tracking-widest">{{ __('Rating') }}</th>
</tr>
</thead>
<tbody>
@forelse ($technicians as $tech)
@php $isSelected = $selected && $selected->id === $tech->id; @endphp
<tr class="border-b border-outline-variant transition-colors cursor-pointer {{ $isSelected ? 'bg-primary/5' : 'hover:bg-surface-dim' }}"
    onclick="window.location='{{ route('admin.ratings', ['technician' => $tech->id]) }}'">
<td class="px-5 py-3 font-medium {{ $isSelected ? 'text-primary' : 'text-on-surface' }}">
<span class="inline-flex items-center gap-2">
@if ($isSelected)<span class="material-symbols-outlined text-[16px] text-primary">chevron_right</span>@endif{{ $tech->name }}
</span>
</td>
<td class="px-5 py-3 text-center">
<span class="inline-flex items-center gap-1 text-[12px] font-semibold text-emerald-600">
<span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1">task_alt</span>{{ $tech->resolved_count }}
</span>
</td>
<td class="px-5 py-3 text-end whitespace-nowrap">
@if ($tech->ratings_count > 0)
<span class="inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1; color: #c5a059">star</span>
<span class="font-semibold text-on-surface">{{ number_format($tech->avg_rating, 1) }}</span>
<span class="text-on-surface-variant/60 text-[11px]">({{ $tech->ratings_count }})</span>
</span>
@else
<span class="text-on-surface-variant/50 text-[12px]">{{ __('No ratings yet') }}</span>
@endif
</td>
</tr>
@empty
<tr><td colspan="3" class="px-5 py-10 text-center italic text-on-surface-variant">{{ __('No ratings yet') }}</td></tr>
@endforelse
</tbody>
</table>
</div>

{{-- Feedback (admin sees requester identity); filtered when a technician is picked --}}
<div class="luxury-card p-6 lg:col-span-7">
<div class="flex items-center justify-between gap-3 mb-4">
<h3 class="text-[12px] font-bold uppercase tracking-widest text-on-surface flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px] text-primary" style="font-variation-settings: 'FILL' 1">reviews</span>
@if ($selected)
{{ __('Feedback for :name', ['name' => $selected->name]) }}
@if ($selected->ratings_count > 0)
<span class="inline-flex items-center gap-1 ms-2 normal-case tracking-normal">
<span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1; color: #c5a059">star</span>
<span class="text-[13px] font-bold text-on-surface">{{ number_format($selected->avg_rating, 1) }}</span>
<span class="text-[11px] text-on-surface-variant">({{ $selected->ratings_count }})</span>
</span>
@endif
@else
{{ __('Recent feedback') }}
@endif
</h3>
@if ($selected)
<a href="{{ route('admin.ratings') }}" class="text-[11px] text-primary font-bold hover:underline whitespace-nowrap">{{ __('Show all') }}</a>
@endif
</div>

@if ($feedback->isEmpty())
<p class="text-[12px] text-on-surface-variant font-light py-2">{{ __('No feedback yet') }}</p>
@else
<ul class="space-y-4">
@foreach ($feedback as $fb)
<li class="flex items-start gap-3 pb-4 border-b border-outline-variant last:border-0 last:pb-0">
<div class="flex items-center gap-0.5 shrink-0" dir="ltr">
@for ($i = 1; $i <= 5; $i++)
<span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1; color: {{ $i <= $fb->rating ? '#c5a059' : '#d1d5db' }}">star</span>
@endfor
</div>
<div class="min-w-0 flex-1">
<p class="text-[13px] text-on-surface leading-relaxed">"{{ $fb->rating_comment }}"</p>
<p class="text-[11px] text-on-surface-variant mt-1">
<a href="{{ route('tickets.show', $fb) }}" class="text-primary font-semibold hover:underline">#{{ $fb->id }}</a>
· {{ $fb->user?->name }}
@if ($fb->agent) → <span class="font-medium text-on-surface">{{ $fb->agent->name }}</span> @endif
@if ($fb->rated_at) · {{ $fb->rated_at->diffForHumans() }} @endif
</p>
</div>
</li>
@endforeach
</ul>
<div class="mt-4">{!! $feedback->links() !!}</div>
@endif
</div>

</div>
@endsection
