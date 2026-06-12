<nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="brand-mark flex h-9 w-9 items-center justify-center rounded-lg text-white transition-all group-hover:shadow-cyan-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="font-display text-slate-900 font-bold text-lg tracking-tight hidden sm:block">ProjectManager</span>
                </a>
            </div>

            <!-- Main Navigation Links -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    Dashboard
                </a>
                <a href="{{ route('dashboard.ai-work-report') }}"
                   class="nav-link {{ request()->routeIs('dashboard.ai-work-report') ? 'active' : '' }} px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    AI Report
                </a>
                <a href="{{ route('projects.index') }}"
                   class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }} px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    Projects
                </a>
                @if(auth()->user()->hasRole(['admin', 'project_manager']))
                <a href="{{ route('activity-logs.index') }}"
                   class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }} px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    Audit Log
                </a>
                @endif
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2">
                <!-- Quick Search -->
                <button
                    type="button"
                    class="quick-search-trigger hidden lg:flex"
                    data-command-open
                    title="Quick actions (Ctrl/Cmd + K)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="text-sm">Search</span>
                    <kbd class="quick-search-kbd hidden xl:inline-flex items-center gap-1">
                        <span>⌘</span>K
                    </kbd>
                </button>

                <!-- Theme Toggle -->
                <div id="theme-toggle-root">
                    <button
                        type="button"
                        class="btn-secondary !px-3"
                        data-theme-toggle
                        title="Toggle theme"
                        aria-label="Toggle theme"
                    >
                        <svg data-theme-icon="moon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                        <svg data-theme-icon="sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36 6.36-1.41-1.41M7.05 7.05 5.64 5.64m12.72 0-1.41 1.41M7.05 16.95l-1.41 1.41"/>
                            <circle cx="12" cy="12" r="4" stroke-width="2"/>
                        </svg>
                    </button>
                </div>

                <!-- Action Buttons -->
                @can('create', App\Models\Task::class)
                <a href="{{ route('tasks.create') }}"
                   class="btn-secondary hidden sm:inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m8 5H4a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v10a2 2 0 01-2 2"/>
                    </svg>
                    <span class="hidden lg:inline">New Task</span>
                </a>
                @endcan

                @can('create', App\Models\Project::class)
                <a href="{{ route('projects.create') }}"
                   class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">New Project</span>
                </a>
                @endcan

                <!-- User Menu -->
                <div class="hidden lg:flex items-center gap-3 pl-2 border-l border-slate-200">
                    <a href="{{ route('profile.show') }}"
                       class="profile-avatar-button {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Profile"
                       aria-label="Open profile">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="icon-action icon-action-danger"
                                title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="icon-action lg:hidden" id="mobile-menu-button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="lg:hidden hidden border-t border-slate-200 py-4" id="mobile-menu">
            <div class="flex flex-col gap-2">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} px-4 py-3 rounded-lg">
                    Dashboard
                </a>
                <a href="{{ route('dashboard.ai-work-report') }}" class="nav-link {{ request()->routeIs('dashboard.ai-work-report') ? 'active' : '' }} px-4 py-3 rounded-lg">
                    AI Report
                </a>
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }} px-4 py-3 rounded-lg">
                    Projects
                </a>
                @if(auth()->user()->hasRole(['admin', 'project_manager']))
                <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }} px-4 py-3 rounded-lg">
                    Audit Log
                </a>
                @endif
                <div class="border-t border-slate-200 my-2"></div>
                @can('create', App\Models\Task::class)
                <a href="{{ route('tasks.create') }}" class="btn-secondary justify-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m8 5H4a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h9a2 2 0 012 2v10a2 2 0 01-2 2"/>
                    </svg>
                    New Task
                </a>
                @endcan
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.show') }}"
                           class="profile-avatar-button {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                           title="Profile"
                           aria-label="Open profile">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </a>
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-secondary text-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    (function () {
        const applyTheme = function (theme) {
            document.documentElement.classList.toggle('dark', theme === 'dark');
            window.localStorage.setItem('pm-theme', theme);

            document.querySelectorAll('[data-theme-icon="moon"]').forEach((icon) => {
                icon.classList.toggle('hidden', theme === 'dark');
            });

            document.querySelectorAll('[data-theme-icon="sun"]').forEach((icon) => {
                icon.classList.toggle('hidden', theme !== 'dark');
            });
        };

        const stored = window.localStorage.getItem('pm-theme');
        const initialTheme = stored === 'dark' || stored === 'light'
            ? stored
            : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        applyTheme(initialTheme);

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.addEventListener('click', function () {
                applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
            });
        });
    })();

    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

<div class="command-palette hidden" data-command-palette aria-hidden="true">
    <div class="command-palette-backdrop" data-command-close></div>
    <div class="command-palette-dialog" role="dialog" aria-modal="true" aria-label="Quick actions">
        <div class="command-palette-head">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                class="command-palette-input"
                data-command-input
                placeholder="Search actions and pages..."
                autocomplete="off"
            />
            <button type="button" class="text-xs text-gray-500 hover:text-gray-700" data-command-close>Esc</button>
        </div>
        <div class="command-palette-list" data-command-list>
            @php
                $commandItems = [
                    ['label' => 'Dashboard', 'url' => route('dashboard'), 'keywords' => 'home overview stats'],
                    ['label' => 'AI Report', 'url' => route('dashboard.ai-work-report'), 'keywords' => 'artificial intelligence work report printable generate copy'],
                    ['label' => 'Projects', 'url' => route('projects.index'), 'keywords' => 'portfolio list all projects'],
                    ['label' => 'Profile', 'url' => route('profile.show'), 'keywords' => 'account user settings details'],
                ];

                if (auth()->user()->hasRole(['admin', 'project_manager'])) {
                    $commandItems[] = ['label' => 'Audit Log', 'url' => route('activity-logs.index'), 'keywords' => 'activity logs security history'];
                }

                if (auth()->user()->can('create', App\Models\Project::class)) {
                    $commandItems[] = ['label' => 'Create Project', 'url' => route('projects.create'), 'keywords' => 'new project add'];
                }

                if (auth()->user()->can('create', App\Models\Task::class)) {
                    $commandItems[] = ['label' => 'Create Task', 'url' => route('tasks.create'), 'keywords' => 'new task add'];
                }
            @endphp

            @foreach($commandItems as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="command-palette-item"
                    data-command-item
                    data-command-text="{{ strtolower($item['label'].' '.$item['keywords']) }}"
                >
                    <span>{{ $item['label'] }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endforeach
        </div>
        <p class="command-palette-empty hidden" data-command-empty>No action found.</p>
    </div>
</div>
