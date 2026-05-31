<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

use App\Enums\Lms\StudentLevel;

final class StudentStateSchema
{
    // Database Column Names
    public const string XP = 'xp';

    public const string LEVEL = 'level';

    public const string STREAK = 'streak';

    public const string MAX_STREAK = 'max_streak';

    public const string TOTAL_ANSWERED = 'total_answered';

    public const string CORRECT_COUNT = 'correct_count';


    public const string ACCURACY = 'accuracy';

    public const string HINTS_USED = 'hints_used';

    public const string HINTS_AVAILABLE = 'hints_available';

    public const string TARGET_DIFFICULTY = 'target_difficulty';

    // JSON Attribute Keys
    public const string SESSION_HISTORY = 'session_history'; // Last 5 sessions accuracy

    public const string CURRENT_SESSION = 'current_session'; // [correct, total, hints, time_spent]

    public const string PERFORMANCE_METRICS = 'performance_metrics'; // [trend, speed, stagnant_count]

    public const string ADAPTIVE_STATE = 'adaptive_state';

    public static function defaults(): array
    {
        return [
            self::XP                => 0,
            self::LEVEL             => StudentLevel::PEMULA->value,
            self::STREAK            => 0,
            self::MAX_STREAK        => 0,
            self::TOTAL_ANSWERED    => 0,
            self::CORRECT_COUNT     => 0,
            self::ACCURACY          => 0.0,
            self::HINTS_USED        => 0,
            self::HINTS_AVAILABLE   => 3,
            self::TARGET_DIFFICULTY => null,
            self::SESSION_HISTORY   => [0.0, 0.0, 0.0],
            self::CURRENT_SESSION   => [
                'correct'      => 0,
                'total'        => 0,
                'hints'        => 0,
                'time_spent'   => 0,
                'question_ids' => [],
            ],
            self::PERFORMANCE_METRICS => [
                'trend'          => 'stable',
                'speed'          => 'normal',
                'stagnant_count' => 0,
            ],
            self::ADAPTIVE_STATE => [],
        ];
    }
}
