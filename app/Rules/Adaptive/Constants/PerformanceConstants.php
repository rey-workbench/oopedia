<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class PerformanceConstants
{
    public const int BASE_SCORE = 10;

    public const float MEDIUM_MULTIPLIER = 1.5;

    public const float HARD_MULTIPLIER = 2.0;

    public const float DEFAULT_MULTIPLIER = 1.0;

    public const int HINT_PENALTY = 2;

    public const int TIME_PENALTY = 1;

    public const int MIN_SCORE = 5;

    public const int SESSION_BUFFER_SIZE = 5;

    public const int MAX_SESSION_HISTORY = 5;

    public const int TREND_ANALYSIS_WINDOW = 3;

    public const int GUIDANCE_THRESHOLD = 3;

    public const int CHALLENGE_XP_REWARD = 100;

    public const int STREAK_BONUS_XP = 50;

    public const int FORCED_EASY_COUNT = 5;
}
