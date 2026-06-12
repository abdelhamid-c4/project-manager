<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    // Admin bypasses all checks
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // all authenticated users can see the list (filtered in controller)
    }

    public function view(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user)
            || $project->hasMember($user)
            || $user->hasRole('project_manager');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'project_manager']);
    }

    public function update(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user) || $user->hasRole(['admin', 'project_manager']);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user) || $user->hasRole('admin');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }
}
