<?php

namespace App\Social;

use App\Enums\Platform;
use App\Models\Post;
use App\Models\SocialAccount;
use App\Social\Contracts\SocialPublisher;
use App\Social\Publishers\InstagramPublisher;
use App\Social\Publishers\SandboxPublisher;
use App\Social\Publishers\TikTokPublisher;
use App\Social\Publishers\VkPublisher;
use App\Social\Publishers\YouTubePublisher;

class SocialPublisherManager
{
    public function publish(Post $post, SocialAccount $account): PublishResult
    {
        return $this->publisherFor($account)->publish($post, $account);
    }

    public function publisherFor(SocialAccount $account): SocialPublisher
    {
        if (! $account->usesLiveApi()) {
            return app(SandboxPublisher::class);
        }

        return match ($account->platform) {
            Platform::Instagram => app(InstagramPublisher::class),
            Platform::Vk => app(VkPublisher::class),
            Platform::TikTok => app(TikTokPublisher::class),
            Platform::YouTube => app(YouTubePublisher::class),
        };
    }

    public function oauthConfigured(Platform $platform): bool
    {
        return match ($platform) {
            Platform::Instagram => filled(config('social.instagram.client_id')) && filled(config('social.instagram.client_secret')),
            Platform::Vk => filled(config('social.vk.client_id')) && filled(config('social.vk.client_secret')),
            Platform::TikTok => filled(config('social.tiktok.client_key')) && filled(config('social.tiktok.client_secret')),
            Platform::YouTube => filled(config('social.youtube.client_id')) && filled(config('social.youtube.client_secret')),
        };
    }
}
