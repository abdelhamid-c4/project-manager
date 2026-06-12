<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_attachment_uploaded_by_another_user(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Attachment Project',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Task with image',
            'status' => 'todo',
            'priority' => 'low',
        ]);

        Storage::disk('public')->put('attachments/test-image.png', 'fake image content');

        $attachment = Attachment::create([
            'user_id' => $member->id,
            'attachable_type' => Task::class,
            'attachable_id' => $task->id,
            'file_name' => 'test-image.png',
            'file_path' => 'attachments/test-image.png',
            'file_type' => 'image/png',
            'file_size' => 18,
        ]);

        $response = $this->actingAs($admin)->delete(route('attachments.destroy', $attachment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing('attachments/test-image.png');
    }

    public function test_admin_can_view_attachment_uploaded_by_another_user(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Attachment Project',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Task with image',
            'status' => 'todo',
            'priority' => 'low',
        ]);

        Storage::disk('public')->put('attachments/test-image.png', 'fake image content');

        $attachment = Attachment::create([
            'user_id' => $member->id,
            'attachable_type' => Task::class,
            'attachable_id' => $task->id,
            'file_name' => 'test-image.png',
            'file_path' => 'attachments/test-image.png',
            'file_type' => 'image/png',
            'file_size' => 18,
        ]);

        $response = $this->actingAs($admin)->get(route('attachments.show', $attachment));

        $response->assertOk();
        $response->assertHeader('content-type', 'image/png');
    }

    public function test_attachment_view_button_uses_authorized_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Attachment Project',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Task with image',
            'status' => 'todo',
            'priority' => 'low',
        ]);

        $attachment = Attachment::create([
            'user_id' => $member->id,
            'attachable_type' => Task::class,
            'attachable_id' => $task->id,
            'file_name' => 'test-image.png',
            'file_path' => 'attachments/test-image.png',
            'file_type' => 'image/png',
            'file_size' => 18,
        ]);

        $response = $this->actingAs($admin)->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertSee(route('attachments.show', $attachment), false);
        $response->assertDontSee('/storage/attachments/test-image.png', false);
    }

    public function test_admin_sees_delete_action_for_attachment_uploaded_by_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'team_member']);
        $project = Project::create([
            'owner_id' => $admin->id,
            'name' => 'Attachment Project',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Task with image',
            'status' => 'todo',
            'priority' => 'low',
        ]);

        Attachment::create([
            'user_id' => $member->id,
            'attachable_type' => Task::class,
            'attachable_id' => $task->id,
            'file_name' => 'test-image.png',
            'file_path' => 'attachments/test-image.png',
            'file_type' => 'image/png',
            'file_size' => 18,
        ]);

        $response = $this->actingAs($admin)->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertSee('test-image.png');
        $response->assertSee('Delete');
    }
}
