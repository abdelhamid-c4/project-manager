<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'due_date',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
    ];

    protected $hidden = [
        'deleted_at',
        'estimated_hours',
        'spent_hours',
    ];

    protected $casts = [
        'due_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'spent_hours' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('order');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function blockingDependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'todo'        => 'bg-gray-100 text-gray-700',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'done'        => 'bg-green-100 text-green-800',
            default       => 'bg-gray-100 text-gray-700',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match($this->priority) {
            'low'    => 'bg-green-100 text-green-700',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high'   => 'bg-red-100 text-red-700',
            default  => 'bg-gray-100 text-gray-700',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function effortProgressPercentage(): ?int
    {
        if (!$this->estimated_hours || (float) $this->estimated_hours <= 0) {
            return null;
        }

        return (int) min(100, round(((float) $this->spent_hours / (float) $this->estimated_hours) * 100));
    }
}
