<?php

namespace App\Rules\Adaptive\Constants;

/**
 * Berisi kunci nama fakta/aksi untuk pencarian dinamis di database.
 * Hindari menyimpan kode Gxx/Hxx secara hardcode di sini.
 */
final class AdaptiveConstants
{
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

    // ── Operational Thresholds & Logic ──────────────────────────────
    public const TIME_FAST_THRESHOLD = 70; // percentage of allocated time
    public const ALLOCATED_TIME = [
        'beginner' => 60,
        'medium'   => 120,
        'hard'     => 180,
    ];

    public const ADAPTIVE_STATE = 'adaptive_state';
    public const TARGET_DIFFICULTY = 'target_difficulty';
    public const FAST_TRACK_ACTIVE = 'fast_track_active';

    // ── Operational Action Labels (Dibutuhkan oleh NextActionResolverService) ──
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
    public const ACTION_RETRY_SAME_DIFFICULTY = 'RETRY_SAME_DIFFICULTY';
    public const ACTION_MODULE_COMPLETE  = 'MODULE_COMPLETE';
    public const ACTION_REVISE_PROJECT   = 'REVISE_PROJECT';
    public const ACTION_REVIEW_MATERIAL  = 'REVIEW_MATERIAL';
    public const ACTION_SHOW_VISUAL_HINT = 'SHOW_VISUAL_HINT';
    public const ACTION_SHOW_TEXT_HINT   = 'SHOW_TEXT_HINT';
}
