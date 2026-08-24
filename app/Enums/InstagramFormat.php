<?php

namespace App\Enums;

enum InstagramFormat: string
{
    case Post = 'post';
    case Story = 'story';

    public function label(): string
    {
        return match ($this) {
            self::Post => 'Пост / Reels',
            self::Story => 'Сторис',
        };
    }
}
