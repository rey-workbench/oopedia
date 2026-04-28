<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

use App\Enums\Lms\LearningStyle;
use App\Enums\Lms\StudentLevel;

final class StudentStateSchema
{
    public const int DEFAULT_HINTS_AVAILABLE = 3;

    // ─── Database Keys (Flat mapping to Columns) ─────────────────────
    public const string GLOBAL_XP = 'xp';

    public const string CURRENT_LEVEL = 'level';

    public const string CURRENT_STREAK = 'streak';

    public const string MAX_STREAK = 'max_streak';

    public const string BADGES = 'badges';

    public const string CERTIFICATIONS = 'certifications';

    public const string TOTAL_QUESTIONS_ANSWERED = 'total_answered';

    public const string CORRECT_COUNT = 'correct_count';

    public const string WRONG_COUNT = 'wrong_count';

    public const string AVG_ACCURACY = 'accuracy';

    public const string CONSECUTIVE_CORRECT = 'consecutive_correct';

    public const string WRONG_STREAK = 'wrong_streak';

    public const string LEARNING_STYLE = 'learning_style';

    public const string HINTS_USED_COUNT = 'hints_used';

    public const string HINTS_AVAILABLE = 'hints_available';

    public const string UNLOCKED_MODULES = 'unlocked_modules';

    public const string TIME_DISTRIBUTION = 'time_distribution';

    public const string CURRENT_MATERIAL_ID = 'current_material_id';

    public const string TARGET_DIFFICULTY = 'target_difficulty';

    /**
     * Default values for a new StudentState.
     */
    public static function defaults(): array
    {
        return [
            self::GLOBAL_XP                => 0,
            self::CURRENT_LEVEL            => StudentLevel::PEMULA->value,
            self::CURRENT_STREAK           => 0,
            self::MAX_STREAK               => 0,
            self::BADGES                   => [],
            self::LEARNING_STYLE           => LearningStyle::TEXTUAL->value,
            self::UNLOCKED_MODULES         => ['1'],
            self::CERTIFICATIONS           => [],
            self::TIME_DISTRIBUTION        => [],
            self::TOTAL_QUESTIONS_ANSWERED => 0,
            self::CORRECT_COUNT            => 0,
            self::WRONG_COUNT              => 0,
            self::WRONG_STREAK             => 0,
            self::HINTS_USED_COUNT         => 0,
            self::HINTS_AVAILABLE          => 3,
            self::CURRENT_MATERIAL_ID      => null,
            self::TARGET_DIFFICULTY        => null,
        ];
    }
}
