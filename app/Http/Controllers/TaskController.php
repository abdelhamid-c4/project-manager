<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', Task::class);

        $projects = $request->user()
            ->accessibleProjects()
            ->latest()
            ->get()
            ->filter(fn (Project $project) => $request->user()->can('update', $project));
        $users    = User::orderBy('name')->get();

        $selectedProject = $request->query('project_id')
            ? Project::find($request->query('project_id'))
            : null;

        return view('tasks.create', compact('projects', 'users', 'selectedProject'));
    }

    public function store(StoreTaskRequest $request)
    {
        $project = Project::findOrFail($request->project_id);
        $this->authorize('update', $project); // must be able to manage the project

        $task = Task::create($request->validated());

        ActivityLog::record(
            'task.created',
            $task,
            "Task \"{$task->title}\" was created in project \"{$project->name}\" by {$request->user()->name}."
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load(['project.owner', 'project.members', 'assignee', 'comments.user', 'attachments.user', 'subtasks']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $task->load(['project', 'comments.user']);
        $users = User::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        ActivityLog::record(
            'task.updated',
            $task,
            "Task \"{$task->title}\" was updated by {$request->user()->name}."
        );

        return redirect()->route('projects.show', $task->project_id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);

        $projectId = $task->project_id;
        $title     = $task->title;
        $task->delete();

        ActivityLog::record(
            'task.deleted',
            $task,
            "Task \"{$title}\" was deleted by {$request->user()->name}."
        );

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Task deleted.');
    }
}
