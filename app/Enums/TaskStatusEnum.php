<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatusEnum: string
{
    case DONE = 'Done';
    case TO_DO = 'ToDo';

    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
