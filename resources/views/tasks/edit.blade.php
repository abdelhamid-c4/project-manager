@extends('layouts.app')
@section('title', 'Edit Task')

@section('content')
<div class="pt-8 max-w-3xl space-y-6">

    <div class="page-hero-panel p-5 lg:p-7">
        <div class="flex items-start gap-4">
            <a href="{{ route('projects.show', $task->project) }}" class="icon-action mt-1" title="Back to project">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="badge bg-cyan-100 text-cyan-800">{{ $task->project->name }}</span>
                <h1 class="mt-3 text-2xl font-bold text-slate-950">Edit Task</h1>
                <p class="mt-2 text-sm text-slate-500">Update status, assignment, due dates, and delivery effort.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="title" class="form-label">Task Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}"
                       class="form-input @error('title') border-red-500 @enderror" required>
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="form-input">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-input">
                        @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="priority" class="form-label">Priority</label>
                    <select name="priority" id="priority" class="form-input">
                        @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $val => $label)
                        <option value="{{ $val }}" {{ old('priority', $task->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                           value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                           class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                    <input type="number" step="0.25" min="0" name="estimated_hours" id="estimated_hours"
                           value="{{ old('estimated_hours', $task->estimated_hours) }}"
                           class="form-input @error('estimated_hours') border-red-500 @enderror">
                    @error('estimated_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="spent_hours" class="form-label">Spent Hours</label>
                    <input type="number" step="0.25" min="0" name="spent_hours" id="spent_hours"
                           value="{{ old('spent_hours', $task->spent_hours) }}"
                           class="form-input @error('spent_hours') border-red-500 @enderror">
                    @error('spent_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            @can('update', $task->project)
            <div>
                <label for="assigned_to" class="form-label">Assign To</label>
                <select name="assigned_to" id="assigned_to" class="form-input">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endcan

            <div class="flex flex-wrap justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('projects.show', $task->project) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Comments ({{ $task->comments->count() }})</h2>
        </div>

        @forelse($task->comments as $comment)
        <div class="surface-row mb-3 p-4">
            <div class="flex items-start gap-3">
                <div class="avatar-mark w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</p>
                        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1 text-sm leading-6 text-gray-600">{{ $comment->content }}</p>
                </div>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">No comments yet.</p>
        @endforelse
    </div>
</div>
@endsection
