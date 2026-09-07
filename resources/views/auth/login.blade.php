<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>تسجيل الدخول - محطات الحاويات الوطنية</title>
{{-- Stitch design: NCT login (Maritime Precision) --}}
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Noto+Serif:wght@400;600;700&amp;family=Public+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "on-secondary": "#ffffff",
                    "surface-container-highest": "#e3e2e3",
                    "on-primary-fixed-variant": "#00419d",
                    "secondary-fixed-dim": "#c2c7cc",
                    "secondary": "#5a5f63",
                    "tertiary-fixed": "#f9e37a",
                    "secondary-fixed": "#dfe3e8",
                    "surface-bright": "#faf9fa",
                    "on-tertiary": "#ffffff",
                    "primary-fixed-dim": "#b1c5ff",
                    "on-primary": "#ffffff",
                    "on-tertiary-fixed-variant": "#524600",
                    "on-tertiary-fixed": "#211b00",
                    "tertiary-container": "#bfab49",
                    "error": "#ba1a1a",
                    "tertiary": "#6d5e00",
                    "secondary-container": "#dfe3e8",
                    "surface-container-lowest": "#ffffff",
                    "primary": "#094cb2",
                    "on-surface-variant": "#434653",
                    "on-surface": "#1b1c1d",
                    "on-error": "#ffffff",
                    "on-primary-fixed": "#001946",
                    "surface-dim": "#dbdadb",
                    "on-secondary-container": "#606569",
                    "surface-container-low": "#f5f3f4",
                    "outline-variant": "#c3c6d5",
                    "surface": "#faf9fa",
                    "on-background": "#1b1c1d",
                    "on-secondary-fixed-variant": "#42474b",
                    "primary-fixed": "#d9e2ff",
                    "on-primary-container": "#e7ebff",
                    "surface-tint": "#2259bf",
                    "primary-container": "#3366cc",
                    "surface-container": "#efedee",
                    "tertiary-fixed-dim": "#dcc661",
                    "outline": "#737784",
                    "error-container": "#ffdad6",
                    "on-error-container": "#93000a",
                    "surface-variant": "#e3e2e3",
                    "on-secondary-fixed": "#171c20",
                    "inverse-on-surface": "#f2f0f1",
                    "background": "#faf9fa",
                    "on-tertiary-container": "#4a3f00",
                    "inverse-primary": "#b1c5ff",
                    "surface-container-high": "#e9e8e9",
                    "inverse-surface": "#303031"
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
<body class="bg-background text-on-background font-body min-h-screen flex flex-col antialiased selection:bg-primary-fixed selection:text-on-primary-fixed relative">
<!-- Background Image with Overlay -->
<div class="fixed inset-0 z-[-1] before:absolute before:inset-0 before:bg-surface/80 before:backdrop-blur-md">
<div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD1z4ekp1wfIAPlDCrzIkGK0Z-uIJr10XkQL8tdRJqdr5_GCsLAHDvQWUAwXgOkq1qWkyd37uWK8GLQ2J98P71-ZiywoHyMJxBw1B51yIijb3w7BTQWVAc-PAzjFgJFKQZj2tCuZ2GLtfKj2QLxG1D-cDf4oqPEHeFeQsikif66reB56EKy2qZ3wgzYNTiFODTgZXhhhNXvynJZ_m-kod07B22QGnNzK0ZAKVFjeyIEwlf9gT3grYBfEYJ88GIBHq1smXPUqsVqS-V1')"></div>
</div>
<!-- TopNavBar -->
<header class="w-full top-0 sticky bg-surface-container-low backdrop-blur-md transition-all duration-200 ease-in-out z-50">
<div class="flex flex-row-reverse justify-between items-center px-8 py-4 w-full">
<img src="{{ asset('images/nctkap_logo.jpg') }}" alt="National Container Terminals" class="h-16 w-auto mix-blend-multiply"/>
</div>
</header>
<!-- Main Content Canvas -->
<main class="flex-grow flex items-center justify-center py-16 px-4 md:px-8 relative z-10">
<!-- Login Card - Glassmorphism -->
<div dir="ltr" class="w-full max-w-sm bg-surface-container-lowest/90 backdrop-blur-xl rounded-xl p-8 flex flex-col gap-3 shadow-2xl shadow-primary/5 border border-outline-variant/15">
<div class="text-center space-y-2">
<h1 class="font-body text-2xl font-bold text-on-surface leading-tight tracking-tight">
                    {{ __('Login') }}
                </h1>
<p class="font-body text-on-surface-variant text-sm">
                    {{ __('Welcome to the National Container Terminals portal') }}
                </p>
</div>
<form action="{{ route('login') }}" class="space-y-4 flex flex-col" method="POST">
@csrf
<!-- Input Group: Email -->
<div class="space-y-2">
<label class="block font-label text-sm text-on-surface font-medium tracking-wide" for="email">
                        {{ __('Username or Email') }}
                    </label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/50 pointer-events-none" style="font-variation-settings: 'FILL' 0;">person</span>
<input class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg py-3 px-12 text-on-surface font-body focus:ring-0 focus:border-primary transition-colors duration-200 placeholder:text-on-surface-variant/40" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter your email') }}" required autofocus type="email">
</div>
@error('email')
<p class="font-label text-sm text-error">{{ $message }}</p>
@enderror
</div>
<!-- Input Group: Password -->
<div class="space-y-2">
<label class="block font-label text-sm text-on-surface font-medium tracking-wide" for="password">
                            {{ __('Password') }}
                        </label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/50 pointer-events-none" style="font-variation-settings: 'FILL' 0;">lock</span>
<input class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg py-3 px-12 text-on-surface font-body focus:ring-0 focus:border-primary transition-colors duration-200" id="password" name="password" placeholder="••••••••" required type="password">
</div>
@error('password')
<p class="font-label text-sm text-error">{{ $message }}</p>
@enderror
<div class="text-right">
<a class="font-label text-sm hover:underline decoration-primary underline-offset-4 transition-all" href="{{ route('password.request') }}" style="color: rgb(197, 160, 89);">
                            {{ __('Forgot password?') }}
                        </a>
</div>
</div>
<!-- Primary Action -->
<button class="mt-2 w-full text-on-primary font-label font-semibold text-base py-3 rounded-lg hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all duration-300 ease-out flex items-center justify-center gap-2" type="submit" style="background-color: rgb(0, 77, 51); color: rgb(255, 255, 255);">
<span class="">{{ __('Login') }}</span>
<span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 0;">login</span>
</button>
</form>
</div>
</main>
</body>
</html>
