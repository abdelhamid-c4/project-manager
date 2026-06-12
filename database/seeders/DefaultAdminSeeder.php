<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        $admin->assignRole('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            ['name' => 'Project Manager', 'password' => Hash::make('password')]
        );
        $manager->assignRole('project_manager');

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            ['name' => 'Team Member', 'password' => Hash::make('password')]
        );
        $member->assignRole('team_member');

        // Create a demo project
        $project = Project::firstOrCreate(
            ['name' => 'Demo Project'],
            [
                'owner_id'    => $manager->id,
                'description' => 'A sample project to demonstrate the system.',
                'status'      => 'in_progress',
            ]
        );

        // Attach members
        $project->members()->syncWithoutDetaching([$member->id]);

        // Create demo tasks
        if ($project->tasks()->count() === 0) {
            Task::create([
                'project_id'  => $project->id,
                'assigned_to' => $member->id,
                'title'       => 'Set up development environment',
                'description' => 'Install all required dependencies and configure local dev environment.',
                'status'      => 'done',
                'priority'    => 'high',
                'due_date'    => now()->subDays(5)->toDateString(),
            ]);

            Task::create([
                'project_id'  => $project->id,
                'assigned_to' => $member->id,
                'title'       => 'Implement authentication',
                'description' => 'Use Laravel Breeze to implement user authentication.',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'due_date'    => now()->addDays(3)->toDateString(),
            ]);

            Task::create([
                'project_id'  => $project->id,
                'assigned_to' => null,
                'title'       => 'Write unit tests',
                'description' => 'Cover critical business logic with unit tests.',
                'status'      => 'todo',
                'priority'    => 'medium',
                'due_date'    => now()->addDays(10)->toDateString(),
            ]);
        }
    }
}
