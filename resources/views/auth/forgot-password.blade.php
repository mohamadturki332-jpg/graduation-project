<!DOCTYPE html>
<html class="light" dir="ltr" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Forgot password — {{ config('app.name') }}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&amp;family=Work+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        theme: { extend: {
            colors: { primary: "#004d33", secondary: "#c5a059", "on-surface": "#1a1a1a", "on-surface-variant": "#666666", outline: "#e5e5e5", "outline-variant": "#f0f0f0" },
            borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "1rem" },
            fontFamily: { headline: ["Work Sans", "Inter", "sans-serif"], body: ["Inter", "sans-serif"] }
        } }
    }
</script>
<style>body { font-family: 'Inter', sans-serif; background-color: #ffffff; letter-spacing: -0.01em; }</style>
</head>
<body class="min-h-screen flex flex-col">
<header class="border-b border-outline-variant">
<div class="max-w-screen-2xl mx-auto px-10 py-4 flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'wght' 300">anchor</span>
<div class="h-6 w-px bg-outline"></div>
<h1 class="text-lg font-headline font-semibold tracking-tight text-on-surface">NCT <span class="font-light text-on-surface-variant">Helpdesk</span></h1>
</div>
</header>
<main class="flex-grow flex items-center justify-center px-4 py-16">
<div class="w-full max-w-md bg-white border border-outline-variant rounded-xl p-10 flex flex-col gap-6 shadow-[0_10px_30px_-15px_rgba(0,0,0,0.06)]">
<div>
<p class="text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-3">Account access</p>
<h2 class="text-2xl font-headline font-light text-on-surface leading-tight">Forgot your <span class="font-semibold">password?</span></h2>
<p class="text-on-surface-variant mt-3 text-[14px] font-light leading-relaxed">
    Accounts are managed by your administrator. Please contact them to have your password reset.
</p>
</div>
<div class="rounded-lg border border-outline-variant bg-[#fafafa] px-5 py-4">
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Administrator</p>
<p class="text-[14px] text-on-surface">admin@nctkap.com</p>
</div>
<a href="{{ route('login') }}" class="w-full text-center bg-primary text-white text-[14px] font-medium py-3 rounded-lg hover:bg-primary/90 transition-all shadow-sm">Back to sign in</a>
</div>
</main>
</body>
</html>
