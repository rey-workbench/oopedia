<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

use App\Enums\Lms\QuestionDifficulty;

final class PedagogicalConstants
{
    // ─── Pedagogical Thresholds ──────────────────────────────────────────────
    public const int TIME_QUICK_THRESHOLD = 30; // seconds

    // ─── Score & Rewards (used by PerformanceService) ────────────────────────
    public const array SCORE_REWARDS = [
        'base'             => 60,
        'difficulty_bonus' => [
            QuestionDifficulty::BEGINNER->value => 0,
            QuestionDifficulty::MEDIUM->value   => 15,
            QuestionDifficulty::HARD->value     => 30,
        ],
        'time_bonus'   => 10,
        'hint_penalty' => 5,
    ];

    // ─── Style & Allocation ──────────────────────────────────────────────────
    public const array ALLOCATED_TIME = [
        QuestionDifficulty::BEGINNER->value => 60,
        QuestionDifficulty::MEDIUM->value   => 120,
        QuestionDifficulty::HARD->value     => 180,
    ];

    public const float RATIO_STYLE_MIXED = 0.2;
}
