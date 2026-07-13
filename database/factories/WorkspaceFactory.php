<?php

namespace Database\Factories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(2),
            'owner_email' => fake()->unique()->safeEmail(),
            'country' => 'NG',
            'timezone' => 'Africa/Lagos',
            'plan' => 'starter',
            'trial_ends_at' => now()->addDays(7),
            'subscription_status' => 'trialing',
            'language' => 'en',
        ];
    }
}
