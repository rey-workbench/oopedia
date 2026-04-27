<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

use App\Enums\Lms\StudentLevel;

final class PedagogicalConstants
{
    // ─── Level Thresholds ──────────────────────────────────────────────────────
    public const array LEVEL_THRESHOLDS = [
        ['name' => StudentLevel::PEMULA->value, 'min' => 0],
        ['name' => StudentLevel::MENENGAH->value, 'min' => 250],
        ['name' => StudentLevel::AHLI->value, 'min' => 500],
    ];

    // ─── Pedagogical Thresholds ──────────────────────────────────────────────
    public const int THRESHOLD_MASTERY_ACCURACY = 70;
    public const int TIME_QUICK_THRESHOLD = 30; // seconds

    // ─── XP & Score Rewards ──────────────────────────────────────────────────
    public const int XP_REWARD_BASE = 10;
    public const int SCORE_BASE_REWARD = 100;

    public const int XP_REWARD_BEGINNER = 10;
    public const int XP_REWARD_MEDIUM = 20;
    public const int XP_REWARD_HARD = 30;
    public const int XP_PENALTY_HINT = 5;

    public const array STREAK_XP_BONUSES = [
        10 => 100,
        5 => 50,
        3 => 20,
    ];

    public const array SCORE_REWARDS = [
        'base' => 60,
        'difficulty_bonus' => [
            QuestionDifficulty::BEGINNER->value => 0,
            QuestionDifficulty::MEDIUM->value => 15,
            QuestionDifficulty::HARD->value => 30,
        ],
        'time_bonus' => 10,
        'hint_penalty' => 5,
    ];

    // ─── Style & Allocation ──────────────────────────────────────────────────
    public const array ALLOCATED_TIME = [
        QuestionDifficulty::BEGINNER->value => 60,
        QuestionDifficulty::MEDIUM->value => 120,
        QuestionDifficulty::HARD->value => 180,
    ];

    public const float RATIO_STYLE_MIXED = 0.2;
    public const string STYLE_MIXED = 'mixed';
}
