<?php

namespace App\Rules\Adaptive\Constants;

/**
 * AdaptiveConstants
 *
 * Central registry for all Fact (G) and Action (H) codes used in the
 * ITS (Intelligent Tutoring System) adaptive engine.
 */
class AdaptiveConstants
{
    // ==================== QUIZ CONFIGURATION (Shared Thresholds) ====================

    /**
     * Allocated seconds per difficulty level.
     * Used by PerformanceService, FactGatheringService, QuizRewardService.
     */
    public const ALLOCATED_TIME = [
        'beginner' => 45,
        'medium' => 90,
        'hard' => 150,
        'final' => 300,
    ];

    /**
     * Answering below this % of allocated time → G05 (Fast).
     * Used as the canonical "fast answer" threshold across all services.
     */
    public const TIME_FAST_THRESHOLD = 70;

    // ==================== FACTS (G Codes) ====================

    // Performance / Score (G01 - G04)
    public const FACT_SCORE_CRITICAL = 'G01';

    public const FACT_SCORE_REMEDIAL = 'G02';

    public const FACT_SCORE_STANDARD = 'G03';

    public const FACT_SCORE_MASTERY = 'G04';

    // Time Spent (G05 - G06)
    public const FACT_TIME_FAST = 'G05';

    public const FACT_TIME_NORMAL = 'G06';

    // Learning Styles (G07 - G08, G27)
    public const FACT_STYLE_VISUAL = 'G07';

    public const FACT_STYLE_TEXTUAL = 'G08';

    public const FACT_STYLE_MIXED = 'G27';

    // Error Types (G09 - G10)
    public const FACT_ERROR_SYNTAX = 'G09';

    public const FACT_ERROR_LOGIC = 'G10';

    // Hint Usage (G11 - G12)
    public const FACT_HINT_NONE = 'G11';

    public const FACT_HINT_USED = 'G12';

    // Modules (G13, G14, G23 - G25)
    public const FACT_MODULE_FOUNDATION = 'G13';

    public const FACT_MODULE_ENCAPSULATION = 'G14';

    public const FACT_MODULE_INHERITANCE = 'G23';

    public const FACT_MODULE_POLYMORPHISM = 'G24';

    public const FACT_MODULE_ABSTRACTION = 'G25';

    // Difficulty (G15 - G17)
    public const FACT_DIFF_BEGINNER = 'G15';

    public const FACT_DIFF_MEDIUM = 'G16';

    public const FACT_DIFF_HARD = 'G17';

    // Special (G18 - G22, G26)
    public const FACT_IS_FINAL_PROJECT = 'G18';

    public const FACT_NEXT_LOCKED = 'G19';

    public const FACT_NEXT_UNLOCKED = 'G20';

    public const FACT_PREV_UNLOCKED = 'G21';

    public const FACT_PERSISTENT_FAIL = 'G22';

    public const FACT_SATISFACTORY_PROGRESS = 'G26';

    // ==================== ACTIONS (H Codes) ====================

    public const ACTION_VISUAL_CRISIS_INTERVENTION = 'H01';

    public const ACTION_TEXTUAL_CRISIS_INTERVENTION = 'H02';

    public const ACTION_SYNTAX_RECOVERY = 'H03';

    public const ACTION_LOGIC_RECOVERY = 'H04';

    public const ACTION_STANDARD_PROMOTION = 'H05';

    public const ACTION_ACCELERATED_JUMP = 'H06';

    public const ACTION_CRITICAL_BACKTRACKING = 'H07';

    public const ACTION_MODULE_GRADUATION = 'H08';

    public const ACTION_GOLD_CERTIFICATE = 'H09';

    public const ACTION_SILVER_CERTIFICATE = 'H10';

    public const ACTION_BRONZE_CERTIFICATE = 'H11';

    public const ACTION_VISUAL_PROJECT_REVISION = 'H12';

    public const ACTION_TEXTUAL_PROJECT_REVISION = 'H13';

    public const ACTION_PERSISTENT_VISUAL_NET = 'H14';

    public const ACTION_PERSISTENT_TEXTUAL_NET = 'H15';

    /**
     * Get detailed descriptions for Fact (G) codes.
     */
    public static function getFactDescriptions(): array
    {
        return [
            self::FACT_SCORE_CRITICAL => 'Skor Kritis (<40)',
            self::FACT_SCORE_REMEDIAL => 'Skor Remedial (40-69)',
            self::FACT_SCORE_STANDARD => 'Skor Standar (70-89)',
            self::FACT_SCORE_MASTERY => 'Skor Mahir (90-100)',
            self::FACT_TIME_FAST => 'Waktu Pengerjaan Cepat',
            self::FACT_TIME_NORMAL => 'Waktu Pengerjaan Normal',
            self::FACT_STYLE_VISUAL => 'Gaya Belajar Visual',
            self::FACT_STYLE_TEXTUAL => 'Gaya Belajar Tekstual',
            self::FACT_STYLE_MIXED => 'Gaya Belajar Campuran',
            self::FACT_ERROR_SYNTAX => 'Kesalahan Sintaksis',
            self::FACT_ERROR_LOGIC => 'Kesalahan Logika',
            self::FACT_HINT_NONE => 'Tanpa Bantuan Hint',
            self::FACT_HINT_USED => 'Menggunakan Hint',
            self::FACT_MODULE_FOUNDATION => 'Modul: Dasar PBO',
            self::FACT_MODULE_ENCAPSULATION => 'Modul: Enkapsulasi',
            self::FACT_MODULE_INHERITANCE => 'Modul: Pewarisan',
            self::FACT_MODULE_POLYMORPHISM => 'Modul: Polimorfisme',
            self::FACT_MODULE_ABSTRACTION => 'Modul: Abstraksi',
            self::FACT_DIFF_BEGINNER => 'Tingkat Kesulitan: Easy',
            self::FACT_DIFF_MEDIUM => 'Tingkat Kesulitan: Medium',
            self::FACT_DIFF_HARD => 'Tingkat Kesulitan: Advanced',
            self::FACT_IS_FINAL_PROJECT => 'Soal Proyek Akhir',
            self::FACT_NEXT_LOCKED => 'Materi Berikutnya Terkunci',
            self::FACT_NEXT_UNLOCKED => 'Materi Berikutnya Terbuka',
            self::FACT_PREV_UNLOCKED => 'Materi Sebelumnya Terbuka',
            self::FACT_PERSISTENT_FAIL => 'Gagal Berulang (Persistent)',
            self::FACT_SATISFACTORY_PROGRESS => 'Progres Materi Memadai (>=60%)',
        ];
    }

    /**
     * Get detailed descriptions for Action (H) codes.
     */
    public static function getActionDescriptions(): array
    {
        return [
            self::ACTION_VISUAL_CRISIS_INTERVENTION => 'Intervensi Krisis Visual',
            self::ACTION_TEXTUAL_CRISIS_INTERVENTION => 'Intervensi Krisis Tekstual',
            self::ACTION_SYNTAX_RECOVERY => 'Pemulihan Sintaksis',
            self::ACTION_LOGIC_RECOVERY => 'Pemulihan Logika',
            self::ACTION_STANDARD_PROMOTION => 'Promosi Standar',
            self::ACTION_ACCELERATED_JUMP => 'Loncatan Akselerasi (Fast Track)',
            self::ACTION_CRITICAL_BACKTRACKING => 'Mundur Kritis (Backtracking)',
            self::ACTION_MODULE_GRADUATION => 'Kelulusan Modul',
            self::ACTION_GOLD_CERTIFICATE => 'Sertifikat Emas',
            self::ACTION_SILVER_CERTIFICATE => 'Sertifikat Perak',
            self::ACTION_BRONZE_CERTIFICATE => 'Sertifikat Perunggu',
            self::ACTION_VISUAL_PROJECT_REVISION => 'Revisi Proyek Visual',
            self::ACTION_TEXTUAL_PROJECT_REVISION => 'Revisi Proyek Tekstual',
            self::ACTION_PERSISTENT_VISUAL_NET => 'Safety Net Visual (Gagal Berulang)',
            self::ACTION_PERSISTENT_TEXTUAL_NET => 'Safety Net Tekstual (Gagal Berulang)',
        ];
    }
}
