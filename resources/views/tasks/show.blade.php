@extends('layouts.app')
@section('title', $task->title)

@section('content')
<div class="pt-8 space-y-6">
    <div class="page-hero-panel overflow-hidden">
        <div class="flex flex-col gap-5 p-5 lg:flex-row lg:items-center lg:justify-between lg:p-7">
            <div class="flex items-start gap-4">
                <a href="{{ route('projects.show', $task->project) }}" class="icon-action mt-1" title="Back to project">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <span class="badge bg-cyan-100 text-cyan-800">{{ $task->project->name }}</span>
                    <h1 class="mt-3 text-2xl font-bold text-slate-950 lg:text-3xl">{{ $task->title }}</h1>
                    <p class="mt-2 text-sm text-slate-500">Track task status, subtasks, comments, and supporting files from one workspace.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="btn-primary">Edit Task</a>
                @endcan
                <a href="{{ route('projects.show', $task->project) }}" class="btn-secondary">Project</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="lg:col-span-2 space-y-6">
            <div class="card space-y-5">
                <div class="flex flex-wrap gap-2">
                    <span class="badge {{ $task->statusBadgeClass() }}">{{ str_replace('_', ' ', $task->status) }}</span>
                    <span class="badge {{ $task->priorityBadgeClass() }}">{{ $task->priority }}</span>
                    @if($task->isOverdue())
                    <span class="badge bg-red-100 text-red-700">Overdue</span>
                    @endif
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Description</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ $task->description ?: 'No description provided.' }}</p>
                </div>
            </div>

            <!-- Subtasks -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Subtasks ({{ $task->subtasks->count() }})</h2>
                    @can('update', $task)
                    <button onclick="document.getElementById('subtask-form').classList.toggle('hidden')" class="btn-secondary text-sm">Add Subtask</button>
                    @endcan
                </div>

                @can('update', $task)
                <form id="subtask-form" method="POST" action="{{ route('subtasks.store', $task) }}" class="surface-row hidden mb-4 p-4">
                    @csrf
                    <div class="space-y-3">
                        <input type="text" name="title" placeholder="Subtask title" class="form-input" required>
                        <textarea name="description" placeholder="Description (optional)" rows="2" class="form-input"></textarea>
                        <button type="submit" class="btn-primary text-sm">Create Subtask</button>
                    </div>
                </form>
                @endcan

                @forelse($task->subtasks as $subtask)
                <div class="surface-row mb-2 flex items-center gap-3 p-3">
                    <form method="POST" action="{{ route('subtasks.update', $subtask) }}" class="shrink-0">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_completed" value="{{ $subtask->is_completed ? '0' : '1' }}">
                        <button type="submit" class="w-5 h-5 rounded border {{ $subtask->is_completed ? 'bg-green-500 border-green-500' : 'border-gray-300' }} flex items-center justify-center">
                            @if($subtask->is_completed)
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    </form>
                    <div class="flex-1">
                        <p class="text-sm font-medium {{ $subtask->is_completed ? 'text-gray-400 line-through' : 'text-gray-800' }}">{{ $subtask->title }}</p>
                        @if($subtask->description)
                        <p class="text-xs text-gray-500">{{ $subtask->description }}</p>
                        @endif
                    </div>
                    @can('update', $task)
                    <form method="POST" action="{{ route('subtasks.destroy', $subtask) }}" onsubmit="return confirm('Delete this subtask?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-action icon-action-danger !h-8 !w-8" title="Delete subtask">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                    @endcan
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No subtasks yet.</p>
                @endforelse
            </div>

            <!-- Comments -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Comments ({{ $task->comments->count() }})</h2>
                </div>

                <form method="POST" action="{{ route('comments.store', $task) }}" class="mb-4">
                    @csrf
                    <div class="space-y-3">
                        <textarea name="content" placeholder="Add a comment..." rows="3" class="form-input" required></textarea>
                        <button type="submit" class="btn-primary text-sm">Post Comment</button>
                    </div>
                </form>

                @forelse($task->comments as $comment)
                <div class="surface-row mb-3 p-4">
                    <div class="flex items-start gap-3">
                        <div class="avatar-mark w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                            <span class="text-white text-xs font-bold">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</p>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ $comment->content }}</p>
                        </div>
                        @if(auth()->user()->id === $comment->user_id)
                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="icon-action icon-action-danger !h-8 !w-8" title="Delete comment">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No comments yet.</p>
                @endforelse
            </div>

            <!-- Attachments -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Attachments ({{ $task->attachments->count() }})</h2>
                </div>

                <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <input type="hidden" name="attachable_type" value="App\Models\Task">
                    <input type="hidden" name="attachable_id" value="{{ $task->id }}">
                    <div class="flex items-center gap-3">
                        <input type="file" name="file" class="form-input" required>
                        <button type="submit" class="btn-primary text-sm">Upload</button>
                    </div>
                </form>

                @forelse($task->attachments as $attachment)
                <div class="surface-row mb-2 flex items-center justify-between p-3">
                    <div class="flex items-center gap-3">
                        @if($attachment->isImage())
                        <div class="w-10 h-10 rounded-lg border border-slate-200 bg-white/70 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        @else
                        <div class="w-10 h-10 rounded-lg border border-cyan-200 bg-cyan-50/80 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $attachment->file_name }}</p>
                            <p class="text-xs text-gray-500">{{ $attachment->file_size_formatted }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('attachments.show', $attachment) }}" target="_blank" class="btn-secondary text-xs py-1.5 px-3">View</a>
                        @can('delete', $attachment)
                        <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm('Delete this attachment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger text-xs py-1.5 px-3">Delete</button>
                        </form>
                        @endcan
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No attachments yet.</p>
                @endforelse
            </div>
        </section>

        <aside class="space-y-4">
            <div class="card space-y-3 text-sm">
                <h2 class="font-semibold text-gray-900">Task Details</h2>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Assignee:</span> {{ $task->assignee?->name ?? 'Unassigned' }}</p>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Due date:</span> {{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</p>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Estimated hours:</span> {{ $task->estimated_hours ?? 'Not set' }}</p>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Spent hours:</span> {{ $task->spent_hours }}</p>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Effort progress:</span> {{ $task->effortProgressPercentage() !== null ? $task->effortProgressPercentage() . '%' : 'N/A' }}</p>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Created:</span> {{ $task->created_at->format('M d, Y') }}</p>
                <p class="text-gray-600"><span class="font-medium text-gray-900">Updated:</span> {{ $task->updated_at->format('M d, Y') }}</p>
            </div>

            <div class="card space-y-3 text-sm">
                <h2 class="font-semibold text-gray-900">Project Owner</h2>
                <p class="text-gray-600">{{ $task->project->owner->name }}</p>
                <p class="text-gray-500">{{ $task->project->members->count() }} member(s)</p>
            </div>
        </aside>
    </div>
</div>
@endsection
