<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Help Desk'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex flex-col items-center leading-tight">
                <span class="font-semibold text-slate-900">{{ config('app.name', 'Help Desk') }}</span>
                <span class="text-xs text-slate-500">system</span>
            </a>
            <div class="text-sm text-slate-500">
                @yield('nav')
            </div>
        </div>
    </nav>

    @if (session('status') || session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            @if (session('status'))
                <div class="rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-md bg-rose-50 border border-rose-200 text-rose-800 px-4 py-2 text-sm">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 py-8">
        @yield('content')
    </main>
</body>
</html>
