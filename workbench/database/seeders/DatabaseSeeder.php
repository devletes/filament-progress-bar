<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'tasks_completed' => 68,
            'tasks_total' => 100,
            'leave_used' => 14,
            'leave_total' => 20,
            'inventory_used' => 215,
            'inventory_total' => 250,
        ]);

        User::factory()->count(5)->create();
    }
}
