@extends('layouts.nct')

@section('title', __('My ratings') . ' — ' . config('app.name'))

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between gap-4">
<div>
<div class="flex items-center gap-2 text-[10px] uppercase font-bold text-on-surface-variant mb-2 tracking-[0.2em]">
<span>{{ __('Tickets') }}</span>
<span class="material-symbols-outlined text-[12px]">chevron_right</span>
<span class="text-primary">{{ __('My ratings') }}</span>
</div>
<h2 class="text-3xl font-headline font-light text-on-surface leading-tight">@if (app()->getLocale() === 'ar')<span class="font-semibold">تقييماتي</span>@else My <span class="font-semibold">ratings</span>@endif</h2>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

{{-- Summary: average + star distribution --}}
<div class="luxury-card p-6 lg:col-span-5">
@if ($count === 0)
<div class="text-center py-8">
<span class="material-symbols-outlined text-[40px] text-on-surface-variant/40" style="font-variation-settings: 'FILL' 1">grade</span>
<p class="text-[13px] text-on-surface-variant font-light mt-2">{{ __('No ratings yet') }}</p>
</div>
@else
<div class="flex items-end gap-4 mb-5">
<span class="text-5xl font-headline font-light text-on-surface leading-none">{{ number_format($avg, 1) }}</span>
<div class="pb-1">
<div class="flex items-center gap-0.5" dir="ltr">
@for ($i = 1; $i <= 5; $i++)
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1; color: {{ $i <= round($avg) ? '#c5a059' : '#d1d5db' }}">star</span>
@endfor
</div>
<p class="text-[11px] text-on-surface-variant mt-1">{{ __('Based on :count ratings', ['count' => $count]) }}</p>
</div>
</div>

<div class="space-y-2">
@foreach ($distribution as $star => $n)
@php $pct = $count ? round($n / $count * 100) : 0; @endphp
<div class="flex items-center gap-2">
<span class="text-[11px] font-semibold text-on-surface-variant w-8 shrink-0 flex items-center gap-0.5">{{ $star }}<span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1; color: #c5a059">star</span></span>
<div class="flex-1 h-2 rounded-full bg-surface-dim overflow-hidden">
<div class="h-full rounded-full" style="width: {{ $pct }}%; background: #c5a059"></div>
</div>
<span class="text-[11px] text-on-surface-variant w-6 text-end shrink-0">{{ $n }}</span>
</div>
@endforeach
</div>
@endif
</div>

{{-- Feedback list (anonymous) --}}
<div class="luxury-card p-6 lg:col-span-7">
<h3 class="text-[12px] font-bold uppercase tracking-widest text-on-surface flex items-center gap-1.5 mb-4">
<span class="material-symbols-outlined text-[18px] text-primary" style="font-variation-settings: 'FILL' 1">reviews</span>{{ __('Recent feedback') }}
</h3>

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
@if ($fb->rated_at)<p class="text-[11px] text-on-surface-variant mt-0.5">{{ $fb->rated_at->diffForHumans() }}</p>@endif
</div>
</li>
@endforeach
</ul>
<div class="mt-4">{!! $feedback->links() !!}</div>
@endif
</div>

</div>
@endsection
