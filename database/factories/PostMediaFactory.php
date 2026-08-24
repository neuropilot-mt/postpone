<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostMedia>
 */
class PostMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'type' => MediaType::Photo,
            'disk' => 'public',
            'path' => 'posts/demo.jpg',
            'original_name' => 'demo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'sort_order' => 0,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (): array => [
            'type' => MediaType::Video,
            'path' => 'posts/demo.mp4',
            'original_name' => 'demo.mp4',
            'mime_type' => 'video/mp4',
        ]);
    }
}
