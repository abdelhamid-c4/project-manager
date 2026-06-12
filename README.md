# Laravel Project Management App

A full-featured project management system with role-based access control, task tracking, audit logs, and an AI-ready architecture.

---

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade + Tailwind CSS (via CDN)
- **Database**: MySQL
- **Auth**: Laravel Breeze
- **Roles/Permissions**: Spatie Laravel Permission

---

## Installation

### 1. Create a new Laravel project

```bash
composer create-project laravel/laravel project-manager
cd project-manager
```

### 2. Install dependencies

```bash
composer require laravel/breeze spatie/laravel-permission
php artisan breeze:install blade
npm install && npm run build
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 3. Configure `.env`

```env
APP_NAME="Project Manager"
APP_URL=http://localhost:8000
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_manager
DB_USERNAME=root
DB_PASSWORD=your_password

OPENAI_API_KEY=your_openai_key_here
```

### 4. Copy all project files

Copy all files from this repository into your Laravel project, maintaining the directory structure.

### 5. Run migrations and seeders

```bash
php artisan migrate:fresh --seed
```

### 6. Start the development server

```bash
php artisan serve
```

Visit: http://localhost:8000

---

## Default Credentials

| Role            | Email                    | Password   |
|-----------------|--------------------------|------------|
| Admin           | admin@example.com        | password   |
| Project Manager | manager@example.com      | password   |
| Team Member     | member@example.com       | password   |
| Client          | client@example.com       | password   |

---

## Manual Test Checklist

### Authentication
- [ ] Register a new user
- [ ] Login with existing user
- [ ] Logout
- [ ] Cannot access dashboard without login

### Admin
- [ ] Can view all projects
- [ ] Can create/edit/delete any project
- [ ] Can create/edit/delete any task
- [ ] Can view activity logs
- [ ] Can view all users

### Project Manager
- [ ] Can create projects
- [ ] Can edit their own projects
- [ ] Cannot delete projects they don't own
- [ ] Can create tasks in their projects
- [ ] Cannot access admin-only routes

### Team Member
- [ ] Can only see projects they are assigned to
- [ ] Can update status of tasks assigned to them
- [ ] Cannot create projects
- [ ] Cannot delete tasks

### Client
- [ ] Can only view projects they are assigned to
- [ ] Cannot create, edit, or delete anything
- [ ] Cannot see activity logs

### Dashboard
- [ ] Shows correct project statistics
- [ ] Shows recent tasks
- [ ] Shows recent activity logs

### Audit Logs
- [ ] Project creation is logged
- [ ] Project update is logged
- [ ] Project deletion is logged
- [ ] Task creation is logged
- [ ] Task update is logged
- [ ] Task deletion is logged

---

## Directory Structure

```
app/
  Http/
    Controllers/
      DashboardController.php
      ProjectController.php
      TaskController.php
      ActivityLogController.php
    Requests/
      Project/
        StoreProjectRequest.php
        UpdateProjectRequest.php
      Task/
        StoreTaskRequest.php
        UpdateTaskRequest.php
  Models/
    User.php
    Project.php
    Task.php
    ActivityLog.php
  Policies/
    ProjectPolicy.php
    TaskPolicy.php
  Services/
    AiProjectService.php

database/
  migrations/
    *_create_projects_table.php
    *_create_tasks_table.php
    *_create_project_user_table.php
    *_create_activity_logs_table.php
  seeders/
    RolePermissionSeeder.php
    DefaultAdminSeeder.php
    DatabaseSeeder.php

resources/views/
  layouts/
    app.blade.php
    navigation.blade.php
  dashboard/
    index.blade.php
  projects/
    index.blade.php
    create.blade.php
    show.blade.php
    edit.blade.php
  tasks/
    create.blade.php
    edit.blade.php
  activity-logs/
    index.blade.php

routes/
  web.php
```
