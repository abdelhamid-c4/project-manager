<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'client')
            ->update(['role' => 'team_member']);
    }

    public function down(): void
    {
        // Intentionally left empty to avoid changing legitimate team members back to clients.
    }
};
