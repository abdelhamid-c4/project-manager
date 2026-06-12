<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Project Manager') }} - Reset Password</title>
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
                <span class="badge bg-amber-100 text-amber-800">Password reset</span>
                <h1 class="mt-4 text-2xl font-bold text-slate-950">Set a new password</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Choose a strong password with at least 12 characters, mixed case, numbers, and symbols.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="form-label">Email address</label>
                    <input id="email"
                           name="email"
                           type="email"
                           value="{{ old('email', $email) }}"
                           required
                           autocomplete="email"
                           class="form-input mt-2 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">New password</label>
                    <input id="password"
                           name="password"
                           type="password"
                           required
                           autofocus
                           autocomplete="new-password"
                           class="form-input mt-2 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <input id="password_confirmation"
                           name="password_confirmation"
                           type="password"
                           required
                           autocomplete="new-password"
                           class="form-input mt-2 @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="custom-button w-full">
                    <div class="custom-button-blob"></div>
                    <button type="submit" class="custom-button-inner w-full">
                        Reset password
                    </button>
                </div>
            </form>

            <div class="mt-6 border-t border-slate-200 pt-5">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-950">
                    Back to sign in
                </a>
            </div>
        </section>
    </main>
</body>
</html>
