<?php

namespace App\Social\Publishers;

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialPublisher;
use App\Social\PublishResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class YouTubePublisher implements SocialPublisher
{
    public function publish(Post $post, SocialAccount $account): PublishResult
    {
        try {
            $video = $post->firstVideo();

            if (! $video instanceof PostMedia) {
                throw new RuntimeException('YouTube Shorts публикует только видео.');
            }

            if (! filled($account->access_token)) {
                throw new RuntimeException('YouTube-канал не подключён через OAuth.');
            }

            $title = str($post->caption ?: 'Shorts')->limit(90)->toString();

            if (! str_contains(mb_strtolower($title), '#shorts')) {
                $title = trim($title.' #Shorts');
            }

            $metadata = [
                'snippet' => [
                    'title' => $title,
                    'description' => (string) $post->caption,
                    'categoryId' => '22',
                ],
                'status' => [
                    'privacyStatus' => data_get($account->meta, 'privacy_status', 'private'),
                    'selfDeclaredMadeForKids' => false,
                ],
            ];

            $init = Http::withToken((string) $account->access_token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withQueryParameters([
                    'uploadType' => 'resumable',
                    'part' => 'snippet,status',
                ])
                ->timeout(60)
                ->post('https://www.googleapis.com/upload/youtube/v3/videos', $metadata);

            if ($init->failed()) {
                throw new RuntimeException($init->json('error.message') ?: 'YouTube отклонил инициализацию загрузки.');
            }

            $uploadUrl = $init->header('Location');

            if (! filled($uploadUrl)) {
                throw new RuntimeException('YouTube не отдал URL для загрузки видео.');
            }

            $uploaded = Http::withToken((string) $account->access_token)
                ->withHeaders([
                    'Content-Type' => $video->mime_type,
                    'Content-Length' => (string) filesize($video->absolutePath()),
                ])
                ->withBody((string) file_get_contents($video->absolutePath()), $video->mime_type)
                ->timeout(300)
                ->put($uploadUrl)
                ->throw()
                ->json();

            $videoId = data_get($uploaded, 'id');

            if (! is_string($videoId) || $videoId === '') {
                throw new RuntimeException('YouTube не вернул id ролика.');
            }

            return PublishResult::ok($videoId, 'https://www.youtube.com/shorts/'.$videoId);
        } catch (Throwable $exception) {
            return PublishResult::failed($exception->getMessage());
        }
    }
}
