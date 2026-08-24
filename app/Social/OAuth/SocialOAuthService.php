<?php

namespace App\Social\OAuth;

use App\Enums\Platform;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SocialOAuthService
{
    public function redirectUrl(Platform $platform): string
    {
        $state = Str::random(40);
        session(['oauth_state' => $state, 'oauth_platform' => $platform->value]);

        return match ($platform) {
            Platform::Instagram => $this->facebookRedirect($state),
            Platform::Vk => $this->vkRedirect($state),
            Platform::TikTok => $this->tiktokRedirect($state),
            Platform::YouTube => $this->youtubeRedirect($state),
        };
    }

    public function connect(User $user, Platform $platform, string $code, ?string $state): SocialAccount
    {
        if ($state === null || $state !== session('oauth_state') || session('oauth_platform') !== $platform->value) {
            throw new RuntimeException('Сессия OAuth устарела. Попробуйте подключить аккаунт ещё раз.');
        }

        session()->forget(['oauth_state', 'oauth_platform']);

        return match ($platform) {
            Platform::Instagram => $this->connectInstagram($user, $code),
            Platform::Vk => $this->connectVk($user, $code),
            Platform::TikTok => $this->connectTikTok($user, $code),
            Platform::YouTube => $this->connectYouTube($user, $code),
        };
    }

    public function callbackUrl(Platform $platform): string
    {
        $configured = config('social.'.$this->configKey($platform).'.redirect');

        return filled($configured) ? (string) $configured : route('oauth.callback', $platform->value);
    }

    private function facebookRedirect(string $state): string
    {
        return 'https://www.facebook.com/'.config('social.instagram.graph_version').'/dialog/oauth?'.http_build_query([
            'client_id' => config('social.instagram.client_id'),
            'redirect_uri' => $this->callbackUrl(Platform::Instagram),
            'state' => $state,
            'response_type' => 'code',
            'scope' => implode(',', config('social.instagram.scopes')),
        ]);
    }

    private function vkRedirect(string $state): string
    {
        return 'https://oauth.vk.com/authorize?'.http_build_query([
            'client_id' => config('social.vk.client_id'),
            'redirect_uri' => $this->callbackUrl(Platform::Vk),
            'display' => 'page',
            'scope' => implode(',', config('social.vk.scopes')),
            'response_type' => 'code',
            'state' => $state,
        ]);
    }

    private function tiktokRedirect(string $state): string
    {
        return 'https://www.tiktok.com/v2/auth/authorize/?'.http_build_query([
            'client_key' => config('social.tiktok.client_key'),
            'redirect_uri' => $this->callbackUrl(Platform::TikTok),
            'scope' => implode(',', config('social.tiktok.scopes')),
            'response_type' => 'code',
            'state' => $state,
        ]);
    }

    private function youtubeRedirect(string $state): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('social.youtube.client_id'),
            'redirect_uri' => $this->callbackUrl(Platform::YouTube),
            'response_type' => 'code',
            'scope' => implode(' ', config('social.youtube.scopes')),
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);
    }

    private function connectInstagram(User $user, string $code): SocialAccount
    {
        $version = config('social.instagram.graph_version');

        $token = Http::asForm()->post('https://graph.facebook.com/'.$version.'/oauth/access_token', [
            'client_id' => config('social.instagram.client_id'),
            'client_secret' => config('social.instagram.client_secret'),
            'redirect_uri' => $this->callbackUrl(Platform::Instagram),
            'code' => $code,
        ])->throw()->json();

        $accessToken = data_get($token, 'access_token');

        $pages = Http::get('https://graph.facebook.com/'.$version.'/me/accounts', [
            'access_token' => $accessToken,
            'fields' => 'id,name,access_token,instagram_business_account',
        ])->throw()->json('data');

        foreach ((array) $pages as $page) {
            $igId = data_get($page, 'instagram_business_account.id');

            if (! filled($igId)) {
                continue;
            }

            $profile = Http::get('https://graph.facebook.com/'.$version.'/'.$igId, [
                'fields' => 'id,username,name',
                'access_token' => data_get($page, 'access_token'),
            ])->throw()->json();

            return $this->storeAccount($user, Platform::Instagram, [
                'name' => (string) (data_get($profile, 'username') ?: data_get($profile, 'name') ?: 'Instagram'),
                'external_id' => (string) $igId,
                'access_token' => (string) data_get($page, 'access_token'),
                'meta' => [
                    'page_id' => data_get($page, 'id'),
                    'page_name' => data_get($page, 'name'),
                ],
            ]);
        }

        throw new RuntimeException('Не найден Instagram Professional, привязанный к Facebook Page.');
    }

    private function connectVk(User $user, string $code): SocialAccount
    {
        $token = Http::asForm()->post('https://oauth.vk.com/access_token', [
            'client_id' => config('social.vk.client_id'),
            'client_secret' => config('social.vk.client_secret'),
            'redirect_uri' => $this->callbackUrl(Platform::Vk),
            'code' => $code,
        ])->throw()->json();

        $userId = data_get($token, 'user_id');
        $groups = Http::asForm()->post('https://api.vk.com/method/groups.get', [
            'access_token' => data_get($token, 'access_token'),
            'filter' => 'admin',
            'extended' => 1,
            'v' => config('social.vk.api_version'),
        ])->json('response.items');

        $group = collect((array) $groups)->first();

        return $this->storeAccount($user, Platform::Vk, [
            'name' => (string) (data_get($group, 'name') ?: 'VK '.$userId),
            'external_id' => $group ? '-'.data_get($group, 'id') : (string) $userId,
            'access_token' => (string) data_get($token, 'access_token'),
            'meta' => [
                'user_id' => $userId,
                'group' => $group,
            ],
        ]);
    }

    private function connectTikTok(User $user, string $code): SocialAccount
    {
        $token = Http::asForm()->post('https://open.tiktokapis.com/v2/oauth/token/', [
            'client_key' => config('social.tiktok.client_key'),
            'client_secret' => config('social.tiktok.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->callbackUrl(Platform::TikTok),
        ])->throw()->json();

        $profile = Http::withToken((string) data_get($token, 'access_token'))
            ->get('https://open.tiktokapis.com/v2/user/info/', [
                'fields' => 'open_id,display_name,username',
            ])
            ->throw()
            ->json('data.user');

        return $this->storeAccount($user, Platform::TikTok, [
            'name' => (string) (data_get($profile, 'username') ?: data_get($profile, 'display_name') ?: 'TikTok'),
            'external_id' => (string) data_get($profile, 'open_id'),
            'access_token' => (string) data_get($token, 'access_token'),
            'refresh_token' => data_get($token, 'refresh_token'),
            'token_expires_at' => now()->addSeconds((int) data_get($token, 'expires_in', 86400)),
            'meta' => $profile,
        ]);
    }

    private function connectYouTube(User $user, string $code): SocialAccount
    {
        $token = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('social.youtube.client_id'),
            'client_secret' => config('social.youtube.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->callbackUrl(Platform::YouTube),
        ])->throw()->json();

        $channel = Http::withToken((string) data_get($token, 'access_token'))
            ->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'snippet',
                'mine' => 'true',
            ])
            ->throw()
            ->json('items.0');

        if (! is_array($channel)) {
            throw new RuntimeException('Не найден YouTube-канал у этого Google-аккаунта.');
        }

        return $this->storeAccount($user, Platform::YouTube, [
            'name' => (string) data_get($channel, 'snippet.title', 'YouTube'),
            'external_id' => (string) data_get($channel, 'id'),
            'access_token' => (string) data_get($token, 'access_token'),
            'refresh_token' => data_get($token, 'refresh_token'),
            'token_expires_at' => now()->addSeconds((int) data_get($token, 'expires_in', 3600)),
            'meta' => [
                'channel' => $channel,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function storeAccount(User $user, Platform $platform, array $attributes): SocialAccount
    {
        return SocialAccount::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'platform' => $platform,
                'external_id' => $attributes['external_id'],
            ],
            [
                ...$attributes,
                'is_sandbox' => false,
                'is_active' => true,
            ],
        );
    }

    private function configKey(Platform $platform): string
    {
        return $platform->value;
    }
}
