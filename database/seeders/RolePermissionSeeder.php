<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Roles are stored directly on users in this app.
        // Kept as a no-op so DatabaseSeeder can retain its existing call order.
    }
}
