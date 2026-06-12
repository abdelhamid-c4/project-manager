@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="pt-8 space-y-6">

    <!-- Header -->
    <div class="page-hero-panel overflow-hidden">
        <div class="flex flex-col gap-5 p-5 lg:flex-row lg:items-center lg:justify-between lg:p-7">
            <div class="flex items-start gap-4">
                <a href="{{ route('projects.index') }}" class="icon-action mt-1" title="Back to projects">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <span class="badge bg-cyan-100 text-cyan-800">Project workspace</span>
                    <h1 class="mt-3 text-2xl font-bold text-slate-950 lg:text-3xl">{{ $project->name }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <span class="badge {{ $project->statusBadgeClass() }}">{{ str_replace('_', ' ', $project->status) }}</span>
                        <span class="badge {{ $project->priorityBadgeClass() }}">{{ ucfirst($project->priority) }}</span>
                        <span>Owner: {{ $project->owner->name }}</span>
                        <span>{{ $project->members->count() }} member(s)</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
            @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="btn-secondary" title="Create new project">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="hidden sm:inline">New Project</span>
            </a>
            @endcan
            @can('update', $project)
            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Task
            </a>
            @endcan
            @can('update', $project)
            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary">Edit</a>
            @endcan
            @can('delete', $project)
            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                  onsubmit="return confirm('Delete this project and all its tasks?')">
                @csrf @method('DELETE')
                <button class="btn-danger">Delete</button>
            </form>
            @endcan
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($project->description)
    <div class="card">
        <p class="text-gray-700 text-sm leading-relaxed">{{ $project->description }}</p>
    </div>
    @endif

    <!-- Milestones -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Milestones ({{ $project->milestones->count() }})</h2>
            @can('update', $project)
            <button onclick="document.getElementById('milestone-form').classList.toggle('hidden')" class="btn-secondary text-sm">Add Milestone</button>
            @endcan
        </div>

        @can('update', $project)
        <form id="milestone-form" method="POST" action="{{ route('milestones.store', $project) }}" class="surface-row hidden mb-4 p-4">
            @csrf
            <div class="space-y-3">
                <input type="text" name="name" placeholder="Milestone name" class="form-input" required>
                <textarea name="description" placeholder="Description (optional)" rows="2" class="form-input"></textarea>
                <input type="date" name="due_date" class="form-input">
                <button type="submit" class="btn-primary text-sm">Create Milestone</button>
            </div>
        </form>
        @endcan

        @forelse($project->milestones as $milestone)
        <div class="surface-row mb-2 flex items-center justify-between p-3">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full {{ $milestone->status === 'completed' ? 'bg-green-500' : ($milestone->isOverdue() ? 'bg-red-500' : 'bg-blue-500') }}"></div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $milestone->name }}</p>
                    @if($milestone->due_date)
                    <p class="text-xs text-gray-500">{{ $milestone->due_date->format('M d, Y') }} {{ $milestone->isOverdue() ? '(Overdue)' : '' }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge {{ $milestone->statusBadgeClass() }} text-xs">{{ str_replace('_', ' ', $milestone->status) }}</span>
                @can('update', $project)
                <form method="POST" action="{{ route('milestones.destroy', $milestone) }}" onsubmit="return confirm('Delete this milestone?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="icon-action icon-action-danger !h-8 !w-8" title="Delete milestone">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </form>
                @endcan
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">No milestones yet.</p>
        @endforelse
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Tasks (2/3 width) -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-base font-semibold text-gray-900">Tasks ({{ $project->tasks->count() }})</h2>

            @forelse($project->tasks as $task)
            <div class="card hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                            <span class="badge {{ $task->statusBadgeClass() }}">{{ str_replace('_', ' ', $task->status) }}</span>
                            <span class="badge {{ $task->priorityBadgeClass() }}">{{ $task->priority }}</span>
                            @if($task->isOverdue())
                            <span class="badge bg-red-100 text-red-700">Overdue</span>
                            @endif
                        </div>
                        @if($task->description)
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $task->description }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            @if($task->assignee)
                            <span>Assigned to: <span class="text-gray-600">{{ $task->assignee->name }}</span></span>
                            @else
                            <span class="text-gray-400 italic">Unassigned</span>
                            @endif
                            @if($task->due_date)
                            <span>Due: {{ $task->due_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        @can('update', $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary text-xs py-1.5 px-3">Edit</a>
                        @endcan
                        @can('delete', $task)
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                              onsubmit="return confirm('Delete this task?')">
                            @csrf @method('DELETE')
                            <button class="btn-danger text-xs py-1.5 px-3">Delete</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="card text-center py-10 text-gray-400">
                <p class="text-sm">No tasks yet.</p>
                @can('update', $project)
                <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}" class="btn-primary mt-3 inline-flex">Add first task</a>
                @endcan
            </div>
            @endforelse
        </div>

        <!-- Members sidebar -->
        <div class="space-y-4">
            <h2 class="text-base font-semibold text-gray-900">Members</h2>
            <div class="card space-y-3">
                <!-- Owner -->
                <div class="flex items-center gap-3">
                    <div class="avatar-mark w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr($project->owner->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $project->owner->name }}</p>
                        <p class="text-xs text-blue-600">Owner</p>
                    </div>
                </div>
                @foreach($project->members as $member)
                @if($member->id !== $project->owner_id)
                <div class="flex items-center gap-3">
                    <div class="avatar-mark w-8 h-8 rounded-full flex items-center justify-center shrink-0 opacity-80">
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ $member->getRoleNames()->first() }}</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Project Info -->
            <div class="card text-sm space-y-2 text-gray-600">
                <p><span class="font-medium text-gray-800">Created:</span> {{ $project->created_at->format('M d, Y') }}</p>
                <p><span class="font-medium text-gray-800">Updated:</span> {{ $project->updated_at->format('M d, Y') }}</p>
                <p><span class="font-medium text-gray-800">Target due date:</span> {{ $project->due_date?->format('M d, Y') ?? 'Not set' }}</p>
                <p><span class="font-medium text-gray-800">Completion:</span> {{ $project->completionPercentage() }}%</p>
                <p><span class="font-medium text-gray-800">Tasks:</span>
                    {{ $project->tasks->where('status', 'done')->count() }} /
                    {{ $project->tasks->count() }} done
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
