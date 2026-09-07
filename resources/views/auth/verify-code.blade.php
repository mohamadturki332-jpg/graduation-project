<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>{{ __('Verification code') }} - محطات الحاويات الوطنية</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Noto+Serif:wght@400;600;700&amp;family=Public+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#f5f3f4",
                    "on-surface-variant": "#434653",
                    "on-surface": "#1b1c1d",
                    "outline-variant": "#c3c6d5",
                    "surface": "#faf9fa",
                    "background": "#faf9fa",
                    "on-background": "#1b1c1d",
                    "error": "#ba1a1a",
                    "primary": "#094cb2"
                },
                "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
                },
                "fontFamily": {
                    "headline": ["Noto Serif"],
                    "display": ["Noto Serif"],
                    "body": ["Inter"],
                    "label": ["Public Sans"]
                }
            }
        }
    }
</script>
</head>
<body class="bg-background text-on-background font-body min-h-screen flex flex-col antialiased relative">
<div class="fixed inset-0 z-[-1] before:absolute before:inset-0 before:bg-surface/80 before:backdrop-blur-md">
<div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD1z4ekp1wfIAPlDCrzIkGK0Z-uIJr10XkQL8tdRJqdr5_GCsLAHDvQWUAwXgOkq1qWkyd37uWK8GLQ2J98P71-ZiywoHyMJxBw1B51yIijb3w7BTQWVAc-PAzjFgJFKQZj2tCuZ2GLtfKj2QLxG1D-cDf4oqPEHeFeQsikif66reB56EKy2qZ3wgzYNTiFODTgZXhhhNXvynJZ_m-kod07B22QGnNzK0ZAKVFjeyIEwlf9gT3grYBfEYJ88GIBHq1smXPUqsVqS-V1')"></div>
</div>
<header class="w-full top-0 sticky bg-surface-container-low backdrop-blur-md z-50">
<div class="flex flex-row-reverse justify-between items-center px-8 py-4 w-full">
<img src="{{ asset('images/nctkap_logo.jpg') }}" alt="National Container Terminals" class="h-16 w-auto mix-blend-multiply"/>
</div>
</header>
<main class="flex-grow flex items-center justify-center py-16 px-4 md:px-8 relative z-10">
<div dir="ltr" class="w-full max-w-sm bg-surface-container-lowest/90 backdrop-blur-xl rounded-xl p-8 flex flex-col gap-4 shadow-2xl border border-outline-variant/15">
<div class="text-center space-y-2">
<span class="material-symbols-outlined text-5xl" style="color: rgb(0, 77, 51); font-variation-settings: 'FILL' 0;">mark_email_read</span>
<h1 class="font-body text-2xl font-bold text-on-surface leading-tight tracking-tight">{{ __('Verification code') }}</h1>
<p class="font-body text-on-surface-variant text-sm">{{ __('We emailed a 6-digit code to :email. Enter it below to continue.', ['email' => $maskedEmail]) }}</p>
</div>

@if (session('status'))
<p class="text-center text-sm font-label rounded-lg py-2 px-3" style="background-color:#f0f7f4; color: rgb(0, 77, 51);">{{ session('status') }}</p>
@endif
@if (session('error'))
<p class="text-center text-sm font-label text-error bg-error/5 rounded-lg py-2 px-3">{{ session('error') }}</p>
@endif

<form action="{{ route('login.verify.submit') }}" method="POST" class="space-y-4 flex flex-col">
@csrf
<div class="space-y-2">
<input
    class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg py-3 text-on-surface font-body text-center text-2xl tracking-[0.5em] focus:ring-0 focus:border-primary transition-colors duration-200"
    id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6"
    placeholder="••••••" required autofocus type="text">
@error('code')
<p class="font-label text-sm text-error text-center">{{ $message }}</p>
@enderror
</div>
<button class="w-full font-label font-semibold text-base py-3 rounded-lg hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-out flex items-center justify-center gap-2" type="submit" style="background-color: rgb(0, 77, 51); color: rgb(255, 255, 255);">
<span>{{ __('Verify') }}</span>
<span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 0;">verified_user</span>
</button>
</form>

<div class="flex items-center justify-between text-sm font-label">
<form action="{{ route('login.verify.resend') }}" method="POST">
@csrf
<button type="submit" class="hover:underline underline-offset-4" style="color: rgb(197, 160, 89);">{{ __('Resend code') }}</button>
</form>
<a href="{{ route('login') }}" class="text-on-surface-variant hover:underline underline-offset-4">{{ __('Back to login') }}</a>
</div>
</div>
</main>
</body>
</html>
