<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class FactConstants
{
    // ── Fact Codes (G-codes) ──────────────────────────────────────────────────
    public const string SCORE_FAIL = 'G01';
    public const string SCORE_PASS = 'G02';
    public const string TIME_QUICK = 'G03';
    public const string TIME_SLOW  = 'G04';
    public const string HINT_USED  = 'G05';
    public const string DIFF_BEGINNER = 'G06';
    public const string DIFF_MEDIUM   = 'G07';
    public const string DIFF_HARD     = 'G08';

    public const array NAMES = [
        self::SCORE_FAIL => 'Jawaban Salah',
        self::SCORE_PASS => 'Jawaban Benar',
        self::TIME_QUICK => 'Respon Cepat',
        self::TIME_SLOW  => 'Respon Lambat',
        self::HINT_USED  => 'Menggunakan Bantuan',
        self::DIFF_BEGINNER => 'Tingkat Pemula',
        self::DIFF_MEDIUM   => 'Tingkat Menengah',
        self::DIFF_HARD     => 'Tingkat Ahli',
    ];

    // ── Virtual Fact Codes (V-codes) ─────────────────────────────────────────
    public const string V_EXCELLENT_RESULT = 'V01';
    public const string V_STRUGGLING       = 'V02';
    public const string V_STEADY_LEARNER  = 'V03';
    public const string V_UNFOCUSED        = 'V04';
    public const string V_MASTERY_BEGINNER = 'V05';
    public const string V_MASTERY_MEDIUM   = 'V06';
    public const string V_MASTERY_HARD     = 'V07';
    public const string V_HINT_DEPENDENT   = 'V08';
    public const string V_CRISIS_STATE     = 'V09';
    public const string V_BOREDOM_DETECTED = 'V10';

    public const array VIRTUAL_NAMES = [
        self::V_EXCELLENT_RESULT => 'Hasil Luar Biasa',
        self::V_STRUGGLING       => 'Sedang Kesulitan',
        self::V_STEADY_LEARNER  => 'Belajar dengan Teliti',
        self::V_UNFOCUSED        => 'Kurang Fokus',
        self::V_MASTERY_BEGINNER => 'Penguasaan Tingkat Pemula',
        self::V_MASTERY_MEDIUM   => 'Penguasaan Tingkat Menengah',
        self::V_MASTERY_HARD     => 'Penguasaan Tingkat Ahli',
        self::V_HINT_DEPENDENT   => 'Ketergantungan Bantuan',
        self::V_CRISIS_STATE     => 'Krisis Pembelajaran',
        self::V_BOREDOM_DETECTED => 'Potensi Kebosanan',
    ];
}
