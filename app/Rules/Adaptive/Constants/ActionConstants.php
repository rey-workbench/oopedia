<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class ActionConstants
{
    // ── Core Pedagogical Actions (Common Set) ─────────────────────────────────
    public const string FEEDBACK = 'FEEDBACK';
    public const string INCREASE_DIFF = 'INCREASE_DIFF';
    public const string REDUCE_DIFF = 'REDUCE_DIFF';
    public const string STREAK_BONUS = 'STREAK_BONUS';
    public const string REMEDIAL = 'REMEDIAL';
    public const string DEDUCTION = 'DEDUCTION';

    public const array NAMES = [
        self::FEEDBACK => 'General Feedback',
        self::INCREASE_DIFF => 'Increase Difficulty',
        self::REDUCE_DIFF => 'Reduce Difficulty',
        self::STREAK_BONUS => 'Streak Bonus',
        self::REMEDIAL => 'Remedial Review',
        self::DEDUCTION => 'Internal Deduction',
    ];

    // ── Pedagogical Flows ─────────────────────────────────────────────────────
    public const string FLOW_NONE = 'NONE';
    public const string FLOW_NEXT = 'NEXT';
    public const string FLOW_UP = 'UP';
    public const string FLOW_DOWN = 'DOWN';
    public const string FLOW_REVIEW = 'REVIEW';
    public const string FLOW_FINISH = 'FINISH';

    // ── Instruction Keys ──────────────────────────────────────────────────────
    public const string KEY_FLOW = 'flow';
    public const string KEY_TITLE = 'title';
    public const string KEY_MESSAGE = 'message';
    public const string KEY_CERTIFICATION = 'certification';
    public const string KEY_BADGES = 'badges';
    public const string KEY_TITLE_OVERRIDE = 'title_override';

    /**
     * Helper to format increment strings for ActionHandler.
     */
    public static function inc(int|string $val): string
    {
        $intVal = (int) $val;
        return $intVal >= 0 ? "+$intVal" : (string) $intVal;
    }
}
