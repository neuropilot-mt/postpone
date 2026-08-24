export type PlatformValue = 'instagram' | 'vk' | 'tiktok' | 'youtube';

export type SocialAccount = {
    id: number;
    platform: PlatformValue;
    platform_label?: string;
    name: string;
    is_sandbox?: boolean;
    is_active?: boolean;
    created_at?: string;
};

export type PostMediaItem = {
    id: number;
    type: 'photo' | 'video';
    url: string;
    original_name: string;
};

export type PostTargetItem = {
    id: number;
    platform: PlatformValue;
    platform_label: string;
    account_name: string | null;
    status: string;
    status_label: string;
    external_url: string | null;
    error_message: string | null;
};

export type PostItem = {
    id: number;
    caption: string | null;
    status: string;
    status_label: string;
    instagram_format: 'post' | 'story' | null;
    scheduled_at: string | null;
    scheduled_at_iso?: string | null;
    published_at?: string | null;
    created_at?: string | null;
    media?: PostMediaItem[];
    targets?: PostTargetItem[];
    platforms?: PlatformValue[] | string[];
    has_video?: boolean;
    has_photo?: boolean;
    author?: string | null;
};

export const platformMeta: Record<
    PlatformValue,
    { label: string; hint: string; color: string }
> = {
    instagram: {
        label: 'Instagram',
        hint: 'Пост, Reels или сторис',
        color: 'bg-gradient-to-br from-purple-500 to-pink-500',
    },
    vk: {
        label: 'ВКонтакте',
        hint: 'Текст, фото и видео',
        color: 'bg-[#0077FF]',
    },
    tiktok: {
        label: 'TikTok',
        hint: 'Только видео',
        color: 'bg-neutral-950',
    },
    youtube: {
        label: 'YouTube Shorts',
        hint: 'Только видео',
        color: 'bg-[#FF0000]',
    },
};
