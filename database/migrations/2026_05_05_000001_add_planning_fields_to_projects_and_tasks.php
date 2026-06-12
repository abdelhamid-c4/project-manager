<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
            $table->date('due_date')->nullable()->after('priority');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('estimated_hours', 6, 2)->nullable()->after('due_date');
            $table->decimal('spent_hours', 6, 2)->default(0)->after('estimated_hours');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['estimated_hours', 'spent_hours']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['priority', 'due_date']);
        });
    }
};
