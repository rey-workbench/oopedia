<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum QuestionDifficulty: string
{
    case BEGINNER = 'beginner';
    case MEDIUM   = 'medium';
    case HARD     = 'hard';
    case FINAL    = 'final'; // Added for final project questions/materials

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => 'Beginner',
            self::MEDIUM   => 'Medium',
            self::HARD     => 'Hard',
            self::FINAL    => 'Final Project',
        };
    }
}
