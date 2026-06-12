<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'service',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Projects this user owns
    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    // Projects this user is a member of
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    // Tasks assigned to this user
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    // Activity logs created by this user
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function trustedDevices(): HasMany
    {
        return $this->hasMany(UserTrustedDevice::class);
    }

    // All projects the user has access to (owned + member)
    public function accessibleProjects()
    {
        if ($this->hasRole(['admin', 'project_manager'])) {
            return Project::query();
        }

        return Project::where('owner_id', $this->id)
            ->orWhereHas('members', fn($q) => $q->where('user_id', $this->id));
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function assignRole(string $role): void
    {
        $this->forceFill(['role' => $role])->save();
    }

    public function getRoleNames(): Collection
    {
        return collect(array_filter([$this->role]));
    }
}
