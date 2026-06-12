@extends('layouts.app')
@section('title', 'Create Task')

@section('content')
<div class="pt-8 max-w-3xl space-y-6">

    <div class="page-hero-panel p-5 lg:p-7">
        <div class="flex items-start gap-4">
            @if($selectedProject)
            <a href="{{ route('projects.show', $selectedProject) }}" class="icon-action mt-1" title="Back to project">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @else
            <a href="{{ route('projects.index') }}" class="icon-action mt-1" title="Back to projects">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @endif
            <div>
                <span class="badge bg-cyan-100 text-cyan-800">New task</span>
                <h1 class="mt-3 text-2xl font-bold text-slate-950">Create Task</h1>
                <p class="mt-2 text-sm text-slate-500">Define the task, assign ownership, and connect it to the right project workflow.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
            @csrf

            <!-- Project -->
            <div>
                <label for="project_id" class="form-label">Project <span class="text-red-500">*</span></label>
                <select name="project_id" id="project_id"
                        class="form-input @error('project_id') border-red-500 @enderror" required>
                    <option value="">Select a project...</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}"
                            {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                    @endforeach
                </select>
                @error('project_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="form-label">Task Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       class="form-input @error('title') border-red-500 @enderror"
                       placeholder="e.g. Design landing page" required>
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="form-input @error('description') border-red-500 @enderror"
                          placeholder="Task details...">{{ old('description') }}</textarea>
            </div>

            <!-- Status / Priority / Due Date -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-input">
                        <option value="todo"        {{ old('status') === 'todo'        ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done"        {{ old('status') === 'done'        ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div>
                    <label for="priority" class="form-label">Priority</label>
                    <select name="priority" id="priority" class="form-input">
                        <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div>
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                           value="{{ old('due_date') }}"
                           class="form-input @error('due_date') border-red-500 @enderror">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                    <input type="number" step="0.25" min="0" name="estimated_hours" id="estimated_hours"
                           value="{{ old('estimated_hours') }}"
                           class="form-input @error('estimated_hours') border-red-500 @enderror">
                    @error('estimated_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="spent_hours" class="form-label">Spent Hours</label>
                    <input type="number" step="0.25" min="0" name="spent_hours" id="spent_hours"
                           value="{{ old('spent_hours', 0) }}"
                           class="form-input @error('spent_hours') border-red-500 @enderror">
                    @error('spent_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Assigned To -->
            <div>
                <label for="assigned_to" class="form-label">Assign To</label>
                <select name="assigned_to" id="assigned_to" class="form-input">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-wrap justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('projects.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Task</button>
            </div>
        </form>
    </div>
</div>
@endsection
