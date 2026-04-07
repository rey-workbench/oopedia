<?php

namespace App\Schemas;

/**
 * Standardized JSON schema for StudentState model.
 * Ensures consistent key names across the entire application.
 */
final class StudentStateSchema
{
    // ==================== GAMIFICATION DATA ====================
    public const GAMIFICATION = [
        'global_xp'      => 0,
        'current_level'  => 'Pemula',
        'current_streak' => 0,
        'max_streak'     => 0,
        'badges'         => [],
    ];

    public const KEY_GLOBAL_XP      = 'global_xp';

    public const KEY_CURRENT_LEVEL  = 'current_level';

    public const KEY_CURRENT_STREAK = 'current_streak';

    public const KEY_MAX_STREAK     = 'max_streak';

    public const KEY_BADGES         = 'badges';

    public const LEVELS = [
        'Pemula',
        'Junior',
        'Menengah',
        'Ahli',
        'Master',
    ];

    // ==================== LEARNING PROFILE ====================
    // Note: unlocked_modules contains module_id values from materials table
    // module_id is INTEGER in DB, but Eloquent casts to string ('1', '2', etc.)
    // NOT ULID - only materials.id uses ULID
    public const LEARNING_PROFILE = [
        'learning_style'   => 'visual',
        'unlocked_modules' => ['1'], // module_id '1' = "Pengantar Konsep Dasar OOP" (first module always unlocked)
        'certifications'   => [],
    ];

    public const KEY_LEARNING_STYLE   = 'learning_style';

    public const KEY_UNLOCKED_MODULES = 'unlocked_modules';

    public const KEY_CERTIFICATIONS   = 'certifications';

    public const LEARNING_STYLES = [
        'visual',
        'textual',
        'mixed',
    ];

    // ==================== PERFORMANCE METRICS ====================
    public const PERFORMANCE_METRICS = [
        'total_questions_answered' => 0,
        'correct_count'            => 0,
        'wrong_count'              => 0,
        'wrong_streak'             => 0,
        'hints_used_count'         => 0,
        'hints_available'          => 3,
    ];

    public const KEY_TOTAL_QUESTIONS_ANSWERED = 'total_questions_answered';

    public const KEY_CORRECT_COUNT            = 'correct_count';

    public const KEY_WRONG_COUNT              = 'wrong_count';

    public const KEY_WRONG_STREAK             = 'wrong_streak';

    public const KEY_HINTS_USED_COUNT         = 'hints_used_count';

    public const KEY_HINTS_AVAILABLE          = 'hints_available';

    public const DEFAULT_HINTS_AVAILABLE = 3;

    // ==================== ADAPTIVE STATE ====================
    public const ADAPTIVE_STATE = [
        'fast_track_active'    => false,
        'current_material_id'  => null,
        'target_difficulty'    => null,
        'module_progress'      => [],
        'time_metrics'         => [
            'avg_time_per_question'  => 0,
            'total_time_spent'       => 0,
        ],
    ];

    public const KEY_FAST_TRACK_ACTIVE   = 'fast_track_active';

    public const KEY_CURRENT_MATERIAL_ID = 'current_material_id';

    public const KEY_TARGET_DIFFICULTY   = 'target_difficulty';

    public const KEY_MODULE_PROGRESS    = 'module_progress';

    public const KEY_TIME_METRICS       = 'time_metrics';

    public const KEY_AVG_TIME_PER_QUESTION = 'avg_time_per_question';

    public const KEY_TOTAL_TIME_SPENT      = 'total_time_spent';

    public const DIFFICULTIES = [
        'beginner',
        'medium',
        'hard',
        'final',
    ];

    // ==================== DEFAULT VALUES ====================

    public static function getDefaultGamification(): array
    {
        return self::GAMIFICATION;
    }

    public static function getDefaultLearningProfile(): array
    {
        return self::LEARNING_PROFILE;
    }

    public static function getDefaultPerformanceMetrics(): array
    {
        return self::PERFORMANCE_METRICS;
    }

    public static function getDefaultAdaptiveState(): array
    {
        return self::ADAPTIVE_STATE;
    }

    public static function getAllDefaults(): array
    {
        return [
            'gamification_data'   => self::GAMIFICATION,
            'learning_profile'    => self::LEARNING_PROFILE,
            'performance_metrics' => self::PERFORMANCE_METRICS,
            'adaptive_state'      => self::ADAPTIVE_STATE,
        ];
    }

    // ==================== VALIDATION ====================

    public static function isValidLearningStyle(string $style): bool
    {
        return in_array($style, self::LEARNING_STYLES, true);
    }

    public static function isValidDifficulty(string $difficulty): bool
    {
        return in_array($difficulty, self::DIFFICULTIES, true);
    }

    public static function isValidLevel(string $level): bool
    {
        return in_array($level, self::LEVELS, true);
    }
}
