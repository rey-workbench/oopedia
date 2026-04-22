<?php

namespace App\Rules\Adaptive\Constants;

final class AdaptiveConstants
{
    // Facts (G-Codes) - Tabel 3.14 Interpretasi Gejala
    public const FACT_SCORE_CRITICAL = 'G01';

    public const FACT_SCORE_REMEDIAL = 'G02';

    public const FACT_SCORE_STANDARD = 'G03';

    public const FACT_SCORE_MASTERY  = 'G04';

    public const FACT_TIME_FAST      = 'G05';

    public const FACT_TIME_SLOW      = 'G06';

    public const FACT_STYLE_VISUAL    = 'G07';

    public const FACT_STYLE_TEXTUAL   = 'G08';

    public const FACT_ERROR_SYNTAX    = 'G09';

    public const FACT_ERROR_LOGIC     = 'G10';

    public const FACT_NO_ERROR        = 'G11';

    public const FACT_HINT_USED       = 'G12';

    public const FACT_IN_MODULE       = 'G13';

    public const FACT_MODULE_STARTED  = 'G14';

    public const FACT_DIFF_BEGINNER   = 'G15';

    public const FACT_DIFF_MEDIUM     = 'G16';

    public const FACT_DIFF_HARD       = 'G17';

    public const FACT_IS_FINAL_PROJECT = 'G18';

    public const FACT_IS_PRACTICE     = 'G19';

    public const FACT_NEXT_UNLOCKED   = 'G20';

    public const FACT_PREV_UNLOCKED   = 'G21';

    public const FACT_PERSISTENT_FAIL = 'G22';

    public const FACT_COMPLETED_MODULE = 'G23';

    public const FACT_COMPLETED_ALL    = 'G24';

    public const FACT_HIGH_ENGAGEMENT  = 'G25';

    public const FACT_SATISFACTORY_PROGRESS = 'G26';

    public const FACT_STYLE_MIXED      = 'G27';

    // Actions (H-Codes) - Tabel 3.15 Interpretasi Hasil
    public const ACTION_VISUAL_CRISIS_INTERVENTION = 'H01';

    public const ACTION_TEXTUAL_CRISIS_INTERVENTION = 'H02';

    public const ACTION_SYNTAX_RECOVERY = 'H03';

    public const ACTION_LOGIC_RECOVERY  = 'H04';

    public const ACTION_STANDARD_PROMOTION = 'H05';

    public const ACTION_ACCELERATED_JUMP = 'H06';

    public const ACTION_CRITICAL_BACKTRACKING = 'H07';

    public const ACTION_MODULE_GRADUATION = 'H08';

    public const ACTION_GOLD_CERTIFICATE = 'H09';

    public const ACTION_SILVER_CERTIFICATE = 'H10';

    public const ACTION_BRONZE_CERTIFICATE = 'H11';

    public const ACTION_VISUAL_PROJECT_REVISION = 'H12';

    public const ACTION_TEXTUAL_PROJECT_REVISION = 'H13';

    public const ACTION_PERSISTENT_VISUAL_SAFETY_NET = 'H14';

    public const ACTION_PERSISTENT_TEXTUAL_SAFETY_NET = 'H15';

    public const ACTION_ACCELERATED_MATERIAL = 'H16';

    // Operational Action Labels (NextActionResolver)
    public const ACTION_NEXT_QUESTION    = 'NEXT_QUESTION';

    public const ACTION_NEXT_MATERIAL    = 'NEXT_MATERIAL';

    public const ACTION_FINISH_MATERIAL  = 'FINISH_MATERIAL';

    public const ACTION_ISSUE_CERTIFICATE = 'ISSUE_CERTIFICATE';

    public const ACTION_REDUCE_DIFFICULTY = 'REDUCE_DIFFICULTY';

    public const ACTION_INCREASE_DIFFICULTY = 'INCREASE_DIFFICULTY';

    public const ACTION_STUDY_VISUAL     = 'STUDY_VISUAL';

    public const ACTION_STUDY_TEXTUAL    = 'STUDY_TEXTUAL';

    public const ACTION_STUDY_SYNTAX     = 'STUDY_SYNTAX';

    public const ACTION_STUDY_THEORY     = 'STUDY_THEORY';

    public const ACTION_STUDY_MIXED      = 'STUDY_MIXED';

    // Difficulty Levels
    public const DIFFICULTY_BEGINNER = 'beginner';

    public const DIFFICULTY_MEDIUM   = 'medium';

    public const DIFFICULTY_HARD     = 'hard';

    // Thresholds
    public const TIME_FAST_THRESHOLD = 70; // percentage

    public const ALLOCATED_TIME = [
        'beginner' => 60,
        'medium'   => 120,
        'hard'     => 180,
    ];

    // State Keys
    public const ADAPTIVE_STATE = 'adaptive_state';

    public const TARGET_DIFFICULTY = 'target_difficulty';

    public const FAST_TRACK_ACTIVE = 'fast_track_active';

    public static function certificationRank(?string $cert): int
    {
        return match ($cert) {
            'gold'   => 3,
            'silver' => 2,
            'bronze' => 1,
            default  => 0,
        };
    }
}
