<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 6: Accelerated Jump
 * IF (G04 AND G05 AND G11 AND G15 AND G19) THEN H06
 *
 * Triggers when student has mastery score, answers fast (G05),
 * no hints, on easy level, and next material is still locked.
 */
class AcceleratedJump extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_06';

    protected string $ruleName = 'Accelerated Jump';

    protected string $actionCode = AdaptiveConstants::ACTION_ACCELERATED_JUMP;

    protected int $priority = 40;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_HINT_NONE,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
            AdaptiveConstants::FACT_NEXT_LOCKED,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['fast_track_active'] = true;
        // The rule dictates that the next difficulty should be medium
        $state['target_difficulty'] = 'medium';
        $state['next_action'] = 'NEXT_QUESTION';
        $state['message'] = 'Luar biasa! Penguasaan dan kecepatan Anda sangat baik. Lanjutkan ke level menengah.';

        return $state;
    }
}
