<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Workbench\App\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'tasks_completed' => fake()->numberBetween(0, 100),
            'tasks_total' => 100,
            'leave_used' => fake()->randomFloat(1, 0, 18),
            'leave_total' => 20,
            'inventory_used' => fake()->numberBetween(0, 250),
            'inventory_total' => 250,
            'demo_variant' => 'default',
            'email_verified_at' => now(),
            'remember_token' => fake()->regexify('[A-Za-z0-9]{10}'),
        ];
    }
}
