<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class PedagogicalConstants
{
    // --- Layer 2: Thresholds (Aturan Numerik) ---
    public const float ACCURACY_CRISIS_THRESHOLD = 40.0;

    public const float ACCURACY_OPTIMAL_THRESHOLD = 80.0;

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

    // --- Diagnosis (Layer 3) ---
    public const string DIAG_CRISIS = 'crisis';

    public const string DIAG_STRUGGLING = 'struggling';

    public const string DIAG_OPTIMAL = 'optimal';

    public const string DIAG_DEPENDENCY = 'dependency';

    public const string DIAG_BOREDOM = 'boredom';

    public const string DIAG_DEFAULT = 'default';

    // --- Recommendations (Layer 4) ---
    public const string REC_REMEDIAL = 'remedial_review';

    public const string REC_REDUCE_DIFF = 'reduce_difficulty';

    public const string REC_INCREASE_DIFF = 'increase_difficulty';

    public const string REC_SCAFFOLD_REDUCTION = 'scaffold_reduction';

    public const string REC_NEW_CHALLENGE = 'new_challenge';

    public const string REC_STREAK_BONUS = 'streak_bonus';

    public const string REC_CERTIFICATION = 'grant_certification';

    public const string REC_GENERAL_FEEDBACK = 'general_feedback';
}
