<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 14: Persistent Visual Safety Net
 * IF (G22 AND G07) THEN H14
 */
class PersistentVisualSafetyNet extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_14';

    protected string $ruleName = 'Persistent Visual Safety Net';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;

    protected int $priority = 5; // Highest priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_VISUAL);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation']        = 'Bantuan Komprehensif';
        $state['next_action']           = 'STUDY_VISUAL';
        $state['message']               = 'Anda mengalami kesulitan signifikan. Mari kita ulas materi secara menyeluruh.';
        $state['intervention_type']     = 'persistent_visual_safety';
        $state['force_material_review'] = true;

        return $state;
    }
}
