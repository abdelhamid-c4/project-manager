<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_exposes_management_routes_and_audit_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Admin Portfolio',
            'status' => 'in_progress',
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Admin task',
            'status' => 'todo',
            'priority' => 'high',
        ]);
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'project.created',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'description' => 'Admin-only audit event',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Admin-only audit event');
        $response->assertSee('"canCreateProject":true', false);
        $response->assertSee('"canCreateTask":true', false);
        $response->assertSee('"canViewActivityLogs":true', false);
        $response->assertSee('"url":"http:\/\/localhost\/tasks\/'.$task->id.'\/edit"', false);
        $response->assertSee('"url":"http:\/\/localhost\/projects\/'.$project->id.'"', false);
    }

    public function test_team_member_dashboard_hides_audit_data_and_uses_assigned_task_edit_links(): void
    {
        $manager = User::factory()->create(['role' => 'project_manager']);
        $member = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $manager->id,
            'name' => 'Team Project',
            'status' => 'in_progress',
        ]);
        $project->members()->attach($member->id);
        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Team visible task',
            'status' => 'todo',
            'priority' => 'medium',
        ]);
        ActivityLog::create([
            'user_id' => $manager->id,
            'action' => 'project.updated',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'description' => 'Sensitive audit event',
        ]);

        $response = $this->actingAs($member)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Sensitive audit event');
        $response->assertSee('"canCreateProject":false', false);
        $response->assertSee('"canCreateTask":false', false);
        $response->assertSee('"canViewActivityLogs":false', false);
        $response->assertSee('"activityLogs":null', false);
        $response->assertSee('"projectsCreate":null', false);
        $response->assertSee('"url":"http:\/\/localhost\/tasks\/'.$task->id.'\/edit"', false);
        $response->assertSee('"actionLabel":"Edit"', false);
    }

    public function test_role_protected_click_targets_return_expected_status_codes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Protected Project',
            'status' => 'pending',
        ]);
        $project->members()->attach($member->id);
        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Read-only task',
            'status' => 'todo',
            'priority' => 'low',
        ]);

        $this->actingAs($member)->get(route('projects.index'))->assertOk();
        $this->actingAs($member)->get(route('projects.show', $project))->assertOk();
        $this->actingAs($member)->get(route('tasks.show', $task))->assertOk();
        $this->actingAs($member)->get(route('activity-logs.index'))->assertForbidden();
        $this->actingAs($member)->get(route('projects.create'))->assertForbidden();
        $this->actingAs($member)->get(route('tasks.create'))->assertForbidden();
        $this->actingAs($member)->get(route('tasks.edit', $task))->assertOk();

        $this->actingAs($admin)->get(route('activity-logs.index'))->assertOk();
        $this->actingAs($admin)->get(route('projects.create'))->assertOk();
        $this->actingAs($admin)->get(route('tasks.create'))->assertOk();
        $this->actingAs($admin)->get(route('tasks.edit', $task))->assertOk();
    }

    public function test_dashboard_activity_stream_only_shows_todays_clickable_logs(): void
    {
        Carbon::setTestNow('2026-05-18 10:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Today Project',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'project.created',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'description' => 'Today visible activity',
        ]);

        $oldLog = ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'project.updated',
            'model_type' => Project::class,
            'model_id' => $project->id,
            'description' => 'Yesterday hidden activity',
        ]);
        $oldLog->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->save();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Today visible activity');
        $response->assertSee('"url":"http:\/\/localhost\/projects\/'.$project->id.'"', false);
        $response->assertDontSee('Yesterday hidden activity');

        Carbon::setTestNow();
    }

    public function test_admin_can_see_task_comments_on_edit_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $commenter = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Comment Project',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Commented task',
            'status' => 'todo',
            'priority' => 'low',
        ]);
        Comment::create([
            'user_id' => $commenter->id,
            'task_id' => $task->id,
            'content' => 'This comment should be visible to admins.',
        ]);

        $response = $this->actingAs($admin)->get(route('tasks.edit', $task));

        $response->assertOk();
        $response->assertSee('Comments (1)');
        $response->assertSee('This comment should be visible to admins.');
    }
}
