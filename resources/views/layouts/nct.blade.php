@php
    $roleLabel = auth()->user()->jobTitle();
    $isRtl = app()->getLocale() === 'ar';
@endphp
<!DOCTYPE html>
<html class="light" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', config('app.name'))</title>
{{-- Stitch design: NCT Luxury Minimalism (shared shell) --}}
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&amp;family=Work+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "primary": "#004d33",
                    "secondary": "#c5a059",
                    "surface": "var(--surface)",
                    "surface-dim": "var(--surface-dim)",
                    "surface-bright": "var(--surface-bright)",
                    "on-surface": "var(--on-surface)",
                    "on-surface-variant": "var(--on-surface-variant)",
                    "outline": "var(--outline)",
                    "outline-variant": "var(--outline-variant)",
                    "background": "var(--background)",
                    "on-background": "var(--on-background)"
                },
                "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "1rem", "full": "9999px" },
                "spacing": { "margin-mobile": "24px", "margin-desktop": "80px", "container-max": "1440px", "gutter": "32px", "base": "8px" },
                "fontFamily": { "headline": ["Work Sans", "Inter", "sans-serif"], "body": ["Inter", "sans-serif"] }
            }
        }
    }
</script>
<script>
    // Apply the saved theme before first paint so there is no white flash.
    if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
</script>
<style>
    :root {
        --surface: #ffffff; --surface-dim: #fafafa; --surface-bright: #ffffff;
        --on-surface: #1a1a1a; --on-surface-variant: #666666;
        --outline: #e5e5e5; --outline-variant: #f0f0f0;
        --background: #ffffff; --on-background: #1a1a1a;
    }
    html.dark {
        /* Soft "dark dimmed" palette: cards sit clearly above a muted (not pure
           black) background, and text is off-white to cut glare/eye strain. */
        --surface: #212730; --surface-dim: #1a1f27; --surface-bright: #2a313b;
        --on-surface: #e4e7ec; --on-surface-variant: #9aa2ad;
        --outline: #3b434f; --outline-variant: #2e353f;
        --background: #14181e; --on-background: #e4e7ec;
    }
    body { font-family: 'Inter', 'Work Sans', sans-serif; background-color: var(--background); letter-spacing: -0.01em; }
    .luxury-card { background: var(--surface); border: 1px solid var(--outline-variant); transition: all 0.5s cubic-bezier(0.2, 1, 0.3, 1); }
    .luxury-card:hover { border-color: #e0e0e0; box-shadow: 0 10px 30px -15px rgba(0,0,0,0.04); }
    /* Views use bg-white on cards, tables, and inputs everywhere; remap it in
       dark mode instead of touching every template. Images keep their white chip. */
    html.dark .bg-white:not(img) { background-color: var(--surface) !important; }
    html.dark .luxury-card:hover { border-color: #454e5b; box-shadow: 0 10px 30px -15px rgba(0,0,0,0.6); }
    /* The brand green (#004d33) is too dark to read as text/border on a dark
       surface — lift accents to a lighter green so they stay legible. The green
       sidebar and solid green buttons keep white text, so they're untouched. */
    html.dark .text-primary { color: #5cae8b; }
    html.dark .border-primary\/20 { border-color: rgba(92,174,139,0.3); }
    html.dark .bg-primary\/5 { background-color: rgba(92,174,139,0.08); }
    /* Soft wells (surface-dim) need a touch more lift so pills/badges read. */
    html.dark .bg-surface-dim { background-color: var(--surface-dim); }
    /* In-card table row dividers should be a whisper hairline — the default
       outline is tuned for card EDGES (which sit on the darker page bg and
       need contrast); inside a card that same line reads as a bright rule. */
    html.dark table tr { border-color: #262c34; }
    html.dark .divide-outline-variant > * { border-color: #262c34; }
    /* The light-mode progress-bar track (#f5f5f5) is a bright line on dark. */
    html.dark .bar-track-minimal { background: #2a313b; }
    html.dark input, html.dark select, html.dark textarea { color: var(--on-surface); }
    html.dark ::placeholder { color: var(--on-surface-variant); }
    html.dark .chart-donut-minimal::after, html.dark .chart-donut-minimal::before { background-color: var(--surface); border-color: var(--outline-variant); }
    html.dark ::-webkit-scrollbar-track { background: var(--background); }
    html.dark ::-webkit-scrollbar-thumb { background: #3a3a3a; }
    .chart-donut-minimal {
        width: 200px; height: 200px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; position: relative;
    }
    .chart-donut-minimal::after { content: ""; position: absolute; width: 196px; height: 196px; background-color: #ffffff; border-radius: 50%; }
    .chart-donut-minimal::before { content: ""; position: absolute; width: 170px; height: 170px; background-color: #ffffff; border-radius: 50%; z-index: 5; border: 1px solid #f5f5f5; }
    .chart-inner-content { position: relative; z-index: 10; text-align: center; }
    .bar-track-minimal { flex: 1; height: 2px; background: #f5f5f5; overflow: hidden; }
    .bar-fill-minimal { height: 100%; transition: width 1.5s cubic-bezier(0.2, 1, 0.3, 1); }
    .nav-link { position: relative; }
    .side-link { position: relative; }
    .side-link::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 2px; height: 0; background: #ffffff; transition: height 0.3s ease; }
    html[dir="rtl"] .side-link::before { left: auto; right: 0; }
    html[dir="rtl"] body { font-family: 'Inter', 'Segoe UI', 'Tahoma', sans-serif; letter-spacing: 0; }
    .side-link:hover::before, .side-link.is-active::before { height: 60%; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #ffffff; }
    ::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }
</style>
@stack('head')
</head>
<body class="text-on-background min-h-screen flex selection:bg-primary/10">
@php
    $navItems = match (auth()->user()->role) {
        'admin' => [
            [__('Dashboard'), route('admin.dashboard'), request()->routeIs('admin.dashboard'), 'dashboard'],
            [__('Tickets'), route('admin.tickets.index'), request()->routeIs('admin.tickets.*'), 'confirmation_number'],
            [__('Ratings'), route('admin.ratings'), request()->routeIs('admin.ratings'), 'grade'],
            [__('Report'), route('admin.report'), request()->routeIs('admin.report'), 'summarize'],
            [__('Users'), route('users.index'), request()->routeIs('users.*'), 'group'],
        ],
        'agent' => [
            [__('My tickets'), route('tickets.mine'), request()->routeIs('tickets.mine'), 'assignment_ind'],
            [__('Tickets'), route('tickets.index'), request()->routeIs('tickets.index'), 'inbox'],
            [__('My ratings'), route('tickets.ratings'), request()->routeIs('tickets.ratings'), 'grade'],
        ],
        default => [
            [__('My tickets'), route('my-tickets.index'), request()->routeIs('my-tickets.index') || request()->routeIs('my-tickets.show'), 'confirmation_number'],
            [__('New ticket'), route('my-tickets.create'), request()->routeIs('my-tickets.create'), 'add_circle'],
        ],
    };
@endphp
<!-- Left Sidebar Navigation (icon rail; expands on hover) -->
<aside class="group fixed top-0 {{ $isRtl ? 'right-0 border-l' : 'left-0 border-r' }} z-40 h-screen w-20 hover:w-64 shrink-0 bg-primary text-white border-white/10 flex flex-col overflow-hidden transition-[width] duration-300 ease-in-out">
<div class="flex items-center h-20 border-b border-white/10">
<span class="w-20 flex justify-center shrink-0">
<img src="{{ asset('images/nctkap_logo.jpg') }}" alt="NCT" class="h-9 w-9 object-cover shrink-0 rounded-full bg-white p-0.5" />
</span>
<h1 class="hidden group-hover:block text-lg font-headline font-semibold tracking-tight text-white whitespace-nowrap">NCT <span class="font-light text-white/60">Ticket System</span></h1>
</div>
<nav class="flex-grow py-6 flex flex-col gap-1">
@foreach ($navItems as [$label, $url, $active, $icon])
<a class="side-link {{ $active ? 'is-active' : '' }} flex items-center py-3 rounded-lg text-[13px] font-medium transition-colors {{ $active ? 'text-white bg-white/10 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}" href="{{ $url }}" title="{{ $label }}">
<span class="w-20 flex justify-center shrink-0"><span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'wght' 300">{{ $icon }}</span></span>
<span class="hidden group-hover:block whitespace-nowrap pr-4">{{ $label }}</span>
</a>
@endforeach
<button type="button" id="theme-toggle" title="Toggle theme"
        class="side-link flex items-center py-3 rounded-lg text-[13px] font-medium transition-colors text-white/70 hover:text-white hover:bg-white/10">
<span class="w-20 flex justify-center shrink-0"><span class="material-symbols-outlined text-[20px]" id="theme-toggle-icon" style="font-variation-settings: 'wght' 300">dark_mode</span></span>
<span class="hidden group-hover:block whitespace-nowrap pr-4" id="theme-toggle-label">{{ __('Dark mode') }}</span>
</button>
<a href="{{ route('locale.switch', $isRtl ? 'en' : 'ar') }}" title="{{ $isRtl ? 'English' : 'عربي' }}"
   class="side-link flex items-center py-3 rounded-lg text-[13px] font-medium transition-colors text-white/70 hover:text-white hover:bg-white/10">
<span class="w-20 flex justify-center shrink-0"><span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'wght' 300">language</span></span>
<span class="hidden group-hover:block whitespace-nowrap pr-4">{{ $isRtl ? 'English' : 'عربي' }}</span>
</a>
<script>
    (function () {
        const icon = document.getElementById('theme-toggle-icon');
        const label = document.getElementById('theme-toggle-label');

        function paint() {
            const dark = document.documentElement.classList.contains('dark');
            icon.textContent = dark ? 'light_mode' : 'dark_mode';
            label.textContent = dark ? @json(__('Light mode')) : @json(__('Dark mode'));
        }

        document.getElementById('theme-toggle').addEventListener('click', function () {
            const dark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', dark ? 'dark' : 'light');
            paint();
        });

        paint();
    })();
</script>
</nav>
<div class="border-t border-white/10 py-4 flex items-center">
<div class="w-20 flex justify-center shrink-0">
<div class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center overflow-hidden bg-white/10">
<span class="material-symbols-outlined text-white/80 text-xl" style="font-variation-settings: 'wght' 300">person</span>
</div>
</div>
<div class="text-left min-w-0 flex-grow whitespace-nowrap hidden group-hover:block">
<p class="text-[13px] font-semibold text-white truncate">{{ auth()->user()->name }}</p>
<p class="text-[10px] text-white/60 tracking-wider uppercase">{{ $roleLabel }}</p>
</div>
<form method="POST" action="{{ route('logout') }}" class="shrink-0 pr-4 hidden group-hover:block">
@csrf
<button type="submit" class="text-white/70 hover:text-white transition-colors flex items-center" title="{{ __('Sign out') }}">
<span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'wght' 300">logout</span>
</button>
</form>
</div>
</aside>
<!-- Content column -->
<div class="flex-grow min-w-0 flex flex-col {{ $isRtl ? 'mr-20' : 'ml-20' }}">
<!-- Main Content -->
<main class="flex-grow w-full max-w-screen-2xl mx-auto px-10 py-10 flex flex-col gap-8">
@if (session('status'))
<div class="border border-primary/20 bg-primary/5 text-primary px-6 py-3 text-[13px] font-medium">{{ session('status') }}</div>
@endif
@if (session('error'))
<div class="border border-red-200 bg-red-50 text-red-600 px-6 py-3 text-[13px] font-medium">{{ session('error') }}</div>
@endif
@yield('content')
</main>
<!-- Footer -->
<footer class="mt-12 py-8 px-10 border-t border-outline-variant bg-white">
<div class="max-w-screen-2xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
<div class="flex items-center gap-4 opacity-60">
<span class="material-symbols-outlined text-on-surface text-xl" style="font-variation-settings: 'wght' 200">sailing</span>
<div>
<p class="text-[11px] text-on-surface font-semibold uppercase tracking-widest">{{ __('National Container Terminals') }}</p>
<p class="text-[10px] text-on-surface-variant mt-1">© {{ date('Y') }} {{ __('Terminal Operations System') }}</p>
</div>
</div>
<div class="flex items-center gap-10">
<div class="flex gap-8">
<a class="text-[11px] font-bold text-on-surface-variant hover:text-primary transition-colors uppercase tracking-widest" href="#">{{ __('Privacy') }}</a>
<a class="text-[11px] font-bold text-on-surface-variant hover:text-primary transition-colors uppercase tracking-widest" href="#">{{ __('Terms') }}</a>
<a class="text-[11px] font-bold text-on-surface-variant hover:text-primary transition-colors uppercase tracking-widest" href="#">{{ __('Support') }}</a>
</div>
</div>
</div>
</footer>
</div>
</body>
</html>
