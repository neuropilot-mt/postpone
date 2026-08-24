<?php

namespace Database\Factories;

use App\Enums\Platform;
use App\Enums\TargetStatus;
use App\Models\Post;
use App\Models\PostTarget;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostTarget>
 */
class PostTargetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'social_account_id' => SocialAccount::factory(),
            'platform' => Platform::Vk,
            'status' => TargetStatus::Pending,
        ];
    }
}
