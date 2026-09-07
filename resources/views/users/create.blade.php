@extends('layouts.nct')

@section('title', __('New user') . ' — ' . config('app.name'))

@section('content')
<div class="max-w-xl w-full mx-auto">
<div class="mb-8">
<p class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-4">{{ __('Access Management') }}</p>
<h2 class="text-3xl font-headline font-light text-on-surface leading-tight">@if (app()->getLocale() === 'ar')<span class="font-semibold">مستخدم جديد</span>@else New <span class="font-semibold">user</span>@endif</h2>
</div>
<div class="luxury-card p-8">
<form method="POST" action="{{ route('users.store') }}" class="space-y-5">
@csrf
<div>
<label for="name" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">{{ __('Name') }}</label>
<input id="name" name="name" type="text" value="{{ old('name') }}"
       class="w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors"/>
@error('name')<p class="mt-2 text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="email" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">{{ __('Email') }}</label>
<input id="email" name="email" type="email" value="{{ old('email') }}"
       class="w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors"/>
@error('email')<p class="mt-2 text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="role" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">{{ __('Role') }}</label>
<select id="role" name="role"
        class="w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors">
<option value="">{{ __('Select a role…') }}</option>
<option value="admin" @selected(old('role') === 'admin')>{{ __('Admin') }}</option>
<option value="agent" @selected(old('role') === 'agent')>{{ __('Technician') }}</option>
<option value="employee" @selected(old('role') === 'employee')>{{ __('Employee') }}</option>
</select>
@error('role')<p class="mt-2 text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="department" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">{{ __('Department') }}</label>
<select id="department" name="department"
        class="w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors">
<option value="">{{ __('Select a department…') }}</option>
@foreach (\App\Models\User::DEPARTMENTS as $dept)
<option value="{{ $dept }}" @selected(old('department') === $dept)>{{ __(in_array($dept, ['it', 'hr'], true) ? strtoupper($dept) : ucfirst($dept)) }}</option>
@endforeach
</select>
@error('department')<p class="mt-2 text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="password" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">{{ __('Password') }}</label>
<input id="password" name="password" type="password"
       class="w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors"/>
@error('password')<p class="mt-2 text-[12px] text-red-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="password_confirmation" class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">{{ __('Confirm password') }}</label>
<input id="password_confirmation" name="password_confirmation" type="password"
       class="w-full bg-white border border-outline rounded-lg px-4 py-2.5 text-[14px] text-on-surface focus:border-primary focus:ring-0 transition-colors"/>
</div>
<div class="flex items-center justify-between pt-2">
<a href="{{ route('users.index') }}" class="text-[13px] text-on-surface-variant hover:text-on-surface transition-colors">{{ __('Cancel') }}</a>
<button type="submit" class="px-8 py-3 text-[13px] font-medium bg-primary text-white hover:bg-primary/90 transition-all shadow-sm">{{ __('Create user') }}</button>
</div>
</form>
</div>
</div>
@endsection
