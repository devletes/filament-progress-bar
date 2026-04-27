<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            ['default config', 'aria@example.com', 68, 14, 215, 'default'],
            ["->textPosition('outside')", 'mateo@example.com', 50, 10, 180, 'outside_text'],
            ["->size('md')", 'hana@example.com', 43, 8, 150, 'medium_size'],
            ["->size('lg')", 'curt@example.com', 91, 18, 240, 'large_size'],
            ["->thresholdDirection('descending')", 'ryan@example.com', 17, 4, 60, 'descending'],
            ["->thresholds([80 => 'success', ...])", 'emelie@example.com', 53, 12, 190, 'threshold_map'],
            ["->borderRadius('4px')", 'noor@example.com', 75, 11, 170, 'border_radius'],
            ['Salman Hijazi', 'salman@example.com', 82, 15, 5, 'infolist_demo'],
        ];

        foreach ($variants as [$name, $email, $tasks, $leave, $inventory, $variant]) {
            User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'tasks_completed' => $tasks,
                'tasks_total' => 100,
                'leave_used' => $leave,
                'leave_total' => 20,
                'inventory_used' => $inventory,
                'inventory_total' => 250,
                'demo_variant' => $variant,
            ]);
        }
    }
}
