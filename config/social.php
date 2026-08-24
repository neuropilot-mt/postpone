<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sandbox publishing
    |--------------------------------------------------------------------------
    |
    | When enabled, posts are marked as published without calling real APIs.
    | Use this until Instagram / VK / TikTok / YouTube apps are configured.
    |
    */

    'sandbox' => env('SOCIAL_SANDBOX', true),

    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
        'graph_version' => env('INSTAGRAM_GRAPH_VERSION', 'v21.0'),
        'redirect' => env('INSTAGRAM_REDIRECT_URI'),
        'scopes' => [
            'instagram_basic',
            'instagram_content_publish',
            'pages_show_list',
            'pages_read_engagement',
            'business_management',
        ],
    ],

    'vk' => [
        'client_id' => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect' => env('VK_REDIRECT_URI'),
        'api_version' => env('VK_API_VERSION', '5.199'),
        'scopes' => ['wall', 'photos', 'video', 'offline'],
    ],

    'tiktok' => [
        'client_key' => env('TIKTOK_CLIENT_KEY'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
        'redirect' => env('TIKTOK_REDIRECT_URI'),
        'scopes' => ['user.info.basic', 'video.upload', 'video.publish'],
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'redirect' => env('YOUTUBE_REDIRECT_URI'),
        'scopes' => [
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube.readonly',
        ],
    ],

];
