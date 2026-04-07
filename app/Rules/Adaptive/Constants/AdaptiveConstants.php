<?php

namespace App\Rules\Adaptive\Constants;

/**
 * AdaptiveConstants
 *
 * Central registry for all Fact (G) and Action (H) codes used in the
 * ITS (Intelligent Tutoring System) adaptive engine.
 */
final class AdaptiveConstants
{
    // ==================== QUIZ CONFIGURATION (Shared Thresholds) ====================

    /**
     * Allocated seconds per difficulty level.
     * Used by PerformanceService, FactGatheringService, QuizRewardService.
     */
    public const array ALLOCATED_TIME = [
        'beginner' => 45,
        'medium'   => 90,
        'hard'     => 150,
        'final'    => 300,
    ];

    /**
     * Answering below this % of allocated time → G05 (Fast).
     * Used as the canonical "fast answer" threshold across all services.
     */
    public const int TIME_FAST_THRESHOLD = 70;

    // ==================== FACTS (G Codes) ====================

    // Performance / Score (G01 - G04)
    public const string FACT_SCORE_CRITICAL = 'G01';

    public const string FACT_SCORE_REMEDIAL = 'G02';

    public const string FACT_SCORE_STANDARD = 'G03';

    public const string FACT_SCORE_MASTERY = 'G04';

    // Time Spent (G05 - G06)
    public const string FACT_TIME_FAST = 'G05';

    public const string FACT_TIME_SLOW = 'G06';

    // Learning Styles (G07 - G08, G27)
    public const string FACT_STYLE_VISUAL = 'G07';

    public const string FACT_STYLE_TEXTUAL = 'G08';

    // Error Types (G09 - G11)
    public const string FACT_ERROR_SYNTAX = 'G09';

    public const string FACT_ERROR_LOGIC = 'G10';

    public const string FACT_NO_ERROR = 'G11';

    // Hint Usage (G12)
    public const string FACT_HINT_USED = 'G12';

    // Modules (G13 - G14)
    public const string FACT_IN_MODULE = 'G13';

    public const string FACT_MODULE_STARTED = 'G14';

    // Difficulty (G15 - G17)
    public const string FACT_DIFF_BEGINNER = 'G15';

    public const string FACT_DIFF_MEDIUM = 'G16';

    public const string FACT_DIFF_HARD = 'G17';

    // Special (G18 - G22)
    public const string FACT_IS_FINAL_PROJECT = 'G18';

    public const string FACT_IS_PRACTICE = 'G19';

    public const string FACT_NEXT_UNLOCKED = 'G20';

    public const string FACT_PREV_UNLOCKED = 'G21';

    public const string FACT_PERSISTENT_FAIL = 'G22';

    // Progress (G23 - G26)
    public const string FACT_COMPLETED_MODULE = 'G23';

    public const string FACT_COMPLETED_ALL_MODULES = 'G24';

    public const string FACT_HIGH_ENGAGEMENT = 'G25';

    public const string FACT_SATISFACTORY_PROGRESS = 'G26';

    // Additional Learning Style (G27)
    public const string FACT_STYLE_MIXED = 'G27';

    // ==================== ACTIONS (H Codes) ====================

    public const string ACTION_VISUAL_CRISIS_INTERVENTION = 'H01';

    public const string ACTION_TEXTUAL_CRISIS_INTERVENTION = 'H02';

    public const string ACTION_SYNTAX_RECOVERY = 'H03';

    public const string ACTION_LOGIC_RECOVERY = 'H04';

    public const string ACTION_STANDARD_PROMOTION = 'H05';

    public const string ACTION_ACCELERATED_JUMP = 'H06';

    public const string ACTION_CRITICAL_BACKTRACKING = 'H07';

    public const string ACTION_MODULE_GRADUATION = 'H08';

    public const string ACTION_GOLD_CERTIFICATE = 'H09';

    public const string ACTION_SILVER_CERTIFICATE = 'H10';

    public const string ACTION_BRONZE_CERTIFICATE = 'H11';

    public const string ACTION_VISUAL_PROJECT_REVISION = 'H12';

    public const string ACTION_TEXTUAL_PROJECT_REVISION = 'H13';

    public const string ACTION_PERSISTENT_VISUAL_NET = 'H14';

    public const string ACTION_PERSISTENT_TEXTUAL_NET = 'H15';

    public const string ACTION_ACCELERATED_MATERIAL_PROMOTION = 'H16';

    // State Fields
    public const string NEXT_ACTION = 'next_action';

    public const string MESSAGE = 'message';

    public const string RECOMMENDATION = 'recommendation';

    public const string INTERVENTION_TYPE = 'intervention_type';

    public const string RECOVERY_TYPE = 'recovery_type';

    public const string FORCE_MATERIAL_REVIEW = 'force_material_review';

    public const string ACHIEVEMENT = 'achievement';

    public const string CERTIFICATION = 'certification';

    public const string BADGES = 'badges';

    public const string GAMIFICATION_DATA = 'gamification_data';

    public const string TARGET_DIFFICULTY = 'target_difficulty';

    public const string FAST_TRACK_ACTIVE = 'fast_track_active';

    public const string ADAPTIVE_STATE = 'adaptive_state';

    public const string MODULE_PROGRESS = 'module_progress';

    // Action Values
    public const string ACTION_NEXT_QUESTION = 'NEXT_QUESTION';

    public const string ACTION_NEXT_MATERIAL = 'NEXT_MATERIAL';

    public const string ACTION_FINISH_MATERIAL = 'FINISH_MATERIAL';

    public const string ACTION_ISSUE_CERTIFICATE = 'ISSUE_CERTIFICATE';

    public const string ACTION_REDUCE_DIFFICULTY = 'REDUCE_DIFFICULTY';

    public const string ACTION_STUDY_VISUAL = 'STUDY_VISUAL';

    public const string ACTION_STUDY_TEXTUAL = 'STUDY_TEXTUAL';

    public const string ACTION_STUDY_SYNTAX = 'STUDY_SYNTAX';

    public const string ACTION_STUDY_THEORY = 'STUDY_THEORY';

    public const string ACTION_STUDY_MIXED = 'STUDY_MIXED';

    // Intervention Types
    public const string INTERVENTION_VISUAL_CRISIS = 'visual_crisis';

    public const string INTERVENTION_TEXTUAL_CRISIS = 'textual_crisis';

    public const string INTERVENTION_PERSISTENT_VISUAL_SAFETY = 'persistent_visual_safety';

    public const string INTERVENTION_PERSISTENT_TEXTUAL_SAFETY = 'persistent_textual_safety';

    public const string INTERVENTION_VISUAL_PROJECT = 'visual_project_revision';

    public const string INTERVENTION_TEXTUAL_PROJECT = 'textual_project_revision';

    public const string INTERVENTION_FINAL_PROJECT_VISUAL_PERSISTENT = 'final_project_visual_persistent';

    public const string INTERVENTION_FINAL_PROJECT_TEXTUAL_PERSISTENT = 'final_project_textual_persistent';

    // Recovery Types
    public const string RECOVERY_SYNTAX = 'syntax';

    public const string RECOVERY_LOGIC = 'logic';

    public const string RECOVERY_INDEPENDENT = 'independent';

    // Achievement Values
    public const string ACHIEVEMENT_MODULE_COMPLETED = 'module_completed';

    public const string ACHIEVEMENT_GOLD_CERTIFICATE = 'gold_certificate';

    public const string ACHIEVEMENT_SILVER_CERTIFICATE = 'silver_certificate';

    public const string ACHIEVEMENT_BRONZE_CERTIFICATE = 'bronze_certificate';

    // Certification Values
    public const string CERT_GOLD = 'gold';

    public const string CERT_SILVER = 'silver';

    public const string CERT_BRONZE = 'bronze';

    // Badge Values
    public const string BADGE_GOLD_ARCHITECT = 'gold_architect';

    public const string BADGE_SILVER_ARCHITECT = 'silver_architect';

    public const string BADGE_SILVER_DEVELOPER = 'silver_developer';

    public const string BADGE_BRONZE_ARCHITECT = 'bronze_architect';

    public const string BADGE_BRONZE_JUNIOR = 'bronze_junior';

    // Difficulty Values
    public const string DIFFICULTY_BEGINNER = 'beginner';

    public const string DIFFICULTY_MEDIUM = 'medium';

    public const string DIFFICULTY_HARD = 'hard';

    /**
     * Get detailed descriptions for Fact (G) codes.
     */
    public static function getFactDescriptions(): array
    {
        return [
            self::FACT_SCORE_CRITICAL        => 'Skor Kritis (<40)',
            self::FACT_SCORE_REMEDIAL        => 'Skor Remedial (40-69)',
            self::FACT_SCORE_STANDARD        => 'Skor Standar (70-89)',
            self::FACT_SCORE_MASTERY         => 'Skor Mahir (90-100)',
            self::FACT_TIME_FAST             => 'Waktu Pengerjaan Cepat',
            self::FACT_TIME_SLOW             => 'Waktu Pengerjaan Lambat',
            self::FACT_STYLE_VISUAL          => 'Gaya Belajar Visual',
            self::FACT_STYLE_TEXTUAL         => 'Gaya Belajar Tekstual',
            self::FACT_STYLE_MIXED           => 'Gaya Belajar Campuran',
            self::FACT_ERROR_SYNTAX          => 'Kesalahan Sintaksis',
            self::FACT_ERROR_LOGIC           => 'Kesalahan Logika',
            self::FACT_NO_ERROR              => 'Tidak Ada Kesalahan',
            self::FACT_HINT_USED             => 'Menggunakan Hint',
            self::FACT_IN_MODULE             => 'Dalam Modul Pembelajaran',
            self::FACT_MODULE_STARTED        => 'Modul Dimulai',
            self::FACT_DIFF_BEGINNER         => 'Tingkat Kesulitan: Easy',
            self::FACT_DIFF_MEDIUM           => 'Tingkat Kesulitan: Medium',
            self::FACT_DIFF_HARD             => 'Tingkat Kesulitan: Advanced',
            self::FACT_IS_FINAL_PROJECT      => 'Soal Proyek Akhir',
            self::FACT_IS_PRACTICE           => 'Soal Latihan',
            self::FACT_NEXT_UNLOCKED         => 'Materi Berikutnya Terbuka',
            self::FACT_PREV_UNLOCKED         => 'Materi Sebelumnya Terbuka',
            self::FACT_PERSISTENT_FAIL       => 'Gagal Berulang (Persistent)',
            self::FACT_COMPLETED_MODULE      => 'Modul Selesai',
            self::FACT_COMPLETED_ALL_MODULES => 'Semua Modul Selesai',
            self::FACT_HIGH_ENGAGEMENT       => 'Keterlibatan Tinggi',
            self::FACT_SATISFACTORY_PROGRESS => 'Progres Materi Memadai (>=60%)',
        ];
    }

    /**
     * Get detailed descriptions for Action (H) codes.
     */
    public static function getActionDescriptions(): array
    {
        return [
            self::ACTION_VISUAL_CRISIS_INTERVENTION     => 'Intervensi Krisis Visual',
            self::ACTION_TEXTUAL_CRISIS_INTERVENTION    => 'Intervensi Krisis Tekstual',
            self::ACTION_SYNTAX_RECOVERY                => 'Pemulihan Sintaksis',
            self::ACTION_LOGIC_RECOVERY                 => 'Pemulihan Logika',
            self::ACTION_STANDARD_PROMOTION             => 'Promosi Standar',
            self::ACTION_ACCELERATED_JUMP               => 'Loncatan Akselerasi (Fast Track)',
            self::ACTION_CRITICAL_BACKTRACKING          => 'Mundur Kritis (Backtracking)',
            self::ACTION_MODULE_GRADUATION              => 'Kelulusan Modul',
            self::ACTION_GOLD_CERTIFICATE               => 'Sertifikat Emas',
            self::ACTION_SILVER_CERTIFICATE             => 'Sertifikat Perak',
            self::ACTION_BRONZE_CERTIFICATE             => 'Sertifikat Perunggu',
            self::ACTION_VISUAL_PROJECT_REVISION        => 'Revisi Proyek Visual',
            self::ACTION_TEXTUAL_PROJECT_REVISION       => 'Revisi Proyek Tekstual',
            self::ACTION_PERSISTENT_VISUAL_NET          => 'Safety Net Visual (Gagal Berulang)',
            self::ACTION_PERSISTENT_TEXTUAL_NET         => 'Safety Net Tekstual (Gagal Berulang)',
            self::ACTION_ACCELERATED_MATERIAL_PROMOTION => 'Loncatan Akseleratif Modul',
        ];
    }
}
