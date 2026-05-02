<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class PedagogicalConstants
{
    // --- Layer 2: Thresholds (Aturan Numerik) ---
    public const float TREND_MARGIN = 5.0; // Margin toleransi ±5%

    // Baselines for Speed Diagnosis (> 2x baseline)
    public const array BASELINE_TIME = [
        'beginner' => 20,
        'medium'   => 40,
        'hard'     => 60,
    ];

    // Fact, Action, and Rule layers are now managed by the database (AdaptiveFact, AdaptiveAction, AdaptiveRule).
}
