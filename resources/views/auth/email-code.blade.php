<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Project Manager') }} - Email Code</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|space-grotesk:500,600,700&display=swap" rel="stylesheet">
    <script>
        (function () {
            const stored = window.localStorage.getItem('pm-theme');
            const theme = (stored === 'dark' || stored === 'light')
                ? stored
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="min-h-screen font-sans antialiased app-shell">
    <main class="mx-auto flex min-h-screen w-full max-w-3xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <section class="card w-full p-6 sm:p-8 lg:p-10">
            <div class="mb-8">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-3">
                    <div class="brand-mark flex h-11 w-11 items-center justify-center rounded-lg text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="font-display text-xl font-bold text-slate-950">ProjectManager</span>
                </a>
            </div>

            <div class="mb-8">
                <span class="badge bg-cyan-100 text-cyan-800">Email verification</span>
                <h1 class="mt-4 text-2xl font-bold text-slate-950">Enter the code sent to your email</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    We sent a 6-digit code to <span class="font-semibold text-slate-700">{{ $email }}</span>. The code expires in 10 minutes.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.email-code.verify') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email_code" class="form-label">Verification code</label>
                    <input id="email_code"
                           name="email_code"
                           type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           required
                           autofocus
                           autocomplete="one-time-code"
                           class="form-input mt-2 text-center text-2xl font-semibold tracking-widest @error('email_code') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                           placeholder="000000">
                    @error('email_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="custom-button w-full">
                    <div class="custom-button-blob"></div>
                    <button type="submit" class="custom-button-inner w-full">
                        Verify and continue
                    </button>
                </div>
            </form>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-5">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-950">
                    Use another account
                </a>

                <form method="POST" action="{{ route('login.email-code.resend') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">
                        Resend code
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
