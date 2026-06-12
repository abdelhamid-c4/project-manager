@extends('layouts.app')
@section('title', 'Projects')

@section('content')
<div class="pt-8 space-y-6" id="projects-index-page">

    <!-- Header -->
    <div class="page-hero-panel p-5 lg:p-7">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="badge bg-cyan-100 text-cyan-800">Project portfolio</span>
                <h1 class="mt-3 text-2xl font-bold text-slate-950 lg:text-3xl">Projects</h1>
                <p class="mt-2 text-sm text-slate-500">Search, filter, and open project workspaces from a single command surface.</p>
            </div>
        @can('create', App\Models\Project::class)
        <div class="custom-button">
            <div class="custom-button-blob"></div>
            <a href="{{ route('projects.create') }}" class="custom-button-inner inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Project
            </a>
        </div>
        @endcan
        </div>
    </div>

    <form method="GET" action="{{ route('projects.index') }}" class="card">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-input"
                data-project-filter
                placeholder="Search by name or description"
            >
            <select name="status" class="form-input">
                <option value="">All statuses</option>
                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="priority" class="form-input">
                <option value="">All priorities</option>
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                    <option value="{{ $value }}" {{ request('priority') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <div class="custom-button">
                    <div class="custom-button-blob"></div>
                    <button type="submit" class="custom-button-inner w-full justify-center">Filter</button>
                </div>
                <div class="custom-button">
                    <div class="custom-button-blob"></div>
                    <a href="{{ route('projects.index') }}" class="custom-button-inner w-full justify-center">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Project Cards -->
    @forelse($projects as $project)
    <div class="card hover:shadow-md transition-shadow" data-project-item data-project-text="{{ strtolower($project->name . ' ' . ($project->description ?? '') . ' ' . $project->status . ' ' . $project->priority . ' ' . $project->owner->name) }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ route('projects.show', $project) }}"
                       class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors truncate">
                        {{ $project->name }}
                    </a>
                    <span class="badge {{ $project->statusBadgeClass() }} shrink-0">
                        {{ str_replace('_', ' ', $project->status) }}
                    </span>
                    <span class="badge {{ $project->priorityBadgeClass() }} shrink-0">
                        {{ ucfirst($project->priority) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 line-clamp-2">{{ $project->description ?? 'No description provided.' }}</p>

                <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                    <span>Owner: <span class="text-gray-600 font-medium">{{ $project->owner->name }}</span></span>
                    <span>{{ $project->tasks->count() }} task(s)</span>
                    <span>{{ $project->completionPercentage() }}% complete</span>
                    <span>{{ $project->members->count() }} member(s)</span>
                    <span>Due: {{ $project->due_date?->format('M d, Y') ?? 'Not set' }}</span>
                    <span>Updated {{ $project->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 shrink-0">
                <div class="custom-button">
                    <div class="custom-button-blob"></div>
                    <a href="{{ route('projects.show', $project) }}" class="custom-button-inner text-xs py-1.5 px-3">View</a>
                </div>
                @can('update', $project)
                <div class="custom-button">
                    <div class="custom-button-blob"></div>
                    <a href="{{ route('projects.edit', $project) }}" class="custom-button-inner text-xs py-1.5 px-3">Edit</a>
                </div>
                @endcan
                @can('delete', $project)
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete this project? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <div class="custom-button">
                        <div class="custom-button-blob"></div>
                        <button type="submit" class="custom-button-inner text-xs py-1.5 px-3">Delete</button>
                    </div>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="card text-center py-16">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-500 text-sm">No projects yet.</p>
        @can('create', App\Models\Project::class)
        <div class="custom-button">
            <div class="custom-button-blob"></div>
            <a href="{{ route('projects.create') }}" class="custom-button-inner mt-4 inline-flex">Create your first project</a>
        </div>
        @endcan
    </div>
    @endforelse
    @if($projects->count() > 0)
        <div class="hidden card text-center py-8 text-sm text-gray-500" data-projects-empty-state>
            No matching projects for your search.
        </div>
    @endif

    <!-- Pagination -->
    <div class="mt-4">{{ $projects->links() }}</div>
</div>
@endsection
