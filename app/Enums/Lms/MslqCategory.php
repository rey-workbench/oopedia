<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum MslqCategory: string
{
    case MOTIVATION        = 'motivation';
    case LEARNING_STRATEGY = 'learning_strategy';

    public function label(): string
    {
        return match ($this) {
            self::MOTIVATION        => 'Motivasi',
            self::LEARNING_STRATEGY => 'Strategi Belajar',
        };
    }
}
