<?php

namespace App\Enums;

enum TargetStatus: string
{
    case Pending = 'pending';
    case Publishing = 'publishing';
    case Published = 'published';
    case Failed = 'failed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает',
            self::Publishing => 'Публикуется',
            self::Published => 'Опубликован',
            self::Failed => 'Ошибка',
            self::Skipped => 'Пропущен',
        };
    }
}
