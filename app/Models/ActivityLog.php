<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenient static helper to record an activity.
     */
    public static function record(string $action, Model $model, string $description): self
    {
        return self::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model_type'  => get_class($model),
            'model_id'    => $model->getKey(),
            'description' => $description,
        ]);
    }

    public function modelLabel(): string
    {
        return class_basename($this->model_type);
    }

    public function url(): ?string
    {
        $model = $this->model_type::find($this->model_id);

        if (! $model) {
            return null;
        }

        return match ($this->model_type) {
            Project::class => Route::has('projects.show') ? route('projects.show', $model) : null,
            Task::class => Route::has('tasks.show') ? route('tasks.show', $model) : null,
            Comment::class => $model->task && Route::has('tasks.show')
                ? route('tasks.show', $model->task) . '#comment-' . $model->id
                : null,
            default => null,
        };
    }
}
