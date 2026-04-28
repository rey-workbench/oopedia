<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class PedagogicalConstants
{
    // --- Layer 2: Thresholds (Aturan Numerik) ---
    public const float ACCURACY_CRISIS_THRESHOLD = 40.0;

    public const float ACCURACY_OPTIMAL_THRESHOLD = 80.0;

    public const float ACCURACY_CERTIFICATION_THRESHOLD = 85.0;

    public const int HELP_HIGH_THRESHOLD = 3;

    public const int STREAK_OPTIMAL_THRESHOLD = 3;

    public const int STREAK_BOREDOM_THRESHOLD = 5;

    public const int STREAK_CERTIFICATION_THRESHOLD = 7;

    public const float TREND_MARGIN = 5.0; // Margin toleransi ±5%

    // Baselines for Speed Diagnosis (> 2x baseline)
    public const array BASELINE_TIME = [
        'beginner' => 20,
        'medium'   => 40,
        'hard'     => 60,
    ];

    // Diagnosis and Recommendation layers are managed by FactConstants and ActionConstants respectively.
}
