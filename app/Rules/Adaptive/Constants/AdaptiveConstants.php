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

    // ── Fact Codes (G-codes) ──────────────────────────────────────────────────
    public const FACT_SCORE_FAILURE      = 'G01';
    public const FACT_SCORE_PASS         = 'G02';
    public const FACT_SCORE_PERFECT      = 'G03';
    public const FACT_SCORE_ZERO         = 'G04';
    public const FACT_CONSISTENCY_HIGH   = 'G05';
    public const FACT_MASTERY_BEGINNER   = 'G06';
    public const FACT_MASTERY_MEDIUM     = 'G07';
    public const FACT_MASTERY_HARD       = 'G08';
    public const FACT_STYLE_VISUAL       = 'G09';
    public const FACT_STYLE_TEXTUAL      = 'G10';
    public const FACT_STYLE_MIXED        = 'G11';
    public const FACT_ERROR_SYNTAX       = 'G12';
    public const FACT_ERROR_LOGIC        = 'G13';
    public const FACT_ERROR_CONCEPT      = 'G14';
    public const FACT_NO_ERROR           = 'G15';
    public const FACT_TIME_FAST_SUCCESS  = 'G16';
    public const FACT_TIME_FAST_FAIL     = 'G17';
    public const FACT_TIME_SLOW_SUCCESS  = 'G18';
    public const FACT_TIME_SLOW_FAIL     = 'G19';
    public const FACT_HINT_USED          = 'G20';
    public const FACT_BOREDOM_SIGNS      = 'G21';
    public const FACT_ANXIETY_SIGNS      = 'G22';
    public const FACT_HIGH_STRUGGLE      = 'G23';
    public const FACT_DIFF_BEGINNER      = 'G26';
    public const FACT_PERSISTENT_FAIL    = 'G28';
    public const FACT_MODULE_NEARLY_DONE = 'G29';
    public const FACT_MODULE_GRADUATION  = 'G30';
    public const FACT_DIFF_MEDIUM        = 'G31';
    public const FACT_DIFF_HARD          = 'G32';
    public const FACT_IN_MODULE          = 'G33';
    public const FACT_SATISFACTORY_PROGRESS = 'G34';
    public const FACT_NEXT_UNLOCKED      = 'G35';
    public const FACT_PREV_UNLOCKED      = 'G36';
    public const FACT_INDEPENDENT_WORK   = 'G37';
    public const FACT_NEXT_LOCKED        = 'G38';
    public const FACT_IS_FINAL_PROJECT   = 'G39';

    // ── Fact Names (Mapping for Seeder/UI) ────────────────────────────────────
    public const FACT_NAMES = [
        self::FACT_SCORE_FAILURE      => 'Perlu Perbaikan',
        self::FACT_SCORE_PASS         => 'Performa Stabil',
        self::FACT_SCORE_PERFECT      => 'Performa Sempurna',
        self::FACT_SCORE_ZERO         => 'Belum Menguasai',
        self::FACT_CONSISTENCY_HIGH   => 'Konsistensi Tinggi',
        self::FACT_MASTERY_BEGINNER   => 'Ahli Dasar',
        self::FACT_MASTERY_MEDIUM     => 'Ahli Madya',
        self::FACT_MASTERY_HARD       => 'Ahli Utama',
        self::FACT_STYLE_VISUAL       => 'Gaya: Visual',
        self::FACT_STYLE_TEXTUAL      => 'Gaya: Tekstual',
        self::FACT_STYLE_MIXED        => 'Gaya: Campuran',
        self::FACT_ERROR_SYNTAX       => 'Kendala: Sintaks',
        self::FACT_ERROR_LOGIC        => 'Kendala: Logika',
        self::FACT_ERROR_CONCEPT      => 'Kendala: Konsep',
        self::FACT_NO_ERROR           => 'Tanpa Cela',
        self::FACT_TIME_FAST_SUCCESS  => 'Kilat & Akurat',
        self::FACT_TIME_FAST_FAIL     => 'Terburu-buru',
        self::FACT_TIME_SLOW_SUCCESS  => 'Sabar & Tekun',
        self::FACT_TIME_SLOW_FAIL     => 'Berjuang Keras',
        self::FACT_HINT_USED          => 'Bantuan Digunakan',
        self::FACT_BOREDOM_SIGNS      => 'Tanda Kebosanan',
        self::FACT_ANXIETY_SIGNS      => 'Tanda Kecemasan',
        self::FACT_HIGH_STRUGGLE      => 'Tantangan Tinggi',
        self::FACT_DIFF_BEGINNER      => 'Level Dasar',
        self::FACT_PERSISTENT_FAIL    => 'Hambatan Berlanjut',
        self::FACT_MODULE_NEARLY_DONE => 'Hampir Selesai',
        self::FACT_MODULE_GRADUATION  => 'Siap Lulus',
        self::FACT_DIFF_MEDIUM        => 'Level Menengah',
        self::FACT_DIFF_HARD          => 'Level Mahir',
        self::FACT_IN_MODULE          => 'Dalam Pembelajaran',
        self::FACT_SATISFACTORY_PROGRESS => 'Progres Memuaskan',
        self::FACT_NEXT_UNLOCKED      => 'Modul Berikutnya Terbuka',
        self::FACT_PREV_UNLOCKED      => 'Modul Sebelumnya Terbuka',
        self::FACT_INDEPENDENT_WORK   => 'Mandiri',
        self::FACT_NEXT_LOCKED        => 'Modul Berikutnya Terkunci',
        self::FACT_IS_FINAL_PROJECT   => 'Dalam Proyek Akhir',
    ];

    // ── Virtual Fact Codes (V-codes) ─────────────────────────────────────────
    public const V01_HIGH_PERFORMER        = 'V01';
    public const V02_NEEDS_FOUNDATION      = 'V02';
    public const V03_IN_CRISIS             = 'V03';
    public const V04_STYLE_MISMATCH_VISUAL = 'V04';
    public const V05_STYLE_MISMATCH_TEXTUAL = 'V05';
    public const V06_MASTERY_READY_BEGINNER = 'V06';
    public const V07_MASTERY_READY_MEDIUM   = 'V07';
    public const V08_CONCEPTUAL_GAP         = 'V08';
    public const V09_CARELESS_PATTERN       = 'V09';
    public const V10_STRUGGLE_PATTERN       = 'V10';
    public const V11_SPEED_DEMON            = 'V11';
    public const V12_METICULOUS_SOLVER      = 'V12';
    public const V13_UNSTOPPABLE_FORCE      = 'V13';

    public const VIRTUAL_NAMES = [
        self::V01_HIGH_PERFORMER        => 'Bintang Pelajar',
        self::V02_NEEDS_FOUNDATION      => 'Butuh Fondasi',
        self::V03_IN_CRISIS             => 'Dalam Krisis',
        self::V04_STYLE_MISMATCH_VISUAL => 'Mismatch Visual',
        self::V05_STYLE_MISMATCH_TEXTUAL => 'Mismatch Tekstual',
        self::V06_MASTERY_READY_BEGINNER => 'Siap Naik ke Menengah',
        self::V07_MASTERY_READY_MEDIUM   => 'Siap Naik ke Mahir',
        self::V08_CONCEPTUAL_GAP         => 'Gap Konseptual',
        self::V09_CARELESS_PATTERN       => 'Pola Kecerobohan',
        self::V10_STRUGGLE_PATTERN       => 'Pola Kesulitan',
        self::V11_SPEED_DEMON            => 'Kilat & Akurat',
        self::V12_METICULOUS_SOLVER      => 'Sabar & Tekun',
        self::V13_UNSTOPPABLE_FORCE      => 'Tak Terbendung',
    ];

    // ─── Level Names & Thresholds ──────────────────────────────────────────────
    public const string LEVEL_PEMULA = 'Pemula';

    public const string LEVEL_JUNIOR = 'Junior';

    public const string LEVEL_MENENGAH = 'Menengah';

    public const string LEVEL_AHLI = 'Ahli';

    public const string LEVEL_MASTER = 'Master';

    public const array LEVEL_THRESHOLDS = [
        ['name' => self::LEVEL_PEMULA, 'min' => 0],
        ['name' => self::LEVEL_JUNIOR, 'min' => 100],
        ['name' => self::LEVEL_MENENGAH, 'min' => 250],
        ['name' => self::LEVEL_AHLI, 'min' => 500],
        ['name' => self::LEVEL_MASTER, 'min' => 1000],
    ];

    // ─── Learning Styles ──────────────────────────────────────────────────────
    public const string STYLE_VISUAL = 'visual';

    public const string STYLE_TEXTUAL = 'textual';

    public const string STYLE_MIXED = 'mixed';

    // ── Action Codes (H-codes) ────────────────────────────────────────────────
    public const ACTION_DEDUCTION          = 'H00'; // Silent deduction code in DB
    public const ACTION_STANDARD_PROMOTION  = 'H01';
    public const ACTION_STANDARD_REMEDIAL   = 'H02';
    public const ACTION_CRITICAL_BACKTRACK  = 'H04';
    public const ACTION_MODULE_GRADUATION   = 'H05';
    public const ACTION_STUDY_VISUAL_MAT    = 'H06';
    public const ACTION_STUDY_TEXTUAL_MAT   = 'H07';
    public const ACTION_LOGIC_GUIDE         = 'H10';
    public const ACTION_SYNTAX_GUIDE        = 'H11';
    public const ACTION_ANXIETY_RELIEF      = 'H17';
    public const ACTION_CHALLENGE_MODE      = 'H18';
    public const ACTION_MOTIVATIONAL_MSG    = 'H19';
    public const ACTION_CAREFUL_ALERT       = 'H20';
    public const ACTION_STUDY_MIXED_MAT     = 'H21';
    public const ACTION_CRISIS_INTERVENTION = 'H22';
    public const ACTION_PERSISTENT_FAIL_AID = 'H23';
    public const ACTION_BOOST_TO_MEDIUM     = 'H24';
    public const ACTION_BOOST_TO_HARD       = 'H25';
    public const ACTION_PREMIUM_PRAISE      = 'H26';

    // ── Action Names (Mapping for Seeder/UI) ──────────────────────────────────
    public const ACTION_NAMES = [
        self::ACTION_DEDUCTION          => 'Silent Deduction',
        self::ACTION_STANDARD_PROMOTION  => 'Standard Promotion',
        self::ACTION_STANDARD_REMEDIAL   => 'Standard Remedial',
        self::ACTION_CRITICAL_BACKTRACK  => 'Critical Backtrack',
        self::ACTION_MODULE_GRADUATION   => 'Module Graduation',
        self::ACTION_STUDY_VISUAL_MAT    => 'Study Visual',
        self::ACTION_STUDY_TEXTUAL_MAT   => 'Study Textual',
        self::ACTION_LOGIC_GUIDE         => 'Logic Guide',
        self::ACTION_SYNTAX_GUIDE        => 'Syntax Guide',
        self::ACTION_ANXIETY_RELIEF      => 'Anxiety Relief',
        self::ACTION_CHALLENGE_MODE      => 'Challenge Mode',
        self::ACTION_MOTIVATIONAL_MSG    => 'Motivational Msg',
        self::ACTION_CAREFUL_ALERT       => 'Careful Alert',
        self::ACTION_STUDY_MIXED_MAT     => 'Study Mixed',
        self::ACTION_CRISIS_INTERVENTION => 'Crisis Intervention',
        self::ACTION_PERSISTENT_FAIL_AID => 'Persistent Fail Aid',
        self::ACTION_BOOST_TO_MEDIUM     => 'Boost to Medium',
        self::ACTION_BOOST_TO_HARD       => 'Boost to Hard',
        self::ACTION_PREMIUM_PRAISE      => 'Premium Praise',
    ];

    // ── Action Labels (Operational Defaults) ──────────────────────────────────
    public const ACTION_SILENT             = 'NO_ACTION';
    public const ACTION_NEXT_QUESTION      = 'NEXT_QUESTION';
    public const ACTION_NEXT_MATERIAL      = 'NEXT_MATERIAL';
    public const ACTION_FINISH_MATERIAL    = 'FINISH_MATERIAL';
    public const ACTION_REDUCE_DIFFICULTY   = 'REDUCE_DIFFICULTY';
    public const ACTION_INCREASE_DIFFICULTY = 'INCREASE_DIFFICULTY';
    public const ACTION_STUDY_MATERIAL     = 'STUDY_MATERIAL';
    public const ACTION_REVISE_PROJECT     = 'REVISE_PROJECT';

    // ── Action Instruction Keys ─────────────────────────────────────────────
    public const string KEY_NEXT_ACTION   = 'next_action';
    public const string KEY_LABEL         = 'label';
    public const string KEY_MESSAGE       = 'message';
    public const string KEY_TITLE         = 'title';
    public const string KEY_CERTIFICATION = 'certification';

    // ─── Difficulty Levels ────────────────────────────────────────────────────
    public const DIFFICULTY_BEGINNER = 'beginner';

    public const DIFFICULTY_MEDIUM = 'medium';

    public const DIFFICULTY_HARD = 'hard';

    public const DIFFICULTY_FINAL = 'final';

    // ─── Pedagogical Thresholds (Operational Defaults) ───────────────────────
    public const int THRESHOLD_MASTERY_ACCURACY = 70;

    public const int THRESHOLD_MASTERY_MIN_ATTEMPTS = 5;

    public const int THRESHOLD_CONSISTENCY_STREAK = 3;

    public const int THRESHOLD_BOREDOM_STREAK = 3;

    public const int THRESHOLD_ANXIETY_STREAK = 2;

    public const int THRESHOLD_MODULE_NEARLY_DONE_PCT = 80;

    public const float RATIO_STYLE_MIXED = 0.20;

    public const int THRESHOLD_PERSISTENT_FAIL = 2;

    public const int THRESHOLD_SATISFACTORY_PROGRESS = 61;

    public const int TIME_FAST_THRESHOLD = 70; // percentage of allocated time

    public const array ALLOCATED_TIME = [
        'beginner' => 60,
        'medium' => 120,
        'hard' => 180,
    ];

    // ─── XP & Score Rewards ────────────────────────────────────────────────────

    public const int XP_REWARD_BEGINNER = 10;

    public const int XP_REWARD_MEDIUM = 20;

    public const int XP_REWARD_HARD = 30;

    public const int XP_PENALTY_HINT = 5;

    public const int SCORE_BASE_REWARD = 80;

    public const int SCORE_BONUS_HARD = 10;

    public const int SCORE_BONUS_MEDIUM = 5;

    public const int SCORE_BONUS_FINAL = 20;

    public const int SCORE_BONUS_TIME = 10;

    public const int SCORE_PENALTY_HINT = 20;

    public const array SCORE_REWARDS = [
        'base' => self::SCORE_BASE_REWARD,
        'difficulty_bonus' => [
            'hard' => self::SCORE_BONUS_HARD,
            'medium' => self::SCORE_BONUS_MEDIUM,
            'final' => self::SCORE_BONUS_FINAL,
        ],
        'time_bonus' => self::SCORE_BONUS_TIME,
        'hint_penalty' => self::SCORE_PENALTY_HINT,
    ];

    // ─── Default Hints & Streaks ──────────────────────────────────────────────
    public const int DEFAULT_HINTS_AVAILABLE = 3;

    public const array STREAK_XP_BONUSES = [
        10 => 20,
        5 => 10,
        3 => 5,
    ];

    // ─── Column Defaults ──────────────────────────────────────────────────────
    public static function defaults(): array
    {
        return [
            self::KEY_GLOBAL_XP => 0,
            self::KEY_CURRENT_LEVEL => self::LEVEL_PEMULA,
            self::KEY_CURRENT_STREAK => 0,
            self::KEY_MAX_STREAK => 0,
            self::KEY_BADGES => [],
            self::KEY_LEARNING_STYLE => self::STYLE_VISUAL,
            self::KEY_UNLOCKED_MODULES => ['1'],
            self::KEY_CERTIFICATIONS => [],
            self::KEY_TIME_DISTRIBUTION => [],
            self::KEY_TOTAL_QUESTIONS_ANSWERED => 0,
            self::KEY_CORRECT_COUNT => 0,
            self::KEY_WRONG_COUNT => 0,
            self::KEY_WRONG_STREAK => 0,
            self::KEY_HINTS_USED_COUNT => 0,
            self::KEY_HINTS_AVAILABLE => self::DEFAULT_HINTS_AVAILABLE,
            self::KEY_CURRENT_MATERIAL_ID => null,
            self::KEY_TARGET_DIFFICULTY => null,
        ];
    }
}
