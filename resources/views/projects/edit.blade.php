@extends('layouts.app')
@section('title', 'Edit Project')

@section('content')
<div class="pt-8 max-w-3xl space-y-6">

    <div class="page-hero-panel p-5 lg:p-7">
        <div class="flex items-start gap-4">
            <a href="{{ route('projects.show', $project) }}" class="icon-action mt-1" title="Back to project">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="badge bg-cyan-100 text-cyan-800">{{ $project->name }}</span>
                <h1 class="mt-3 text-2xl font-bold text-slate-950">Edit Project</h1>
                <p class="mt-2 text-sm text-slate-500">Refine project details, timing, priority, and team access.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="name" class="form-label">Project Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}"
                       class="form-input @error('name') border-red-500 @enderror" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="form-input @error('description') border-red-500 @enderror">{{ old('description', $project->description) }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-input @error('status') border-red-500 @enderror">
                    @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $project->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="form-label">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" class="form-input @error('priority') border-red-500 @enderror">
                        @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                        <option value="{{ $value }}" {{ old('priority', $project->priority) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="due_date" class="form-label">Target Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                           value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}"
                           class="form-input @error('due_date') border-red-500 @enderror">
                    @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label">Members</label>
                <div class="surface-row max-h-48 overflow-y-auto p-3 space-y-2">
                    @forelse($users as $user)
                    @if($user->id !== $project->owner_id)
                    <label class="flex items-center gap-3 cursor-pointer rounded-md p-2 transition hover:bg-cyan-50/60">
                        <input type="checkbox" name="members[]" value="{{ $user->id }}"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               {{ in_array($user->id, old('members', $memberIds)) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $user->name }}</span>
                        <span class="text-xs text-gray-400 capitalize">{{ $user->getRoleNames()->first() }}</span>
                    </label>
                    @endif
                    @empty
                    <p class="text-sm text-gray-400">No other users found.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 pt-2 border-t border-gray-100">
                <div class="custom-button">
                    <div class="custom-button-blob"></div>
                    <a href="{{ route('projects.show', $project) }}" class="custom-button-inner">
                        Cancel
                    </a>
                </div>
                <div class="custom-button">
                    <div class="custom-button-blob"></div>
                    <button type="submit" class="custom-button-inner">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
