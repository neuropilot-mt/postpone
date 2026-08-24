<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'caption' => fake()->sentence(),
            'status' => PostStatus::Scheduled,
            'scheduled_at' => now()->addHour(),
        ];
    }

    public function due(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Scheduled,
            'scheduled_at' => now()->subMinute(),
        ]);
    }
}
