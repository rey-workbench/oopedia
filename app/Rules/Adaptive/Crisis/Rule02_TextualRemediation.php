<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 2: Textual Remediation
 * IF (G01 AND G08 AND NOT G22) THEN H02
 *
 * Triggers when student has critical score,
 * is a textual learner, and hasn't failed persistently yet.
 * Error type is irrelevant — any critical score warrants intervention.
 */
class Rule02_TextualRemediation extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_02';

    protected string $ruleName = 'Textual Remediation';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_REMEDIATION;

    protected int $priority = 10; // High priority (crisis)

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_CRITICAL,
            AdaptiveConstants::FACT_STYLE_TEXTUAL,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation']    = 'Ulas Materi';
        $state['next_action']       = 'STUDY_TEXTUAL';
        $state['message']           = 'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.';
        $state['intervention_type'] = 'textual_crisis';

        return $state;
    }
}
