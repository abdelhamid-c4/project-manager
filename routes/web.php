<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AiTeamReportController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');
Route::view('/contact', 'contact')->name('contact');

// All authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/ai-work-report', [DashboardController::class, 'aiReport'])
        ->name('dashboard.ai-work-report');
    Route::get('/dashboard/ai-team-report', AiTeamReportController::class)
        ->middleware('throttle:10,1')
        ->name('dashboard.ai-team-report');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->middleware('throttle:20,1')
        ->name('profile.update');
    // Projects — rate limit create/store to prevent spam
    Route::resource('projects', ProjectController::class)->except('store');
    Route::post('/projects', [ProjectController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('projects.store');

    // Tasks - rate limit create/store/update/delete
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])
        ->middleware('throttle:60,1')
        ->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('tasks.destroy');

    // Audit logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Attachments - rate limit to prevent abuse + SSRF protection
    Route::post('/attachments', [AttachmentController::class, 'store'])
        ->middleware('throttle:20,1', 'ssrf')
        ->name('attachments.store');
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('attachments.show');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('attachments.destroy');

    // Comments - rate limit to prevent spam
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:50,1')
        ->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('comments.destroy');

    // Subtasks - rate limit to prevent abuse
    Route::post('/tasks/{task}/subtasks', [SubtaskController::class, 'store'])
        ->middleware('throttle:40,1')
        ->name('subtasks.store');
    Route::put('/subtasks/{subtask}', [SubtaskController::class, 'update'])
        ->middleware('throttle:60,1')
        ->name('subtasks.update');
    Route::delete('/subtasks/{subtask}', [SubtaskController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('subtasks.destroy');

    // Milestones - rate limit to prevent abuse
    Route::post('/projects/{project}/milestones', [MilestoneController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('milestones.store');
    Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])
        ->middleware('throttle:60,1')
        ->name('milestones.update');
    Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('milestones.destroy');
});

require __DIR__ . '/auth.php';
