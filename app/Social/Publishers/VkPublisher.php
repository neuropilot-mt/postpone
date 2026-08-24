<?php

namespace App\Social\Publishers;

use App\Enums\MediaType;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialPublisher;
use App\Social\PublishResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class VkPublisher implements SocialPublisher
{
    public function publish(Post $post, SocialAccount $account): PublishResult
    {
        try {
            if (! filled($account->access_token)) {
                throw new RuntimeException('VK-аккаунт не подключён через OAuth.');
            }

            $attachments = [];

            foreach ($post->media as $media) {
                $attachments[] = $media->type === MediaType::Video
                    ? $this->uploadVideo($post, $account, $media)
                    : $this->uploadPhoto($account, $media);
            }

            $ownerId = $account->external_id;
            $payload = [
                'message' => $post->caption,
                'attachments' => implode(',', $attachments),
                'from_group' => 1,
            ];

            if (filled($ownerId) && str_starts_with((string) $ownerId, '-')) {
                $payload['owner_id'] = $ownerId;
            }

            $response = $this->api($account, 'wall.post', $payload);
            $postId = data_get($response, 'response.post_id');

            if (! is_numeric($postId)) {
                throw new RuntimeException((string) data_get($response, 'error.error_msg', 'VK не вернул id поста.'));
            }

            $urlOwner = $ownerId ?: data_get($account->meta, 'user_id');

            return PublishResult::ok(
                (string) $postId,
                'https://vk.com/wall'.$urlOwner.'_'.$postId,
            );
        } catch (Throwable $exception) {
            return PublishResult::failed($exception->getMessage());
        }
    }

    private function uploadPhoto(SocialAccount $account, PostMedia $media): string
    {
        $server = $this->api($account, 'photos.getWallUploadServer', [
            'group_id' => $this->groupId($account),
        ]);

        $uploadUrl = data_get($server, 'response.upload_url');

        if (! is_string($uploadUrl)) {
            throw new RuntimeException('VK не отдал upload_url для фото.');
        }

        $contents = file_get_contents($media->absolutePath());

        if ($contents === false) {
            throw new RuntimeException('Не удалось прочитать файл фото.');
        }

        $uploaded = Http::timeout(120)
            ->attach('photo', $contents, $media->original_name)
            ->post($uploadUrl)
            ->json();

        $saved = $this->api($account, 'photos.saveWallPhoto', [
            'group_id' => $this->groupId($account),
            'photo' => data_get($uploaded, 'photo'),
            'server' => data_get($uploaded, 'server'),
            'hash' => data_get($uploaded, 'hash'),
        ]);

        $photo = data_get($saved, 'response.0');

        if (! is_array($photo)) {
            throw new RuntimeException('Не удалось сохранить фото во VK.');
        }

        return 'photo'.data_get($photo, 'owner_id').'_'.data_get($photo, 'id');
    }

    private function uploadVideo(Post $post, SocialAccount $account, PostMedia $media): string
    {
        $save = $this->api($account, 'video.save', [
            'name' => str($post->caption ?: $media->original_name)->limit(128)->toString(),
            'description' => $post->caption,
            'group_id' => $this->groupId($account),
            'wallpost' => 0,
        ]);

        $uploadUrl = data_get($save, 'response.upload_url');

        if (! is_string($uploadUrl)) {
            throw new RuntimeException('VK не отдал upload_url для видео.');
        }

        $contents = file_get_contents($media->absolutePath());

        if ($contents === false) {
            throw new RuntimeException('Не удалось прочитать файл видео.');
        }

        Http::timeout(300)
            ->attach('video_file', $contents, $media->original_name)
            ->post($uploadUrl)
            ->throw();

        return 'video'.data_get($save, 'response.owner_id').'_'.data_get($save, 'response.video_id');
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function api(SocialAccount $account, string $method, array $params = []): array
    {
        $response = Http::timeout(60)
            ->asForm()
            ->post('https://api.vk.com/method/'.$method, array_filter([
                ...$params,
                'access_token' => $account->access_token,
                'v' => config('social.vk.api_version'),
            ], fn ($value) => $value !== null && $value !== ''))
            ->throw()
            ->json();

        if (! is_array($response)) {
            throw new RuntimeException('Пустой ответ VK API.');
        }

        if (isset($response['error'])) {
            throw new RuntimeException((string) data_get($response, 'error.error_msg', 'Ошибка VK API.'));
        }

        return $response;
    }

    private function groupId(SocialAccount $account): ?int
    {
        $id = $account->external_id;

        if (! filled($id)) {
            return null;
        }

        $numeric = (int) ltrim((string) $id, '-');

        return $numeric > 0 ? $numeric : null;
    }
}
