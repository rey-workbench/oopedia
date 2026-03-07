<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 16: Mastery Medium
 * IF (G04 AND G05 AND G11 AND G16) THEN NEXT_QUESTION
 *
 * Triggers when student achieves mastery score with fast response (G05)
 * on a medium-level question without using hints.
 * Consistent with R06 (easy) and R08 (advanced) time requirements.
 */
class MasteryMedium extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_16';

    protected string $ruleName = 'Mastery Medium';

    protected string $actionCode = AdaptiveConstants::ACTION_STANDARD_PROMOTION;

    protected int $priority = 35;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_HINT_NONE,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['fast_track_active'] = true;
        $state['target_difficulty'] = 'hard';
        $state['next_action'] = 'NEXT_QUESTION';
        $state['message'] = 'Luar biasa! Penguasaan dan kecepatan Anda di level menengah sangat baik. Lanjutkan ke level sulit (Hard).';

        return $state;
    }
}
