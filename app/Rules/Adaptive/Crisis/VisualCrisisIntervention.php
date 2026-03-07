<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 1: Visual Crisis Intervention
 * IF (G01 AND G07 AND NOT G22) THEN H01
 *
 * Triggers when student has critical score,
 * is a visual learner, and hasn't failed persistently yet.
 * Error type is irrelevant — any critical score warrants intervention.
 */
class VisualCrisisIntervention extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_01';

    protected string $ruleName = 'Visual Crisis Intervention';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;

    protected int $priority = 10; // Very high priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_CRITICAL,
            AdaptiveConstants::FACT_STYLE_VISUAL,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Ulas Materi';
        $state['next_action'] = 'STUDY_VISUAL';
        $state['message'] = 'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.';
        $state['intervention_type'] = 'visual_crisis';

        return $state;
    }
}
