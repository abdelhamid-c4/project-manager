<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $projects = $request->user()
            ->accessibleProjects()
            ->with(['owner', 'members', 'tasks'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);

        $users = User::orderBy('name')->get();

        return view('projects.create', compact('users'));
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->safe()->except('members');

        $project = Project::create([
            'owner_id' => $request->user()->id,
            ...$validated,
        ]);

        if ($request->filled('members')) {
            $project->members()->sync($request->members);
        }

        ActivityLog::record(
            'project.created',
            $project,
            "Project \"{$project->name}\" was created by {$request->user()->name}."
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['owner', 'members', 'tasks.assignee']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $users           = User::orderBy('name')->get();
        $memberIds       = $project->members()->pluck('user_id')->toArray();

        return view('projects.edit', compact('project', 'users', 'memberIds'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->safe()->except('members'));

        $project->members()->sync($request->members ?? []);

        ActivityLog::record(
            'project.updated',
            $project,
            "Project \"{$project->name}\" was updated by {$request->user()->name}."
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project)
    {
        $this->authorize('delete', $project);

        $name = $project->name;
        $project->delete();

        ActivityLog::record(
            'project.deleted',
            $project,
            "Project \"{$name}\" was deleted by {$request->user()->name}."
        );

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
