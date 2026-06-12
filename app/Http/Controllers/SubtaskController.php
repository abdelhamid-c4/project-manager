<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subtask = Subtask::create([
            'task_id' => $task->id,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $task->subtasks()->count(),
        ]);

        ActivityLog::record(
            'subtask.created',
            $subtask,
            "Subtask \"{$subtask->title}\" created in task \"{$task->title}\" by {$request->user()->name}."
        );

        return back()->with('success', 'Subtask added successfully.');
    }

    public function update(Request $request, Subtask $subtask)
    {
        $this->authorize('update', $subtask->task);

        $request->validate([
            'is_completed' => 'boolean',
        ]);

        $subtask->update($request->only('is_completed'));

        $action = $subtask->is_completed ? 'completed' : 'marked incomplete';

        ActivityLog::record(
            'subtask.updated',
            $subtask,
            "Subtask \"{$subtask->title}\" {$action} by {$request->user()->name}."
        );

        return back()->with('success', 'Subtask updated successfully.');
    }

    public function destroy(Subtask $subtask)
    {
        $this->authorize('update', $subtask->task);

        $subtask->delete();

        return back()->with('success', 'Subtask deleted successfully.');
    }
}
