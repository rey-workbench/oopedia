<?php

namespace App\Schemas;

/**
 * FROZEN SCHEMA – StudentState flat column defaults & constants.
 * All fields map directly to `student_states` table columns.
 * No nested JSON blobs. Arrays (badges, unlocked_modules, certifications,
 * time_distribution) remain JSON columns but are the only exceptions.
 */
final class StudentStateSchema
{
    // ─── Gamification data keys ────────────────────────────────────────────────
    public const string KEY_GLOBAL_XP = 'xp';

    public const string KEY_CURRENT_LEVEL = 'level';

    public const string KEY_CURRENT_STREAK = 'streak';

    public const string KEY_MAX_STREAK = 'max_streak';

    public const string KEY_BADGES = 'badges';

    public const string KEY_CERTIFICATIONS = 'certifications';

    // ─── Performance metrics keys ─────────────────────────────────────────
    public const string KEY_TOTAL_QUESTIONS_ANSWERED = 'total_answered';

    public const string KEY_CORRECT_COUNT = 'correct_count';

    public const string KEY_WRONG_COUNT = 'wrong_count';

    public const string KEY_AVG_TIME = 'avg_time';

    public const string KEY_AVG_ACCURACY = 'accuracy';

    // ─── Adaptive state keys ─────────────────────────────────────────
    public const string KEY_CURRENT_DIFFICULTY = 'current_difficulty';

    public const string KEY_CONSECUTIVE_CORRECT = 'consecutive_correct';

    public const string KEY_WRONG_STREAK = 'wrong_streak';

    // ─── Learning profile keys ───────────────────────────────────────────
    public const string KEY_LEARNING_STYLE = 'learning_style';

    public const string KEY_WEAKNESSES = 'weaknesses';

    // ─── Hints keys ───────────────────────────────────────────────
    public const string KEY_HINTS_USED_COUNT = 'hints_used';

    public const string KEY_HINTS_AVAILABLE = 'hints_available';

    // ─── Other keys ─────────────────────────────────────────────────
    public const string KEY_UNLOCKED_MODULES = 'unlocked_modules';

    public const string KEY_TIME_DISTRIBUTION = 'time_distribution';

    public const string KEY_CURRENT_MATERIAL_ID = 'current_material_id';

    public const string KEY_TARGET_DIFFICULTY = 'target_difficulty';

    // ─── Level names ───────────────────────────────────────────────────────────
    public const string LEVEL_PEMULA   = 'Pemula';

    public const string LEVEL_JUNIOR   = 'Junior';

    public const string LEVEL_MENENGAH = 'Menengah';

    public const string LEVEL_AHLI     = 'Ahli';

    public const string LEVEL_MASTER   = 'Master';

    public const array LEVEL_THRESHOLDS = [
        ['name' => self::LEVEL_PEMULA,   'min' => 0],
        ['name' => self::LEVEL_JUNIOR,   'min' => 100],
        ['name' => self::LEVEL_MENENGAH, 'min' => 250],
        ['name' => self::LEVEL_AHLI,     'min' => 500],
        ['name' => self::LEVEL_MASTER,   'min' => 1000],
    ];

    // ─── Learning style names ───────────────────────────────────────────────────
    public const string STYLE_VISUAL  = 'visual';

    public const string STYLE_TEXTUAL = 'textual';

    public const string STYLE_MIXED   = 'mixed';

    // ─── Score band thresholds ──────────────────────────────────────────────────
    public const int FACT_SCORE_CRITICAL_MAX = 40;

    public const int FACT_SCORE_REMEDIAL_MAX = 70;

    public const int FACT_SCORE_STANDARD_MAX = 90;

    // ─── Score bounds ───────────────────────────────────────────────────────────
    public const int SCORE_MIN_CORRECT = 70;

    public const int SCORE_MAX_WRONG   = 69;

    // ─── XP rewards ────────────────────────────────────────────────────────────
    public const int XP_REWARD_BEGINNER = 10;

    public const int XP_REWARD_MEDIUM   = 20;

    public const int XP_REWARD_HARD     = 30;

    public const int XP_REWARD_FINAL    = 50;

    public const int XP_BONUS_FAST      = 5;

    public const int XP_PENALTY_HINT    = 5;

    // ─── Score rewards ──────────────────────────────────────────────────────────
    public const int SCORE_BASE_REWARD  = 80;

    public const int SCORE_BONUS_HARD   = 10;

    public const int SCORE_BONUS_MEDIUM = 5;

    public const int SCORE_BONUS_FINAL  = 20;

    public const int SCORE_BONUS_TIME   = 10;

    public const int SCORE_PENALTY_HINT = 20;

    public const array SCORE_REWARDS = [
        'base'             => self::SCORE_BASE_REWARD,
        'difficulty_bonus' => [
            'hard'   => self::SCORE_BONUS_HARD,
            'medium' => self::SCORE_BONUS_MEDIUM,
            'final'  => self::SCORE_BONUS_FINAL,
        ],
        'time_bonus'   => self::SCORE_BONUS_TIME,
        'hint_penalty' => self::SCORE_PENALTY_HINT,
    ];

    // ─── Streaks / hints defaults ───────────────────────────────────────────────
    public const int DEFAULT_HINTS_AVAILABLE   = 3;

    public const array STREAK_XP_BONUSES = [
        10 => 20,
        5  => 10,
        3  => 5,
    ];

    public const int STREAK_HINT_THRESHOLD = 5;

    // ─── Behavioural thresholds ─────────────────────────────────────────────────
    public const int THRESHOLD_FAST_TIME_MINS         = 15;

    public const int THRESHOLD_FAST_ACCURACY_PCT      = 90;

    public const int THRESHOLD_FATIGUE_TIME_MINS      = 30;

    public const int THRESHOLD_FATIGUE_WRONG_STREAK   = 2;

    public const int THRESHOLD_FATIGUE_ACCURACY_PCT   = 70;

    public const float RATIO_STYLE_MIXED              = 0.20;

    public const int THRESHOLD_PERSISTENT_FAIL        = 2;

    public const int THRESHOLD_SATISFACTORY_PROGRESS  = 61;

    // ─── Column defaults (used when creating new StudentState rows) ─────────────
    public static function defaults(): array
    {
        return [
            'xp'                  => 0,
            'level'               => self::LEVEL_PEMULA,
            'streak'              => 0,
            'max_streak'          => 0,
            'badges'              => [],
            'learning_style'      => self::STYLE_VISUAL,
            'unlocked_modules'    => ['1'],
            'certifications'      => [],
            'time_distribution'   => [],
            'total_answered'      => 0,
            'correct_count'       => 0,
            'wrong_count'         => 0,
            'wrong_streak'        => 0,
            'hints_used'          => 0,
            'hints_available'     => self::DEFAULT_HINTS_AVAILABLE,
            'current_material_id' => null,
            'target_difficulty'   => null,
        ];
    }
}
