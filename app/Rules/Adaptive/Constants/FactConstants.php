<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class FactConstants
{
    // --- G-codes (Numerical Thresholds) ---
    public const string ACCURACY_CRISIS = 'G01';
    public const string ACCURACY_STRUGGLE = 'G02';
    public const string ACCURACY_STABLE = 'G03';
    public const string ACCURACY_PROGRESSING = 'G18';
    public const string ACCURACY_OPTIMAL = 'G04';
    public const string ACCURACY_EXCELLENT = 'G17';

    public const string HELP_HIGH = 'G08';
    public const string HELP_MED = 'G09';
    public const string HELP_NONE = 'G20';

    public const string TREND_DOWN = 'G05';
    public const string TREND_STABLE = 'G06';
    public const string TREND_UP = 'G07';

    public const string TIME_FAST = 'G11';
    public const string TIME_SLOW = 'G12';
    public const string TIME_NORMAL = 'G13';

    public const string STREAK_3D = 'G14';
    public const string STREAK_5D = 'G15';
    public const string STREAK_7D = 'G16';

    public const string LEVEL_AHLI = 'G19';

    // --- V-codes (Diagnosis) ---
    public const string V_CRISIS = 'V01';
    public const string V_STRUGGLING = 'V02';
    public const string V_OPTIMAL = 'V03';
    public const string V_DEPENDENCY = 'V04';
    public const string V_BOREDOM = 'V05';

    public const array NAMES = [
        self::ACCURACY_CRISIS      => 'Akurasi <40%',
        self::ACCURACY_STRUGGLE    => 'Akurasi 40-60%',
        self::ACCURACY_STABLE      => 'Akurasi 60-70%',
        self::ACCURACY_PROGRESSING => 'Akurasi 70-80%',
        self::ACCURACY_OPTIMAL     => 'Akurasi >80%',
        self::ACCURACY_EXCELLENT   => 'Akurasi >85%',
        self::TREND_DOWN           => 'Tren Turun',
        self::TREND_STABLE         => 'Tren Stabil',
        self::TREND_UP             => 'Tren Naik',
        self::HELP_HIGH            => 'Bantuan >3x',
        self::HELP_MED             => 'Bantuan 2-3x',
        self::HELP_NONE            => 'Bantuan 0x',
        self::TIME_FAST            => 'Respon Cepat',
        self::TIME_SLOW            => 'Respon Lambat',
        self::TIME_NORMAL          => 'Respon Normal',
        self::STREAK_3D            => 'Streak >=3 Hari',
        self::STREAK_5D            => 'Streak >=5 Hari',
        self::STREAK_7D            => 'Streak >=7 Hari',
        self::LEVEL_AHLI           => 'Level Ahli',
    ];

    public const array VIRTUAL_NAMES = [
        self::V_CRISIS     => 'Krisis Pembelajaran',
        self::V_STRUGGLING => 'Sedang Kesulitan',
        self::V_OPTIMAL    => 'Performa Optimal',
        self::V_DEPENDENCY => 'Ketergantungan Bantuan',
        self::V_BOREDOM    => 'Potensi Kebosanan',
    ];

    /**
     * Get the JSON logic definition for a given G-code.
     */
    public static function getLogic(string $id): ?array
    {
        $keys = AdaptiveConditionKeys::class;

        return match ($id) {
            self::ACCURACY_CRISIS      => [$keys::KEY => $keys::ACCURACY, $keys::OP => $keys::OP_LT, $keys::VAL => 40],
            self::ACCURACY_STRUGGLE    => [$keys::KEY => $keys::ACCURACY, $keys::OP => $keys::OP_LTE, $keys::VAL => 60],
            self::ACCURACY_STABLE      => [$keys::KEY => $keys::ACCURACY, $keys::OP => $keys::OP_LTE, $keys::VAL => 70],
            self::ACCURACY_PROGRESSING => [$keys::KEY => $keys::ACCURACY, $keys::OP => $keys::OP_LTE, $keys::VAL => 80],
            self::ACCURACY_OPTIMAL     => [$keys::KEY => $keys::ACCURACY, $keys::OP => $keys::OP_GT, $keys::VAL => 80],
            self::ACCURACY_EXCELLENT   => [$keys::KEY => $keys::ACCURACY, $keys::OP => $keys::OP_GT, $keys::VAL => 85],

            self::HELP_HIGH => [$keys::KEY => $keys::HINTS, $keys::OP => $keys::OP_GT, $keys::VAL => 3],
            self::HELP_MED  => [$keys::KEY => $keys::HINTS, $keys::OP => $keys::OP_GTE, $keys::VAL => 2],
            self::HELP_NONE => [$keys::KEY => $keys::HINTS, $keys::OP => $keys::OP_EQ, $keys::VAL => 0],

            self::TREND_DOWN   => [$keys::KEY => $keys::TREND, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::TREND_DOWN],
            self::TREND_STABLE => [$keys::KEY => $keys::TREND, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::TREND_STABLE],
            self::TREND_UP     => [$keys::KEY => $keys::TREND, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::TREND_UP],

            self::TIME_FAST   => [$keys::KEY => $keys::SPEED, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::SPEED_FAST],
            self::TIME_SLOW   => [$keys::KEY => $keys::SPEED, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::SPEED_SLOW],
            self::TIME_NORMAL => [$keys::KEY => $keys::SPEED, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::SPEED_NORMAL],

            self::STREAK_3D => [$keys::KEY => $keys::STREAK, $keys::OP => $keys::OP_GTE, $keys::VAL => 3],
            self::STREAK_5D => [$keys::KEY => $keys::STREAK, $keys::OP => $keys::OP_GTE, $keys::VAL => 5],
            self::STREAK_7D => [$keys::KEY => $keys::STREAK, $keys::OP => $keys::OP_GTE, $keys::VAL => 7],

            self::LEVEL_AHLI => [$keys::KEY => $keys::LEVEL, $keys::OP => $keys::OP_EQ, $keys::VAL => $keys::LEVEL_AHLI],
            
            default => null,
        };
    }
}
