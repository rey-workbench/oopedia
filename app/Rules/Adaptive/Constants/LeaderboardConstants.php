<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class LeaderboardConstants
{
    public const int BASE_POINTS_BEGINNER = 5;

    public const int BASE_POINTS_MEDIUM = 10;

    public const int BASE_POINTS_HARD = 15;

    public const float MULTIPLIER_FIRST_ATTEMPT = 1.0;

    public const float MULTIPLIER_SECOND_ATTEMPT = 0.8;

    public const float MULTIPLIER_THIRD_ATTEMPT = 0.6;

    public const float MULTIPLIER_FOURTH_ATTEMPT = 0.4;

    public const float MULTIPLIER_DEFAULT_ATTEMPT = 0.2;
}
