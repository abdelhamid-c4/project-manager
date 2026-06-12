<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'due_date' => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'model_id')
                    ->where('model_type', self::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('order');
    }

    public function attachments(): HasMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Helpers
    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'pending'     => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed'   => 'bg-green-100 text-green-800',
            default       => 'bg-gray-100 text-gray-800',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match($this->priority) {
            'low' => 'bg-green-100 text-green-700',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function completionPercentage(): int
    {
        $total = $this->tasks->count();

        if ($total === 0) {
            return 0;
        }

        $done = $this->tasks->where('status', 'done')->count();

        return (int) round(($done / $total) * 100);
    }
}
