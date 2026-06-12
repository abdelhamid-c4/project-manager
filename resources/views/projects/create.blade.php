@extends('layouts.app')
@section('title', 'Create Project')

@section('content')
<div class="project-create-shell pt-8 space-y-8">
    <!-- Header Section -->
    <div class="page-hero-panel">
        <div class="px-5 py-6 lg:px-8 lg:py-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <a href="{{ route('projects.index') }}" 
                       class="icon-action mt-1"
                       title="Back to projects">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Project
                            </span>
                        </div>
                        <h1 class="mt-2 text-2xl lg:text-3xl font-bold text-slate-900 tracking-tight">Create a new project</h1>
                        <p class="mt-2 max-w-2xl text-sm lg:text-base text-slate-500 leading-relaxed">
                            Define your project's vision, assemble your team, and set the initial stage for successful delivery.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('projects.index') }}" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        View Portfolio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Section -->
    <div class="py-2 lg:py-4">
        <form method="POST" action="{{ route('projects.store') }}" class="grid grid-cols-1 gap-8 lg:gap-10 xl:grid-cols-[1fr_380px]">
            @csrf

            <!-- Left Column - Form Fields -->
            <section class="space-y-8">
                <!-- Project Identity Card -->
                <div class="card bg-white border border-slate-200 shadow-lg shadow-slate-200/50 overflow-hidden">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Project Identity</h2>
                                <p class="text-sm text-slate-500">Give your project a clear name and description</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-sm font-semibold text-slate-700">Owner</label>
                                <input type="text"
                                       value="{{ auth()->user()->name }}"
                                       class="form-input bg-slate-50 text-slate-600"
                                       readonly>
                                <p class="mt-1 text-xs text-slate-500">The creator is automatically assigned as project owner.</p>
                            </div>
                        </div>

                        <div>
                            <label for="name" class="form-label text-sm font-semibold text-slate-700">
                                Project name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative mt-2">
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror pl-11 py-3 text-base"
                                       placeholder="e.g., Q4 Marketing Campaign" 
                                       required>
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                            <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="form-label text-sm font-semibold text-slate-700">
                                Project brief
                            </label>
                            <div class="relative mt-2">
                                <textarea name="description" 
                                          id="description" 
                                          rows="5"
                                          class="form-input @error('description') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror py-3 text-base resize-none"
                                          placeholder="Describe the project's objectives, scope, deliverables, timeline, and success criteria...">{{ old('description') }}</textarea>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="badge bg-slate-100 text-slate-600">Objectives</span>
                                <span class="badge bg-slate-100 text-slate-600">Scope</span>
                                <span class="badge bg-slate-100 text-slate-600">Deliverables</span>
                                <span class="badge bg-slate-100 text-slate-600">Constraints</span>
                            </div>
                            @error('description')
                            <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Planning and Ownership Card -->
                <div class="card bg-white border border-slate-200 shadow-lg shadow-slate-200/50 overflow-hidden">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Planning & Team</h2>
                                <p class="text-sm text-slate-500">Set the project status and assign team members</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Status Selection -->
                        <div>
                            <label class="form-label text-sm font-semibold text-slate-700 mb-4 block">
                                Initial status
                            </label>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <label class="group relative cursor-pointer rounded-xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-blue-300 hover:shadow-md peer-checked:border-blue-500 peer-checked:shadow-lg peer-checked:shadow-blue-500/10">
                                    <input type="radio" name="status" value="pending" class="sr-only peer" required
                                           {{ old('status', 'pending') === 'pending' ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600 transition-colors group-hover:bg-amber-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold text-slate-900">Pending</span>
                                            <span class="block text-xs text-slate-500">Planning phase</span>
                                        </div>
                                    </div>
                                    <div class="pointer-events-none absolute inset-0 rounded-xl ring-2 ring-transparent transition-all peer-checked:ring-blue-500 peer-checked:ring-offset-2"></div>
                                </label>

                                <label class="group relative cursor-pointer rounded-xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-blue-300 hover:shadow-md peer-checked:border-blue-500 peer-checked:shadow-lg peer-checked:shadow-blue-500/10">
                                    <input type="radio" name="status" value="in_progress" class="sr-only peer"
                                           {{ old('status') === 'in_progress' ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold text-slate-900">In Progress</span>
                                            <span class="block text-xs text-slate-500">Active delivery</span>
                                        </div>
                                    </div>
                                    <div class="pointer-events-none absolute inset-0 rounded-xl ring-2 ring-transparent transition-all peer-checked:ring-blue-500 peer-checked:ring-offset-2"></div>
                                </label>

                                <label class="group relative cursor-pointer rounded-xl border-2 border-slate-200 bg-white p-4 transition-all hover:border-blue-300 hover:shadow-md peer-checked:border-blue-500 peer-checked:shadow-lg peer-checked:shadow-blue-500/10">
                                    <input type="radio" name="status" value="completed" class="sr-only peer"
                                           {{ old('status') === 'completed' ? 'checked' : '' }}>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold text-slate-900">Completed</span>
                                            <span class="block text-xs text-slate-500">Finished work</span>
                                        </div>
                                    </div>
                                    <div class="pointer-events-none absolute inset-0 rounded-xl ring-2 ring-transparent transition-all peer-checked:ring-blue-500 peer-checked:ring-offset-2"></div>
                                </label>
                            </div>
                            @error('status')
                            <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="priority" class="form-label text-sm font-semibold text-slate-700">Priority</label>
                                <select name="priority" id="priority" class="form-input @error('priority') border-red-500 @enderror">
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="due_date" class="form-label text-sm font-semibold text-slate-700">Target due date</label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                       class="form-input @error('due_date') border-red-500 @enderror">
                                @error('due_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Team Members -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <label class="form-label text-sm font-semibold text-slate-700 mb-0">
                                    Team members
                                </label>
                                <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Owner: {{ auth()->user()->name }}
                                </span>
                            </div>
                            <div class="surface-row grid max-h-80 grid-cols-1 gap-3 overflow-y-auto p-4 sm:grid-cols-2">
                                @forelse($users as $user)
                                @if($user->id !== auth()->id())
                                <label class="group flex cursor-pointer items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white/70 p-3 transition-all hover:border-cyan-300 hover:shadow-sm">
                                    <span class="flex min-w-0 items-center gap-3">
                                        <span class="avatar-mark flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xs font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-medium text-slate-800">{{ $user->name }}</span>
                                            <span class="block text-xs capitalize text-slate-500">{{ $user->getRoleNames()->first() ?? 'user' }}</span>
                                        </span>
                                    </span>
                                    <div class="relative flex items-center">
                                        <input type="checkbox" 
                                               name="members[]" 
                                               value="{{ $user->id }}"
                                               class="peer h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition-colors"
                                               {{ in_array($user->id, old('members', [])) ? 'checked' : '' }}>
                                        <svg class="pointer-events-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </label>
                                @endif
                                @empty
                                <div class="col-span-full flex flex-col items-center justify-center py-8 text-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-sm text-slate-500">No other team members available</p>
                                </div>
                                @endforelse
                            </div>
                            @error('members')
                            <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                            @error('members.*')
                            <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <!-- Right Column - Sidebar -->
            <aside class="space-y-6">
                <!-- Creation Checklist -->
                <div class="card bg-white border border-slate-200 shadow-lg shadow-slate-200/50 overflow-hidden">
                    <div class="border-b border-slate-100 bg-gradient-to-r from-cyan-50 to-white px-5 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <h2 class="text-base font-semibold text-slate-900">Quick Checklist</h2>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs text-slate-600">
                            <p class="font-semibold text-slate-700 mb-1">Project parameters included on this page:</p>
                            <p>Name, description, owner, status, priority, target due date, and team members.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">1</div>
                            <p class="text-sm text-slate-600 leading-relaxed">Choose a descriptive project name</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">2</div>
                            <p class="text-sm text-slate-600 leading-relaxed">Write a clear project brief</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">3</div>
                            <p class="text-sm text-slate-600 leading-relaxed">Select the appropriate status</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">4</div>
                            <p class="text-sm text-slate-600 leading-relaxed">Add team members</p>
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="card action-card overflow-hidden">
                    <div class="p-6 space-y-5">
                        <div class="flex items-center gap-3 text-white">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold">Ready to create?</h2>
                                <p class="text-xs text-cyan-100">Your project will be live instantly</p>
                            </div>
                        </div>

                        <div class="rounded-lg bg-white/10 p-4 text-sm text-cyan-50">
                            <p class="leading-relaxed">After creation, you'll be redirected to the project detail page where you can add tasks, track progress, and manage your team.</p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="custom-button">
                                <div class="custom-button-blob"></div>
                                <button type="submit" class="custom-button-inner w-full flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Create Project
                                </button>
                            </div>
                            <div class="custom-button">
                                <div class="custom-button-blob"></div>
                                <a href="{{ route('projects.index') }}" class="custom-button-inner w-full flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card bg-slate-50 border border-slate-200 p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Need help?</h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                                Contact your project manager or admin for assistance with project setup and team assignments.
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</div>
@endsection
