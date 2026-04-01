<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedInteger('tasks_completed')->default(0);
            $table->unsignedInteger('tasks_total')->default(100);
            $table->decimal('leave_used', 8, 2)->default(0);
            $table->decimal('leave_total', 8, 2)->default(20);
            $table->unsignedInteger('inventory_used')->default(0);
            $table->unsignedInteger('inventory_total')->default(250);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'tasks_completed',
                'tasks_total',
                'leave_used',
                'leave_total',
                'inventory_used',
                'inventory_total',
            ]);
        });
    }
};
