<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 15: Persistent Textual Safety Net
 * IF (G22 AND G08) THEN H15
 */
class PersistentTextualSafetyNet extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_15';

    protected string $ruleName = 'Persistent Textual Safety Net';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_CRISIS_INTERVENTION;

    protected int $priority = 5; // Highest priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_TEXTUAL)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Bantuan Komprehensif';
        $state['next_action'] = 'STUDY_TEXTUAL';
        $state['message'] = 'Anda mengalami kesulitan signifikan. Mari kita ulas materi secara menyeluruh.';
        $state['intervention_type'] = 'persistent_textual_safety';
        $state['force_material_review'] = true;

        return $state;
    }
}
