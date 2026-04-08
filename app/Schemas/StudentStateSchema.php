<?php

namespace App\Schemas;

use App\Models\Question;

/**
 * Standardized JSON schema for StudentState model.
 * Ensures consistent key names across the entire application.
 */
final class StudentStateSchema
{
    public const string LEVEL_PEMULA   = 'Pemula';

    public const string LEVEL_JUNIOR   = 'Junior';

    public const string LEVEL_MENENGAH = 'Menengah';

    public const string LEVEL_AHLI     = 'Ahli';

    public const string LEVEL_MASTER   = 'Master';

    public const array GAMIFICATION = [
        'global_xp'      => 0,
        'current_level'  => self::LEVEL_PEMULA,
        'current_streak' => 0,
        'max_streak'     => 0,
        'badges'         => [],
    ];

    public const string KEY_GLOBAL_XP      = 'global_xp';

    public const string KEY_CURRENT_LEVEL  = 'current_level';

    public const string KEY_CURRENT_STREAK = 'current_streak';

    public const string KEY_MAX_STREAK     = 'max_streak';

    public const array LEVEL_THRESHOLDS = [
        ['name' => self::LEVEL_PEMULA,   'min' => 0],
        ['name' => self::LEVEL_JUNIOR,   'min' => 100],
        ['name' => self::LEVEL_MENENGAH, 'min' => 250],
        ['name' => self::LEVEL_AHLI,     'min' => 500],
        ['name' => self::LEVEL_MASTER,   'min' => 1000],
    ];

    public const array LEVELS = [
        self::LEVEL_PEMULA,
        self::LEVEL_JUNIOR,
        self::LEVEL_MENENGAH,
        self::LEVEL_AHLI,
        self::LEVEL_MASTER,
    ];

    public const string STYLE_VISUAL  = 'visual';

    public const string STYLE_TEXTUAL = 'textual';

    public const string STYLE_MIXED   = 'mixed';

    public const array LEARNING_PROFILE = [
        'learning_style'   => self::STYLE_VISUAL,
        'unlocked_modules' => ['1'], // module_id '1' = "Pengantar Konsep Dasar OOP" (first module always unlocked)
        'certifications'   => [],
    ];

    public const string KEY_LEARNING_STYLE   = 'learning_style';

    public const string KEY_UNLOCKED_MODULES = 'unlocked_modules';

    public const string KEY_TIME_DISTRIBUTION = 'time_distribution';

    public const array LEARNING_STYLES = [
        self::STYLE_VISUAL,
        self::STYLE_TEXTUAL,
        self::STYLE_MIXED,
    ];

    public const array PERFORMANCE_METRICS = [
        'total_questions_answered' => 0,
        'correct_count'            => 0,
        'wrong_count'              => 0,
        'wrong_streak'             => 0,
        'hints_used_count'         => 0,
        'hints_available'          => 3,
    ];

    public const int FACT_SCORE_CRITICAL_MAX = 40;  // < 40

    public const int FACT_SCORE_REMEDIAL_MAX = 70;  // 40-69

    public const int FACT_SCORE_STANDARD_MAX = 90;  // 70-89

    public const string KEY_TOTAL_QUESTIONS_ANSWERED = 'total_questions_answered';

    public const string KEY_CORRECT_COUNT            = 'correct_count';

    public const string KEY_WRONG_COUNT              = 'wrong_count';

    public const string KEY_WRONG_STREAK             = 'wrong_streak';

    public const string KEY_HINTS_USED_COUNT         = 'hints_used_count';

    public const string KEY_HINTS_AVAILABLE          = 'hints_available';

    public const int DEFAULT_HINTS_AVAILABLE = 3;

    public const array ADAPTIVE_STATE = [
        'fast_track_active'    => false,
        'current_material_id'  => null,
        'target_difficulty'    => null,
        'module_progress'      => [],
        'time_metrics'         => [
            'avg_time_per_question'  => 0,
            'total_time_spent'       => 0,
        ],
    ];

    public const array DIFFICULTIES = [
        Question::DIFFICULTY_BEGINNER,
        Question::DIFFICULTY_MEDIUM,
        Question::DIFFICULTY_HARD,
        Question::DIFFICULTY_FINAL,
    ];

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

    public const int XP_REWARD_BEGINNER = 10;

    public const int XP_REWARD_MEDIUM   = 20;

    public const int XP_REWARD_HARD     = 30;

    public const int XP_REWARD_FINAL    = 50;

    public const int XP_BONUS_FAST      = 5;

    public const int XP_PENALTY_HINT    = 5;

    public const array XP_REWARDS = [
        'base' => [
            Question::DIFFICULTY_BEGINNER => self::XP_REWARD_BEGINNER,
            Question::DIFFICULTY_MEDIUM   => self::XP_REWARD_MEDIUM,
            Question::DIFFICULTY_HARD     => self::XP_REWARD_HARD,
            Question::DIFFICULTY_FINAL    => self::XP_REWARD_FINAL,
        ],
        'fast_bonus'   => self::XP_BONUS_FAST,
        'hint_penalty' => self::XP_PENALTY_HINT,
    ];

    public const int SCORE_BASE_REWARD  = 80;

    public const int SCORE_BONUS_HARD   = 10;

    public const int SCORE_BONUS_MEDIUM = 5;

    public const int SCORE_BONUS_FINAL  = 20;

    public const int SCORE_BONUS_TIME   = 10;

    public const int SCORE_PENALTY_HINT = 20;

    public const array SCORE_REWARDS = [
        'base'             => self::SCORE_BASE_REWARD,
        'difficulty_bonus' => [
            Question::DIFFICULTY_HARD   => self::SCORE_BONUS_HARD,
            Question::DIFFICULTY_MEDIUM => self::SCORE_BONUS_MEDIUM,
            Question::DIFFICULTY_FINAL  => self::SCORE_BONUS_FINAL,
        ],
        'time_bonus'   => self::SCORE_BONUS_TIME,
        'hint_penalty' => self::SCORE_PENALTY_HINT,
    ];

    public const int THRESHOLD_FAST_TIME_MINS         = 15;

    public const int THRESHOLD_FAST_ACCURACY_PCT      = 90;

    public const int THRESHOLD_FATIGUE_TIME_MINS      = 30;

    public const int THRESHOLD_FATIGUE_WRONG_STREAK   = 2;

    public const int THRESHOLD_FATIGUE_ACCURACY_PCT   = 70;

    public const float RATIO_STYLE_MIXED              = 0.20;

    public const int THRESHOLD_PERSISTENT_FAIL        = 2;

    public const int THRESHOLD_SATISFACTORY_PROGRESS  = 50;

    public const array BEHAVIOURAL_THRESHOLDS = [
        'fast_learner' => [
            'max_avg_time_mins' => self::THRESHOLD_FAST_TIME_MINS,
            'min_accuracy_pct'  => self::THRESHOLD_FAST_ACCURACY_PCT,
        ],
        'fatigue' => [
            'min_time_mins'      => self::THRESHOLD_FATIGUE_TIME_MINS,
            'min_wrong_streak'   => self::THRESHOLD_FATIGUE_WRONG_STREAK,
            'max_accuracy_pct'   => self::THRESHOLD_FATIGUE_ACCURACY_PCT,
        ],
        'style_mixed_ratio'         => self::RATIO_STYLE_MIXED,
        'persistent_fail'           => self::THRESHOLD_PERSISTENT_FAIL,
        'satisfactory_progress_pct' => self::THRESHOLD_SATISFACTORY_PROGRESS,
    ];

    public const float WEIGHT_PROGRESS_BEGINNER = 1.0;

    public const float WEIGHT_PROGRESS_MEDIUM   = 1.5;

    public const float WEIGHT_PROGRESS_HARD     = 2.0;

    public const int BONUS_REACHING_HARD_BASE      = 10;

    public const int BONUS_HARD_QUESTION_ANSWERED  = 5;

    public const int BONUS_MAX_HARD_PROGRESSION    = 30;

    public const int BONUS_REACHING_MEDIUM_STREAK  = 10;

    public const int THRESHOLD_MEDIUM_REACHED_COUNT = 3;

    public const int SCORE_MIN_CORRECT = 70;

    public const int SCORE_MAX_WRONG   = 69;

    public const array STREAK_XP_BONUSES = [
        10 => 20,
        5  => 10,
        3  => 5,
    ];

    public const int STREAK_HINT_THRESHOLD = 5;
}
