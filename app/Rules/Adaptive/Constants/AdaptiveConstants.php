<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

/**
 * SOURCE OF TRUTH – Konstanta Adaptif & Schema StudentState.
 * Berisi kunci nama fakta/aksi, schema database, dan threshold pedagogis.
 * Unified from StudentStateSchema and legacy AdaptiveConstants.
 */
final class AdaptiveConstants
{
    // ─── Database Keys (StudentState Columns) ─────────────────────────
    public const string KEY_GLOBAL_XP = 'xp';

    public const string KEY_CURRENT_LEVEL = 'level';

    public const string KEY_CURRENT_STREAK = 'streak';

    public const string KEY_MAX_STREAK = 'max_streak';

    public const string KEY_BADGES = 'badges';

    public const string KEY_CERTIFICATIONS = 'certifications';

    public const string KEY_TOTAL_QUESTIONS_ANSWERED = 'total_answered';

    public const string KEY_CORRECT_COUNT = 'correct_count';

    public const string KEY_WRONG_COUNT = 'wrong_count';

    public const string KEY_AVG_ACCURACY = 'accuracy';

    public const string KEY_CONSECUTIVE_CORRECT = 'consecutive_correct';

    public const string KEY_WRONG_STREAK = 'wrong_streak';

    public const string KEY_LEARNING_STYLE = 'learning_style';

    public const string KEY_HINTS_USED_COUNT = 'hints_used';

    public const string KEY_HINTS_AVAILABLE = 'hints_available';

    public const string KEY_UNLOCKED_MODULES = 'unlocked_modules';

    public const string KEY_TIME_DISTRIBUTION = 'time_distribution';

    public const string KEY_CURRENT_MATERIAL_ID = 'current_material_id';

    public const string KEY_TARGET_DIFFICULTY = 'target_difficulty';

    // ── Fact Names (Keys untuk pencarian di DB) ─────────────────────
    public const FACT_SCORE_FAILURE      = 'Score Failure';

    public const FACT_SCORE_PASS         = 'Score Pass';

    public const FACT_SCORE_PERFECT      = 'Score Perfect';

    public const FACT_SCORE_ZERO         = 'Score Zero';

    public const FACT_MASTERY_BEGINNER   = 'Mastery Beginner';

    public const FACT_MASTERY_MEDIUM     = 'Mastery Medium';

    public const FACT_MASTERY_HARD       = 'Mastery Hard';

    public const FACT_CONSISTENCY_HIGH   = 'Consistency High';

    public const FACT_STYLE_VISUAL       = 'Style Visual';

    public const FACT_STYLE_TEXTUAL      = 'Style Textual';

    public const FACT_STYLE_MIXED        = 'Style Mixed';

    public const FACT_ERROR_SYNTAX       = 'Syntax Issue';

    public const FACT_ERROR_LOGIC        = 'Logic Issue';

    public const FACT_ERROR_CONCEPT      = 'Concept Issue';

    public const FACT_NO_ERROR           = 'No Mistake';

    public const FACT_TIME_FAST_SUCCESS  = 'Fast Success';

    public const FACT_TIME_FAST_FAIL     = 'Fast Failure';

    public const FACT_TIME_SLOW_SUCCESS  = 'Slow Success';

    public const FACT_TIME_SLOW_FAIL     = 'Slow Failure';

    public const FACT_HINT_USED          = 'Used Hint';

    public const FACT_BOREDOM_SIGNS      = 'Boredom Signs';

    public const FACT_ANXIETY_SIGNS      = 'Anxiety Signs';

    public const FACT_HIGH_STRUGGLE      = 'High Struggle';

    public const FACT_DIFF_BEGINNER      = 'In Beginner';

    public const FACT_DIFF_MEDIUM        = 'In Medium';

    public const FACT_DIFF_HARD          = 'In Hard';

    public const FACT_IS_FINAL_PROJECT   = 'In Project';

    public const FACT_IN_MODULE          = 'In Module';

    public const FACT_SATISFACTORY_PROGRESS = 'Satisfactory Progress';

    public const FACT_NEXT_UNLOCKED      = 'Next Module Unlocked';

    public const FACT_PREV_UNLOCKED      = 'Previous Module Unlocked';

    public const FACT_PERSISTENT_FAIL    = 'Persistent Fail';

    public const FACT_MODULE_NEARLY_DONE = 'Module Nearly Done';

    public const FACT_MODULE_GRADUATION  = 'Module Graduation';

    // ─── Level Names & Thresholds ──────────────────────────────────────────────
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

    // ─── Learning Styles ──────────────────────────────────────────────────────
    public const string STYLE_VISUAL  = 'visual';

    public const string STYLE_TEXTUAL = 'textual';

    public const string STYLE_MIXED   = 'mixed';

    // ─── Operational Action Labels ───────────────────────────────────────────
    public const ACTION_NEXT_QUESTION      = 'NEXT_QUESTION';

    public const ACTION_NEXT_MATERIAL      = 'NEXT_MATERIAL';

    public const ACTION_FINISH_MATERIAL    = 'FINISH_MATERIAL';

    public const ACTION_ISSUE_CERTIFICATE   = 'ISSUE_CERTIFICATE';

    public const ACTION_REDUCE_DIFFICULTY   = 'REDUCE_DIFFICULTY';

    public const ACTION_INCREASE_DIFFICULTY = 'INCREASE_DIFFICULTY';

    public const ACTION_STUDY_VISUAL       = 'STUDY_VISUAL';

    public const ACTION_STUDY_TEXTUAL      = 'STUDY_TEXTUAL';

    public const ACTION_STUDY_SYNTAX       = 'STUDY_SYNTAX';

    public const ACTION_STUDY_THEORY       = 'STUDY_THEORY';

    public const ACTION_STUDY_MIXED        = 'STUDY_MIXED';

    public const ACTION_STUDY_MATERIAL     = 'STUDY_MATERIAL';

    public const ACTION_REVISE_PROJECT     = 'REVISE_PROJECT';



    // ─── Difficulty Levels ────────────────────────────────────────────────────
    public const DIFFICULTY_BEGINNER = 'beginner';

    public const DIFFICULTY_MEDIUM   = 'medium';

    public const DIFFICULTY_HARD     = 'hard';

    public const DIFFICULTY_FINAL    = 'final';

    // ─── Pedagogical Thresholds (Operational Defaults) ───────────────────────
    public const int THRESHOLD_MASTERY_ACCURACY      = 70;

    public const int THRESHOLD_MASTERY_MIN_ATTEMPTS  = 3;

    public const int THRESHOLD_CONSISTENCY_STREAK    = 3;

    public const int THRESHOLD_BOREDOM_STREAK        = 3;

    public const int THRESHOLD_ANXIETY_STREAK        = 2;

    public const int THRESHOLD_MODULE_NEARLY_DONE_PCT = 80;

    public const float RATIO_STYLE_MIXED              = 0.20;

    public const int THRESHOLD_PERSISTENT_FAIL        = 2;

    public const int THRESHOLD_SATISFACTORY_PROGRESS  = 61;

    public const int TIME_FAST_THRESHOLD             = 70; // percentage of allocated time

    public const array ALLOCATED_TIME = [
        'beginner' => 60,
        'medium'   => 120,
        'hard'     => 180,
    ];

    // ─── XP & Score Rewards ────────────────────────────────────────────────────

    public const int XP_REWARD_BEGINNER = 10;

    public const int XP_REWARD_MEDIUM   = 20;

    public const int XP_REWARD_HARD     = 30;

    public const int XP_PENALTY_HINT    = 5;

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

    // ─── Default Hints & Streaks ──────────────────────────────────────────────
    public const int DEFAULT_HINTS_AVAILABLE   = 3;

    public const array STREAK_XP_BONUSES = [
        10 => 20,
        5  => 10,
        3  => 5,
    ];

    // ─── Column Defaults ──────────────────────────────────────────────────────
    public static function defaults(): array
    {
        return [
            self::KEY_GLOBAL_XP                => 0,
            self::KEY_CURRENT_LEVEL            => self::LEVEL_PEMULA,
            self::KEY_CURRENT_STREAK           => 0,
            self::KEY_MAX_STREAK               => 0,
            self::KEY_BADGES                   => [],
            self::KEY_LEARNING_STYLE           => self::STYLE_VISUAL,
            self::KEY_UNLOCKED_MODULES         => ['1'],
            self::KEY_CERTIFICATIONS           => [],
            self::KEY_TIME_DISTRIBUTION        => [],
            self::KEY_TOTAL_QUESTIONS_ANSWERED => 0,
            self::KEY_CORRECT_COUNT            => 0,
            self::KEY_WRONG_COUNT              => 0,
            self::KEY_WRONG_STREAK             => 0,
            self::KEY_HINTS_USED_COUNT         => 0,
            self::KEY_HINTS_AVAILABLE          => self::DEFAULT_HINTS_AVAILABLE,
            self::KEY_CURRENT_MATERIAL_ID      => null,
            self::KEY_TARGET_DIFFICULTY        => null,
        ];
    }
}
