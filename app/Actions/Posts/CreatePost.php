<?php

namespace App\Actions\Posts;

use App\Enums\InstagramFormat;
use App\Enums\MediaType;
use App\Enums\Platform;
use App\Enums\PostStatus;
use App\Enums\TargetStatus;
use App\Http\Requests\StorePostRequest;
use App\Jobs\PublishPostJob;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\PostTarget;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePost
{
    public function handle(User $user, StorePostRequest $request): Post
    {
        /** @var list<string> $platformValues */
        $platformValues = $request->validated('platforms');

        $platforms = array_map(
            fn (string $value): Platform => Platform::from($value),
            $platformValues,
        );

        $accounts = $this->accountsFor($user, $platforms);
        $publishNow = $request->string('publish_mode')->toString() === 'now';
        $caption = $request->string('caption')->trim()->toString();
        $files = $request->mediaFiles();

        $post = DB::transaction(function () use ($user, $request, $files, $platforms, $accounts, $publishNow, $caption): Post {
            $post = Post::query()->create([
                'user_id' => $user->id,
                'caption' => $caption !== '' ? $caption : null,
                'status' => $publishNow ? PostStatus::Publishing : PostStatus::Scheduled,
                'instagram_format' => in_array(Platform::Instagram, $platforms, true)
                    ? InstagramFormat::from((string) $request->input('instagram_format', InstagramFormat::Post->value))
                    : null,
                'scheduled_at' => $publishNow ? now() : $request->date('scheduled_at'),
            ]);

            foreach ($files as $index => $file) {
                $path = $file->store('posts/'.$post->id, 'public');

                PostMedia::query()->create([
                    'post_id' => $post->id,
                    'type' => MediaType::fromMime((string) $file->getMimeType()),
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => (string) $file->getMimeType(),
                    'size' => (int) $file->getSize(),
                    'sort_order' => $index,
                ]);
            }

            foreach ($platforms as $platform) {
                $account = $accounts[$platform->value];

                PostTarget::query()->create([
                    'post_id' => $post->id,
                    'social_account_id' => $account->id,
                    'platform' => $platform,
                    'status' => TargetStatus::Pending,
                ]);
            }

            return $post->load(['media', 'targets.socialAccount']);
        });

        if ($publishNow) {
            PublishPostJob::dispatch($post);
        }

        return $post;
    }

    /**
     * @param  list<Platform>  $platforms
     * @return array<string, SocialAccount>
     */
    private function accountsFor(User $user, array $platforms): array
    {
        $accounts = [];

        foreach ($platforms as $platform) {
            $account = $user->socialAccounts()
                ->where('platform', $platform)
                ->where('is_active', true)
                ->first();

            if (! $account instanceof SocialAccount) {
                throw ValidationException::withMessages([
                    'platforms' => 'Подключите аккаунт '.$platform->label().' перед публикацией.',
                ]);
            }

            $accounts[$platform->value] = $account;
        }

        return $accounts;
    }
}
