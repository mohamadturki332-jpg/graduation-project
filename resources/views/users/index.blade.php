@extends('layouts.nct')

@section('title', __('Manage users') . ' — ' . config('app.name'))

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-baseline gap-4">
<div>
<p class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-4">{{ __('Access Management') }}</p>
<h2 class="text-3xl font-headline font-light text-on-surface leading-tight">@if (app()->getLocale() === 'ar')<span class="font-semibold">إدارة المستخدمين</span>@else Manage <span class="font-semibold">users</span>@endif</h2>
</div>
<div class="flex items-center gap-4">
<p class="text-[12px] text-on-surface-variant uppercase tracking-widest">{{ $users->total() }} {{ __('total') }}</p>
<a href="{{ route('users.create') }}" class="px-8 py-3 text-[13px] font-medium bg-primary text-white hover:bg-primary/90 transition-all shadow-sm">{{ __('New user') }}</a>
</div>
</div>

<div class="flex flex-col lg:flex-row gap-6 items-start">
<div class="luxury-card flex-1 min-w-0 w-full overflow-x-auto">
<table class="min-w-full text-[13px]">
<thead>
<tr class="border-b border-outline-variant text-on-surface-variant">
<th class="px-6 py-4 text-start text-[10px] font-bold uppercase tracking-widest">{{ __('Name') }}</th>
<th class="px-6 py-4 text-start text-[10px] font-bold uppercase tracking-widest">{{ __('Email') }}</th>
<th class="px-6 py-4 text-start text-[10px] font-bold uppercase tracking-widest">{{ __('Role') }}</th>
<th class="px-6 py-4 text-start text-[10px] font-bold uppercase tracking-widest">{{ __('Department') }}</th>
<th class="px-6 py-4 text-end text-[10px] font-bold uppercase tracking-widest">{{ __('Actions') }}</th>
</tr>
</thead>
<tbody>
@forelse ($users as $u)
@php
    $roleColor = match ($u->role) {
        'admin'    => 'text-primary',
        'agent'    => 'text-secondary',
        'employee' => 'text-on-surface-variant',
        default    => 'text-on-surface-variant',
    };
    $roleLabel = $u->jobTitle();
@endphp
<tr class="border-b border-outline-variant hover:bg-surface-dim transition-colors">
<td class="px-6 py-4 text-on-surface font-medium">
{{ $u->name }}
@if ($u->id === auth()->id())
<span class="text-on-surface-variant/60 text-[11px] ms-1">({{ __('you') }})</span>
@endif
</td>
<td class="px-6 py-4 text-on-surface-variant">{{ $u->email }}</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-2 text-[12px] font-semibold {{ $roleColor }} uppercase tracking-wide">
<span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $roleLabel }}
</span>
</td>
<td class="px-6 py-4 text-on-surface-variant">
{{ __(in_array($u->department, ['it', 'hr'], true) ? strtoupper($u->department) : ucfirst((string) $u->department)) }}
</td>
<td class="px-6 py-4 text-end whitespace-nowrap">
<a href="{{ route('users.edit', $u) }}" class="text-[12px] font-medium text-on-surface hover:text-primary transition-colors me-4">{{ __('Edit') }}</a>
@if ($u->id !== auth()->id())
<form method="POST" action="{{ route('users.destroy', $u) }}" class="inline" onsubmit="return confirm(@json(__('Delete this user? Any tickets they submitted will be permanently deleted too.')));">
@csrf
@method('DELETE')
<button type="submit" class="text-[12px] font-medium text-red-500 hover:text-red-600 transition-colors">{{ __('Delete') }}</button>
</form>
@endif
</td>
</tr>
@empty
<tr><td colspan="5" class="px-6 py-16 text-center italic text-on-surface-variant">{{ __('No users match your filters.') }}</td></tr>
@endforelse
</tbody>
</table>
</div>

{{-- Filters --}}
<aside class="luxury-card w-full lg:w-72 shrink-0">
<form method="GET" action="{{ route('users.index') }}" class="flex flex-col h-full">
<div class="px-4 py-3 border-b border-outline-variant flex items-center justify-between">
<span class="text-[11px] font-bold uppercase text-primary tracking-widest flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">filter_list</span>{{ __('Filters') }}
</span>
<a href="{{ route('users.index') }}" class="text-[10px] text-primary font-bold hover:underline">{{ __('RESET') }}</a>
</div>
<div class="p-4 space-y-6">
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Keyword') }}</label>
<div class="relative">
<input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="{{ __('Name, email, or ID…') }}" class="w-full border-outline-variant rounded bg-white text-xs py-2 pe-8 focus:ring-1 focus:ring-primary focus:border-primary" />
<span class="material-symbols-outlined absolute end-2 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant">search</span>
</div>
</div>
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Role') }}</label>
<div class="space-y-2">
@foreach (['admin' => __('System Administrator'), 'agent' => __('Technician'), 'employee' => __('Employee')] as $role => $label)
<label class="flex items-center gap-3 cursor-pointer">
<input type="checkbox" name="role[]" value="{{ $role }}" @checked(in_array($role, (array) request('role', []))) class="rounded text-primary focus:ring-primary" />
<span class="text-xs font-medium">{{ $label }}</span>
</label>
@endforeach
</div>
</div>
<div>
<label class="block text-[11px] font-bold text-on-surface-variant uppercase mb-2">{{ __('Department') }}</label>
<div class="flex flex-wrap gap-2">
@foreach (\App\Models\User::DEPARTMENTS as $department)
<label class="cursor-pointer">
<input type="checkbox" name="department[]" value="{{ $department }}" @checked(in_array($department, (array) request('department', []))) class="peer sr-only" />
<span class="block px-2 py-1 bg-white border border-outline-variant rounded text-[10px] font-bold peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary hover:border-primary transition-all">{{ __(in_array($department, ['it', 'hr'], true) ? strtoupper($department) : ucfirst($department)) }}</span>
</label>
@endforeach
</div>
</div>
</div>
<div class="p-4 border-t border-outline-variant mt-auto">
<button type="submit" class="w-full py-2 bg-primary text-white rounded text-xs font-bold hover:brightness-110 transition">{{ __('APPLY FILTERS') }}</button>
</div>
</form>
</aside>
</div>

<div class="mt-2">{!! $users->links() !!}</div>
@endsection
