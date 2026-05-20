<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum QuestionDifficulty: string
{
    case BEGINNER = 'beginner';
    case MEDIUM   = 'medium';
    case HARD     = 'hard';
    case FINAL    = 'final';
}
