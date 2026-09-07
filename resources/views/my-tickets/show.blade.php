@extends('layouts.nct')

@section('title', __('Ticket') . ' #' . $ticket->id . ' — ' . $ticket->title)

@section('content')
@php
    $statusMeta = \App\Models\Ticket::statusMeta($ticket->status);
    $priorityColor = match ($ticket->priority) {
        'high' => 'text-red-600', 'medium' => 'text-amber-500', 'low' => 'text-gray-500', default => 'text-gray-500',
    };
    $sectionLabel = 'text-[11px] font-bold text-on-surface-variant uppercase tracking-widest';
@endphp

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">
<a href="{{ route('my-tickets.index') }}" class="hover:text-primary transition-colors">{{ __('My tickets') }}</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-primary">#{{ $ticket->id }}</span>
</div>

{{-- Header card (gold top accent) --}}
<div class="luxury-card rounded-lg p-8 border-t-2 border-secondary flex flex-wrap items-center gap-3">
<h1 class="text-2xl font-headline font-light text-on-surface">
<span class="text-on-surface-variant/50">#{{ $ticket->id }}</span> {{ $ticket->title }}
</h1>
<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-dim border border-outline-variant text-[11px] font-bold uppercase tracking-wider text-on-surface">
<span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }}"></span>{{ $statusMeta['label'] }}
</span>
</div>

{{-- Two-column workspace --}}
<div class="grid grid-cols-12 gap-8 items-start">

{{-- LEFT: details + conversation --}}
<div class="col-span-12 lg:col-span-8 flex flex-col gap-8">

<div class="luxury-card rounded-lg p-8">
<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4">{{ __('Description') }}</h2>
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
    $url = route('my-tickets.attachment', [$ticket, $attachment]);
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

{{-- RIGHT: read-only details --}}
<div class="col-span-12 lg:col-span-4 flex flex-col gap-8">
<div class="luxury-card rounded-lg p-6">
<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4">{{ __('Details') }}</h2>
<dl class="space-y-4 text-[13px]">
<div class="flex items-center justify-between gap-4">
<dt class="text-on-surface-variant">{{ __('Status') }}</dt>
<dd class="inline-flex items-center gap-2 font-semibold text-on-surface"><span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }}"></span>{{ $statusMeta['label'] }}</dd>
</div>
<div class="flex items-center justify-between gap-4">
<dt class="text-on-surface-variant">{{ __('Priority') }}</dt>
<dd class="font-semibold {{ $priorityColor }}">{{ \App\Models\Ticket::priorityLabel($ticket->priority) }}</dd>
</div>
<div class="flex items-center justify-between gap-4">
<dt class="text-on-surface-variant">{{ __('Category') }}</dt>
<dd class="font-semibold text-primary">{{ \App\Models\Ticket::categoryLabel($ticket->category) }}</dd>
</div>
<div class="flex items-center justify-between gap-4">
<dt class="text-on-surface-variant">{{ __('Submitted') }}</dt>
<dd class="font-medium text-on-surface text-end">{{ $ticket->created_at->translatedFormat('M j, Y g:ia') }}</dd>
</div>
</dl>
<p class="mt-5 pt-4 border-t border-outline-variant text-[12px] text-on-surface-variant font-light leading-relaxed">
{{ __("Post a message below to reach the support team. You'll get an email when a technician replies.") }}
</p>
</div>

{{-- Rate the technician (only once the ticket is solved) --}}
@if ($ticket->canBeRated())
<div class="luxury-card rounded-lg p-6">
<h2 class="{{ $sectionLabel }} border-b border-outline-variant pb-2 mb-4">{{ __('Rate the technician') }}</h2>

@if ($ticket->agent)
<p class="text-[13px] text-on-surface-variant mb-4">
{{ __('How was your experience with :name?', ['name' => $ticket->agent->name]) }}
</p>
@endif

<form method="POST" action="{{ route('my-tickets.rate', $ticket) }}" class="space-y-4">
@csrf
<input type="hidden" name="rating" id="rating-value" value="{{ old('rating', $ticket->rating) }}">
<div id="star-rating" class="flex items-center gap-1" dir="ltr" role="radiogroup" aria-label="{{ __('Rating') }}">
@for ($i = 1; $i <= 5; $i++)
<button type="button" data-star="{{ $i }}" aria-label="{{ $i }}"
    class="star-btn text-3xl leading-none transition-colors focus:outline-none">
<span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1">star</span>
</button>
@endfor
</div>
@error('rating')<p class="text-red-500 text-[12px]">{{ $message }}</p>@enderror

<textarea name="rating_comment" rows="3" maxlength="1000"
    placeholder="{{ __('Add a comment (optional)…') }}"
    class="block w-full rounded-lg border border-outline bg-white px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors">{{ old('rating_comment', $ticket->rating_comment) }}</textarea>
@error('rating_comment')<p class="text-red-500 text-[12px]">{{ $message }}</p>@enderror

<button type="submit" class="inline-flex items-center justify-center gap-2 w-full px-6 py-2.5 rounded-lg bg-primary text-white text-[13px] font-medium hover:bg-primary/90 transition-all shadow-sm">
<span class="material-symbols-outlined text-[18px]">send</span>
{{ $ticket->isRated() ? __('Update rating') : __('Submit rating') }}
</button>
</form>

<script>
(function () {
    const wrap = document.getElementById('star-rating');
    const input = document.getElementById('rating-value');
    if (!wrap || !input) return;
    const stars = Array.from(wrap.querySelectorAll('.star-btn'));

    function paint(value) {
        stars.forEach(function (btn) {
            const on = Number(btn.dataset.star) <= value;
            btn.style.color = on ? '#c5a059' : '#d1d5db';
            btn.setAttribute('aria-checked', Number(btn.dataset.star) === Number(input.value) ? 'true' : 'false');
        });
    }

    stars.forEach(function (btn) {
        const v = Number(btn.dataset.star);
        btn.addEventListener('mouseenter', function () { paint(v); });
        btn.addEventListener('click', function () { input.value = v; paint(v); });
    });
    wrap.addEventListener('mouseleave', function () { paint(Number(input.value) || 0); });

    paint(Number(input.value) || 0);
})();
</script>
</div>
@endif

</div>

</div>
@endsection
