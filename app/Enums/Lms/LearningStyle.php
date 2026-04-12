<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum LearningStyle: string
{
    case VISUAL  = 'visual';
    case TEXTUAL = 'textual';
    case MIXED   = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::VISUAL  => 'Visual',
            self::TEXTUAL => 'Textual',
            self::MIXED   => 'Mixed',
        };
    }
}
