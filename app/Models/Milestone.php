<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'due_date',
        'status',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'status' => 'string',
        'order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function completionPercentage(): int
    {
        $tasks = $this->project->tasks;
        $total = $tasks->count();

        if ($total === 0) {
            return 0;
        }

        $done = $tasks->where('status', 'done')->count();

        return (int) round(($done / $total) * 100);
    }
}
