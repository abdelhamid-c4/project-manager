<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Project Manager') }} - Register</title>
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
    <main class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full items-stretch gap-6 lg:grid-cols-[0.9fr_1.1fr]">
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
                            <span class="badge bg-cyan-100 text-cyan-800">Team workspace</span>
                            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                                Start with a focused project workspace
                            </h1>
                            <p class="mt-4 max-w-lg text-sm leading-6 text-slate-500 sm:text-base">
                                Create your account, join your service lane, and keep project delivery visible from the first task.
                            </p>
                        </div>
                    </div>

                    <div class="mt-12 hidden gap-3 md:grid md:grid-cols-3">
                        <div class="rounded-lg border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase text-slate-500">Setup</p>
                            <p class="metric-number mt-2 text-2xl font-semibold text-slate-950">Fast</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase text-slate-500">Role</p>
                            <p class="metric-number mt-2 text-2xl font-semibold text-blue-700">Member</p>
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
                        <span class="badge bg-slate-100 text-slate-600">Create account</span>
                        <h2 class="mt-4 text-2xl font-bold text-slate-950">Register</h2>
                        <p class="mt-2 text-sm text-slate-500">Tell us who you are and which service team you belong to.</p>
                    </div>

                    <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                        @csrf

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="form-label">Name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                                       class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="service" class="form-label">Service</label>
                                <select id="service" name="service" required
                                        class="form-input @error('service') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                    <option value="">Select a service</option>
                                    <option value="development" {{ old('service') === 'development' ? 'selected' : '' }}>Development</option>
                                    <option value="design" {{ old('service') === 'design' ? 'selected' : '' }}>Design</option>
                                    <option value="marketing" {{ old('service') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="support" {{ old('service') === 'support' ? 'selected' : '' }}>Support</option>
                                    <option value="sales" {{ old('service') === 'sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="other" {{ old('service') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('service')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                   class="form-input @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                   placeholder="you@example.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                       class="form-input @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="form-label">Confirm password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                       class="form-input">
                            </div>
                        </div>

                        <div class="custom-button w-full">
                            <div class="custom-button-blob"></div>
                            <button type="submit" class="custom-button-inner w-full">
                                Create account
                            </button>
                        </div>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="auth-shell-link">Log in</a>
                    </p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
