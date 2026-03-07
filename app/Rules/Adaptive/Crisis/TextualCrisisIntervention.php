<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 2: Textual Crisis Intervention
 * IF (G01 AND G08 AND NOT G22) THEN H02
 *
 * Triggers when student has critical score,
 * is a textual learner, and hasn't failed persistently yet.
 * Error type is irrelevant — any critical score warrants intervention.
 */
class TextualCrisisIntervention extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_02';

    protected string $ruleName = 'Textual Crisis Intervention';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_CRISIS_INTERVENTION;

    protected int $priority = 10; // High priority (crisis)

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_CRITICAL,
            AdaptiveConstants::FACT_STYLE_TEXTUAL,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Ulas Materi';
        $state['next_action'] = 'STUDY_TEXTUAL';
        $state['message'] = 'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.';
        $state['intervention_type'] = 'textual_crisis';

        return $state;
    }
}
