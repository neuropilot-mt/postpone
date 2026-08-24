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

class TikTokPublisher implements SocialPublisher
{
    public function publish(Post $post, SocialAccount $account): PublishResult
    {
        try {
            $video = $post->firstVideo();

            if (! $video instanceof PostMedia) {
                throw new RuntimeException('TikTok публикует только видео.');
            }

            if (! filled($account->access_token)) {
                throw new RuntimeException('TikTok-аккаунт не подключён через OAuth.');
            }

            $size = filesize($video->absolutePath());

            if ($size === false) {
                throw new RuntimeException('Не удалось прочитать файл видео.');
            }

            $init = Http::withToken((string) $account->access_token)
                ->timeout(60)
                ->post('https://open.tiktokapis.com/v2/post/publish/video/init/', [
                    'post_info' => [
                        'title' => str($post->caption ?: 'Short video')->limit(150)->toString(),
                        'privacy_level' => data_get($account->meta, 'privacy_level', 'SELF_ONLY'),
                        'disable_duet' => false,
                        'disable_comment' => false,
                        'disable_stitch' => false,
                    ],
                    'source_info' => [
                        'source' => 'FILE_UPLOAD',
                        'video_size' => $size,
                        'chunk_size' => $size,
                        'total_chunk_count' => 1,
                    ],
                ])
                ->throw()
                ->json();

            $publishId = data_get($init, 'data.publish_id');
            $uploadUrl = data_get($init, 'data.upload_url');

            if (! is_string($publishId) || ! is_string($uploadUrl)) {
                throw new RuntimeException((string) data_get($init, 'error.message', 'TikTok не начал загрузку видео.'));
            }

            Http::withHeaders([
                'Content-Type' => $video->mime_type,
                'Content-Length' => (string) $size,
                'Content-Range' => 'bytes 0-'.($size - 1).'/'.$size,
            ])
                ->withBody((string) file_get_contents($video->absolutePath()), $video->mime_type)
                ->timeout(300)
                ->put($uploadUrl)
                ->throw();

            return PublishResult::ok($publishId, 'https://www.tiktok.com/');
        } catch (Throwable $exception) {
            return PublishResult::failed($exception->getMessage());
        }
    }
}
