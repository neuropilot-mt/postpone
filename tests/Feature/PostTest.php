<?php

namespace Tests\Feature;

use App\Enums\Platform;
use App\Enums\PostStatus;
use App\Enums\TargetStatus;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\PostTarget;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_posts(): void
    {
        $this->get(route('posts.create'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_composer(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('posts.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('posts/create'));
    }

    public function test_user_can_schedule_a_post_to_connected_networks(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        SocialAccount::factory()->platform(Platform::Vk)->create(['user_id' => $user->id]);
        SocialAccount::factory()->platform(Platform::Instagram)->create(['user_id' => $user->id]);

        $photo = UploadedFile::fake()->create('photo.jpg', 120, 'image/jpeg');

        $this->actingAs($user)
            ->post(route('posts.store'), [
                'caption' => 'Тестовый пост',
                'platforms' => [Platform::Vk->value, Platform::Instagram->value],
                'instagram_format' => 'post',
                'publish_mode' => 'scheduled',
                'scheduled_at' => now()->addHour()->format('Y-m-d H:i:s'),
                'media' => [$photo],
            ])
            ->assertRedirect();

        $post = Post::query()->first();

        $this->assertNotNull($post);
        $this->assertSame(PostStatus::Scheduled, $post->status);
        $this->assertSame('Тестовый пост', $post->caption);
        $this->assertCount(1, $post->media);
        $this->assertCount(2, $post->targets);
    }

    public function test_tiktok_requires_video(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        SocialAccount::factory()->platform(Platform::TikTok)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('posts.create'))
            ->post(route('posts.store'), [
                'caption' => 'Только фото',
                'platforms' => [Platform::TikTok->value],
                'publish_mode' => 'now',
                'media' => [UploadedFile::fake()->create('photo.jpg', 120, 'image/jpeg')],
            ])
            ->assertRedirect(route('posts.create'))
            ->assertSessionHasErrors('platforms');
    }

    public function test_youtube_requires_video(): void
    {
        $user = User::factory()->create();
        SocialAccount::factory()->platform(Platform::YouTube)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('posts.create'))
            ->post(route('posts.store'), [
                'caption' => 'Текст без видео',
                'platforms' => [Platform::YouTube->value],
                'publish_mode' => 'now',
            ])
            ->assertSessionHasErrors('platforms');
    }

    public function test_due_posts_are_published_in_sandbox(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $account = SocialAccount::factory()->platform(Platform::Vk)->create(['user_id' => $user->id]);
        $post = Post::factory()->due()->create(['user_id' => $user->id]);
        PostMedia::factory()->create(['post_id' => $post->id]);
        PostTarget::factory()->create([
            'post_id' => $post->id,
            'social_account_id' => $account->id,
            'platform' => Platform::Vk,
            'status' => TargetStatus::Pending,
        ]);

        $this->artisan('posts:publish-due')->assertSuccessful();

        $this->assertSame(PostStatus::Published, $post->fresh()?->status);
        $this->assertSame(TargetStatus::Published, $post->targets()->first()?->status);
    }
}
