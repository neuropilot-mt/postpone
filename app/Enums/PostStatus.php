<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Publishing = 'publishing';
    case Published = 'published';
    case Partial = 'partial';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Черновик',
            self::Scheduled => 'Запланирован',
            self::Publishing => 'Публикуется',
            self::Published => 'Опубликован',
            self::Partial => 'Частично',
            self::Failed => 'Ошибка',
        };
    }
}
