<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum ContentCategory: string
{
    case TEORI   = 'teori';
    case SINTAKS = 'sintaks';
    case MIXED   = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::TEORI   => 'Teori',
            self::SINTAKS => 'Sintaks',
            self::MIXED   => 'Mixed',
        };
    }
}
