<?php

namespace App\Social\Publishers;

use App\Enums\InstagramFormat;
use App\Enums\MediaType;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialPublisher;
use App\Social\PublishResult;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class InstagramPublisher implements SocialPublisher
{
    public function publish(Post $post, SocialAccount $account): PublishResult
    {
        try {
            $this->assertReady($post, $account);

            $format = $post->instagram_format ?? InstagramFormat::Post;
            $containerId = $format === InstagramFormat::Story
                ? $this->createStoryContainer($post, $account)
                : $this->createFeedContainer($post, $account);

            $this->waitUntilReady($account, $containerId);

            $published = $this->graph($account)
                ->post($account->external_id.'/media_publish', [
                    'creation_id' => $containerId,
                ])
                ->json();

            $mediaId = data_get($published, 'id');

            if (! is_string($mediaId) || $mediaId === '') {
                throw new RuntimeException('Instagram не вернул id публикации.');
            }

            return PublishResult::ok($mediaId, 'https://www.instagram.com/p/'.$mediaId);
        } catch (Throwable $exception) {
            return PublishResult::failed($exception->getMessage());
        }
    }

    private function createStoryContainer(Post $post, SocialAccount $account): string
    {
        $media = $post->media->first();

        if (! $media instanceof PostMedia) {
            throw new RuntimeException('Для сторис нужно фото или видео.');
        }

        $payload = [
            'media_type' => 'STORIES',
        ];

        if ($media->type === MediaType::Video) {
            $payload['video_url'] = $media->absoluteUrl();
        } else {
            $payload['image_url'] = $media->absoluteUrl();
        }

        return $this->createContainer($account, $payload);
    }

    private function createFeedContainer(Post $post, SocialAccount $account): string
    {
        if ($post->hasVideo()) {
            $video = $post->firstVideo();

            if (! $video instanceof PostMedia) {
                throw new RuntimeException('Не найдено видео для Instagram.');
            }

            return $this->createContainer($account, [
                'media_type' => 'REELS',
                'video_url' => $video->absoluteUrl(),
                'caption' => $post->caption,
            ]);
        }

        $photos = $post->photos();

        if ($photos->isEmpty()) {
            throw new RuntimeException('Для поста Instagram нужно фото или видео.');
        }

        if ($photos->count() === 1) {
            return $this->createContainer($account, [
                'image_url' => $photos->first()->absoluteUrl(),
                'caption' => $post->caption,
            ]);
        }

        $children = $photos->map(function (PostMedia $photo) use ($account): string {
            return $this->createContainer($account, [
                'image_url' => $photo->absoluteUrl(),
                'is_carousel_item' => true,
            ]);
        });

        return $this->createContainer($account, [
            'media_type' => 'CAROUSEL',
            'children' => $children->implode(','),
            'caption' => $post->caption,
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createContainer(SocialAccount $account, array $payload): string
    {
        $response = $this->graph($account)
            ->post($account->external_id.'/media', $payload)
            ->json();

        $id = data_get($response, 'id');

        if (! is_string($id) || $id === '') {
            throw new RuntimeException((string) data_get($response, 'error.message', 'Не удалось создать контейнер Instagram.'));
        }

        return $id;
    }

    private function waitUntilReady(SocialAccount $account, string $containerId): void
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $status = $this->graph($account)
                ->get($containerId, ['fields' => 'status_code'])
                ->json('status_code');

            if ($status === 'FINISHED') {
                return;
            }

            if (in_array($status, ['ERROR', 'EXPIRED'], true)) {
                throw new RuntimeException('Instagram отклонил медиа: '.(string) $status);
            }

            sleep(3);
        }

        throw new RuntimeException('Instagram слишком долго обрабатывает медиа.');
    }

    private function graph(SocialAccount $account): PendingRequest
    {
        $version = (string) config('social.instagram.graph_version');

        return Http::baseUrl('https://graph.facebook.com/'.$version)
            ->timeout(120)
            ->throw()
            ->withQueryParameters([
                'access_token' => (string) $account->access_token,
            ]);
    }

    private function assertReady(Post $post, SocialAccount $account): void
    {
        if (! filled($account->access_token) || ! filled($account->external_id)) {
            throw new RuntimeException('Instagram-аккаунт не подключён через OAuth.');
        }

        if ($post->media->isEmpty()) {
            throw new RuntimeException('Instagram требует фото или видео.');
        }

        $appUrl = (string) config('app.url');

        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            throw new RuntimeException('Instagram API нужен публичный HTTPS URL медиа. Сейчас APP_URL локальный.');
        }
    }
}
