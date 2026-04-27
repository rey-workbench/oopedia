<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class ActionConstants
{
    // ── Primary Action Codes (H-codes) ────────────────────────────────────────
    public const string DEDUCTION      = 'H00';
    public const string NEXT_QUESTION  = 'H01';
    public const string INCREASE_DIFF  = 'H02';
    public const string REDUCE_DIFF    = 'H03';
    public const string STUDY_MATERIAL = 'H04';
    public const string WRONG_ANSWER   = 'H05';
    public const string AWARD_BADGE    = 'H06';
    public const string CELEBRATION    = 'H07';
    public const string STREAK_BONUS   = 'H08';
    public const string EMPATHY_MSG    = 'H09';

    public const array NAMES = [
        self::DEDUCTION      => 'Logic Deduction',
        self::NEXT_QUESTION  => 'Next Question',
        self::INCREASE_DIFF  => 'Increase Difficulty',
        self::REDUCE_DIFF    => 'Reduce Difficulty',
        self::STUDY_MATERIAL => 'Remedial Review',
        self::WRONG_ANSWER   => 'Wrong Feedback',
        self::AWARD_BADGE    => 'Award Badge',
        self::CELEBRATION    => 'Module Graduation',
        self::STREAK_BONUS   => 'Streak Bonus',
        self::EMPATHY_MSG    => 'Empathy Message',
    ];

    // ── Pedagogical Flows ─────────────────────────────────────────────────────
    public const string FLOW_NONE    = 'NONE';
    public const string FLOW_NEXT    = 'NEXT';
    public const string FLOW_UP      = 'UP';
    public const string FLOW_DOWN    = 'DOWN';
    public const string FLOW_REVIEW  = 'REVIEW';
    public const string FLOW_FINISH  = 'FINISH';

    // ── Instruction Keys ──────────────────────────────────────────────────────
    public const string KEY_FLOW          = 'flow';
    public const string KEY_TITLE         = 'title';
    public const string KEY_MESSAGE       = 'message';
    public const string KEY_CERTIFICATION = 'certification';
    public const string KEY_BADGES        = 'badges';

    /**
     * Helper to format increment strings for ActionHandler.
     */
    public static function inc(int|string $val): string
    {
        $intVal = (int) $val;
        return $intVal >= 0 ? "+$intVal" : (string) $intVal;
    }
}
