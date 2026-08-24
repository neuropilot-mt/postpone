<?php

namespace App\Enums;

enum Platform: string
{
    case Instagram = 'instagram';
    case Vk = 'vk';
    case TikTok = 'tiktok';
    case YouTube = 'youtube';

    public function label(): string
    {
        return match ($this) {
            self::Instagram => 'Instagram',
            self::Vk => 'ВКонтакте',
            self::TikTok => 'TikTok',
            self::YouTube => 'YouTube Shorts',
        };
    }

    public function requiresVideo(): bool
    {
        return match ($this) {
            self::TikTok, self::YouTube => true,
            default => false,
        };
    }

    /**
     * @return list<self>
     */
    public static function values(): array
    {
        return self::cases();
    }
}
