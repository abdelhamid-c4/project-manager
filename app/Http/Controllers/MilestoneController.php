<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $milestone = Milestone::create([
            'project_id' => $project->id,
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'order' => $project->milestones()->count(),
        ]);

        ActivityLog::record(
            'milestone.created',
            $milestone,
            "Milestone \"{$milestone->name}\" created in project \"{$project->name}\" by {$request->user()->name}."
        );

        return back()->with('success', 'Milestone created successfully.');
    }

    public function update(Request $request, Milestone $milestone)
    {
        $this->authorize('update', $milestone->project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $milestone->update($request->only('name', 'description', 'due_date', 'status'));

        ActivityLog::record(
            'milestone.updated',
            $milestone,
            "Milestone \"{$milestone->name}\" updated by {$request->user()->name}."
        );

        return back()->with('success', 'Milestone updated successfully.');
    }

    public function destroy(Milestone $milestone)
    {
        $this->authorize('update', $milestone->project);

        $name = $milestone->name;
        $milestone->delete();

        ActivityLog::record(
            'milestone.deleted',
            $milestone,
            "Milestone \"{$name}\" deleted by {$request->user()->name}."
        );

        return back()->with('success', 'Milestone deleted successfully.');
    }
}
