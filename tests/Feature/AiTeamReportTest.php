<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiTeamReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_report_identifies_open_assigned_tasks(): void
    {
        config(['services.groq.key' => null]);

        $manager = User::factory()->create(['role' => 'project_manager']);
        $member = User::factory()->create(['role' => 'team_member', 'name' => 'Nadia Member']);

        $project = Project::create([
            'owner_id' => $manager->id,
            'name' => 'Mobile Launch',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
        $project->members()->attach($member->id);

        $urgentTask = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Fix payment handoff',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => now()->subDay()->toDateString(),
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Prepare QA notes',
            'status' => 'in_progress',
            'priority' => 'medium',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Completed onboarding copy',
            'status' => 'done',
            'priority' => 'high',
        ]);

        Task::create([
            'project_id' => $project->id,
            'title' => 'Unassigned launch check',
            'status' => 'todo',
            'priority' => 'high',
        ]);

        $response = $this->actingAs($member)->getJson(route('dashboard.ai-team-report'));

        $response->assertOk();
        $response->assertJsonPath('report.source', 'local');
        $response->assertJsonPath('report.tasks.0.id', $urgentTask->id);
        $response->assertJsonPath('report.tasks.0.title', 'Fix payment handoff');
        $response->assertJsonPath('report.tasks.0.urgency', 'high');
        $response->assertJsonPath('report.tasks.0.action_label', 'Edit task');
        $response->assertJsonPath('report.tasks.0.url', route('tasks.edit', $urgentTask));
        $response->assertJsonFragment(['summary' => 'You have 2 open assigned task(s), including 1 overdue and 1 high-priority item(s).']);
        $response->assertJsonMissing(['title' => 'Completed onboarding copy']);
        $response->assertJsonMissing(['title' => 'Unassigned launch check']);
    }

    public function test_team_member_report_handles_empty_task_list(): void
    {
        config(['services.groq.key' => null]);

        $member = User::factory()->create(['role' => 'team_member', 'name' => 'Clear Member']);

        $response = $this->actingAs($member)->getJson(route('dashboard.ai-team-report'));

        $response->assertOk();
        $response->assertJsonPath('report.source', 'local');
        $response->assertJsonPath('report.tasks', []);
        $response->assertJsonPath('report.summary', 'No open tasks are currently assigned to Clear Member.');
    }

    public function test_ai_work_report_page_loads_for_authenticated_users(): void
    {
        $member = User::factory()->create(['role' => 'team_member', 'name' => 'Nadia Member']);

        $response = $this->actingAs($member)->get(route('dashboard.ai-work-report'));

        $response->assertOk();
        $response->assertSee('id="ai-report-page"', false);
        $response->assertSee('AI Report');
        $response->assertSee('dashboard\/ai-team-report', false);
    }

    public function test_team_report_requires_authentication(): void
    {
        $this->getJson(route('dashboard.ai-team-report'))->assertUnauthorized();
    }
}
