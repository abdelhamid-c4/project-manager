<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $canViewActivityLogs = $user->hasRole(['admin', 'project_manager']);

        // Scope projects based on user role
        $projectQuery = $user->accessibleProjects();

        $stats = [
            'total_projects'    => (clone $projectQuery)->count(),
            'active_projects'   => (clone $projectQuery)->where('status', 'in_progress')->count(),
            'completed_projects'=> (clone $projectQuery)->where('status', 'completed')->count(),
            'pending_projects'  => (clone $projectQuery)->where('status', 'pending')->count(),
        ];

        // Pending tasks for this user's accessible projects
        $projectIds = (clone $projectQuery)->pluck('id');

        $pendingTasks = Task::whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->count();

        $recentTasks = Task::with(['project', 'assignee'])
            ->whereIn('project_id', $projectIds)
            ->latest()
            ->take(5)
            ->get();

        $projects = (clone $projectQuery)
            ->with(['owner', 'members', 'tasks'])
            ->latest()
            ->take(6)
            ->get();

        $recentLogs = $canViewActivityLogs
            ? ActivityLog::with('user')
                ->whereDate('created_at', today())
                ->latest()
                ->take(10)
                ->get()
            : collect();

        $tasksByStatus = Task::whereIn('project_id', $projectIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $dashboardData = [
            'user' => [
                'name' => $user->name,
                'role' => $user->getRoleNames()->first() ?? 'User',
                'canCreateProject' => $user->can('create', Project::class),
                'canCreateTask' => $user->can('create', Task::class),
                'canViewActivityLogs' => $canViewActivityLogs,
            ],
            'routes' => [
                'projectsIndex' => route('projects.index'),
                'projectsCreate' => $user->can('create', Project::class) ? route('projects.create') : null,
                'tasksCreate' => $user->can('create', Task::class) ? route('tasks.create') : null,
                'activityLogs' => $canViewActivityLogs ? route('activity-logs.index') : null,
                'teamReport' => route('dashboard.ai-team-report'),
                'teamReportPage' => route('dashboard.ai-work-report'),
            ],
            'stats' => [
                ...$stats,
                'pending_tasks' => $pendingTasks,
                'todo_tasks' => (int) ($tasksByStatus['todo'] ?? 0),
                'in_progress_tasks' => (int) ($tasksByStatus['in_progress'] ?? 0),
                'done_tasks' => (int) ($tasksByStatus['done'] ?? 0),
            ],
            'projects' => $projects->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'owner' => $project->owner?->name,
                'members' => $project->members->pluck('name')->values(),
                'tasksTotal' => $project->tasks->count(),
                'tasksDone' => $project->tasks->where('status', 'done')->count(),
                'tasksOpen' => $project->tasks->where('status', '!=', 'done')->count(),
                'url' => route('projects.show', $project),
            ])->values(),
            'recentTasks' => $recentTasks->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'project' => $task->project?->name,
                'assignee' => $task->assignee?->name,
                'dueDate' => $task->due_date?->format('M d'),
                'isOverdue' => $task->isOverdue(),
                'url' => $user->can('update', $task)
                    ? route('tasks.edit', $task)
                    : route('tasks.show', $task),
                'actionLabel' => $user->can('update', $task) ? 'Edit' : 'View',
            ])->values(),
            'recentLogs' => $recentLogs->map(fn (ActivityLog $log) => [
                'id' => $log->id,
                'description' => $log->description,
                'action' => $log->action,
                'user' => $log->user?->name ?? 'System',
                'time' => $log->created_at->diffForHumans(),
                'url' => $log->url(),
            ])->values(),
        ];

        return view('dashboard.index', compact('dashboardData'));
    }

    public function aiReport(Request $request)
    {
        $user = $request->user();

        $reportData = [
            'user' => [
                'name' => $user->name,
                'role' => $user->getRoleNames()->first() ?? 'User',
            ],
            'routes' => [
                'dashboard' => route('dashboard'),
                'teamReport' => route('dashboard.ai-team-report'),
            ],
        ];

        return view('dashboard.ai-report', compact('reportData'));
    }
}
