<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Project Manager') }} - Login</title>
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
    @if (($recaptchaEnabled ?? true) && ($recaptchaConfigured ?? false))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</head>
<body class="min-h-screen font-sans antialiased app-shell">
    <main class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full items-stretch gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <section class="page-hero-panel overflow-hidden">
                <div class="flex h-full flex-col justify-between p-6 sm:p-8 lg:p-10">
                    <div>
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                            <div class="brand-mark flex h-11 w-11 items-center justify-center rounded-lg text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="font-display text-xl font-bold text-slate-950">ProjectManager</span>
                        </a>

                        <div class="mt-10 max-w-xl sm:mt-14">
                            <span class="badge bg-cyan-100 text-cyan-800">Workspace access</span>
                            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                                Sign in to your project command center
                            </h1>
                            <p class="mt-4 max-w-lg text-sm leading-6 text-slate-500 sm:text-base">
                                Review delivery health, coordinate project work, and keep team activity moving from one focused workspace.
                            </p>
                        </div>
                    </div>

                    <div class="mt-12 hidden gap-3 md:grid md:grid-cols-3">
                        <div class="rounded-lg border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase text-slate-500">Portfolio</p>
                            <p class="metric-number mt-2 text-2xl font-semibold text-slate-950">Live</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase text-slate-500">Tasks</p>
                            <p class="metric-number mt-2 text-2xl font-semibold text-blue-700">Synced</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase text-slate-500">Access</p>
                            <p class="metric-number mt-2 text-2xl font-semibold text-emerald-700">Secure</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card flex items-center p-0">
                <div class="w-full p-6 sm:p-8 lg:p-10">
                    <div class="mb-8">
                        <span class="badge bg-slate-100 text-slate-600">Welcome back</span>
                        <h2 class="mt-4 text-2xl font-bold text-slate-950">Log in</h2>
                        <p class="mt-2 text-sm text-slate-500">Use your workspace credentials to continue.</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="form-label">Email address</label>
                            <div class="relative mt-2">
                                <input id="email"
                                       name="email"
                                       type="email"
                                       value="{{ old('email') }}"
                                       required
                                       autocomplete="email"
                                       class="form-input w-full pl-11 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                       placeholder="you@example.com">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                    </svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label for="password" class="form-label">Password</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-cyan-700 hover:text-cyan-900">
                                    Forgot password?
                                </a>
                            </div>
                            <div class="relative mt-2">
                                <input id="password"
                                       name="password"
                                       type="password"
                                       required
                                       autocomplete="current-password"
                                       class="form-input w-full pl-11 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                       placeholder="Enter your password">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 11.25h10.5A2.25 2.25 0 0019.5 19.5v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('password')
                                <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        @if ($recaptchaEnabled ?? true)
                            <div>
                                <label class="form-label">Security verification</label>

                                <div class="mt-2 rounded-lg border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-950/50">
                                    @if ($recaptchaConfigured ?? false)
                                        <div class="flex justify-center sm:justify-start">
                                            <div class="g-recaptcha"
                                                 data-sitekey="{{ $recaptchaSiteKey }}">
                                            </div>
                                        </div>
                                    @elseif ($recaptchaLocalFallback ?? false)
                                        <label class="flex min-h-20 cursor-pointer items-center gap-4 rounded border border-slate-300 bg-white px-5 py-4 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-slate-400 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                                            <input type="checkbox"
                                                   name="local_security_check"
                                                   value="1"
                                                   class="h-8 w-8 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span class="flex-1 text-lg font-medium">I'm not a robot</span>
                                            <span class="text-right text-xs font-semibold text-slate-400">
                                                reCAPTCHA<br>
                                                <span class="font-medium">Local mode</span>
                                            </span>
                                        </label>
                                        <p class="mt-3 text-xs leading-5 text-slate-500">
                                            Local development mode. Add Google reCAPTCHA keys to display the official widget.
                                        </p>
                                    @else
                                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                                            Security verification is not configured. Please contact the administrator.
                                        </div>
                                    @endif
                                </div>

                                @error('g-recaptcha-response')
                                    <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror

                                @error('local_security_check')
                                    <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endif

                        <div class="flex items-center justify-between gap-4">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                Remember me
                            </label>
                        </div>

                        <div class="custom-button w-full">
                            <div class="custom-button-blob"></div>
                            <button type="submit" class="custom-button-inner w-full">
                                Log in
                            </button>
                        </div>
                    </form>

                    @if (Route::has('register'))
                        <p class="mt-6 text-center text-sm text-slate-500">
                            No account yet?
                            <a href="{{ route('register') }}" class="font-semibold text-cyan-700 hover:text-cyan-900">Create one</a>
                        </p>
                    @endif
                </div>
            </section>
        </div>
    </main>
</body>
</html>
