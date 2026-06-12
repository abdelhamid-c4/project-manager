<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null;
    }

    public function view(User $user, Task $task): bool
    {
        $project = $task->project;
        return $project->isOwnedBy($user)
            || $project->hasMember($user)
            || $user->hasRole('project_manager');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'project_manager']);
    }

    public function update(User $user, Task $task): bool
    {
        $project = $task->project;

        // Project owner / PM can edit anything in their project
        if ($project->isOwnedBy($user) || $user->hasRole('project_manager')) {
            return true;
        }

        // Team member can only update their own tasks
        if ($user->hasRole('team_member') && $task->isAssignedTo($user)) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project->isOwnedBy($user) || $user->hasRole(['admin', 'project_manager']);
    }
}
