<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'is_completed',
        'order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'order' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function markAsCompleted(): void
    {
        $this->is_completed = true;
        $this->save();
    }

    public function markAsIncomplete(): void
    {
        $this->is_completed = false;
        $this->save();
    }
}
