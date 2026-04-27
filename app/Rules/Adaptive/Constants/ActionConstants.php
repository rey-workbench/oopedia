<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class ActionConstants
{
    // ── Action Codes (H-codes) ────────────────────────────────────────────────
    public const string DEDUCTION      = 'H00'; // Silent deduction code in DB
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
        self::DEDUCTION      => 'Internal Logic',
        self::NEXT_QUESTION  => 'Berikan Soal Berikutnya',
        self::INCREASE_DIFF  => 'Treatment: Tingkatkan Kesulitan',
        self::REDUCE_DIFF    => 'Treatment: Turunkan Kesulitan',
        self::STUDY_MATERIAL => 'Treatment: Review Materi',
        self::WRONG_ANSWER   => 'Tampilkan Feedback Kesalahan',
        self::AWARD_BADGE    => 'Berikan Badge Penghargaan',
        self::CELEBRATION    => 'Perayaan Kelulusan Modul',
        self::STREAK_BONUS   => 'Bonus Beruntun (Streak)',
        self::EMPATHY_MSG    => 'Pesan Empati & Motivasi',
    ];

    // ── Action Labels (Operational Defaults) ──────────────────────────────────
    public const string SILENT             = 'NO_ACTION';
    public const string FINISH_MATERIAL    = 'FINISH_MATERIAL';
    public const string LABEL_NEXT_QUESTION      = 'NEXT_QUESTION';
    public const string LABEL_INCREASE_DIFFICULTY = 'INCREASE_DIFFICULTY';
    public const string LABEL_REDUCE_DIFFICULTY   = 'REDUCE_DIFFICULTY';
    public const string LABEL_STUDY_MATERIAL     = 'STUDY_MATERIAL';

    // ── Action Instruction Keys ─────────────────────────────────────────────
    public const string KEY_NEXT_ACTION   = 'next_action';
    public const string KEY_LABEL         = 'label';
    public const string KEY_MESSAGE       = 'message';
    public const string KEY_TITLE         = 'title';
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
