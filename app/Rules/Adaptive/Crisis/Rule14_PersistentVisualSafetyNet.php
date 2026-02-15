<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 14: Persistent Visual Safety Net
 * IF ((G01 OR G02) AND G22 AND G07) THEN H01
 * 
 * Triggers when student has failed ≥3 times and is a visual learner.
 * This is a safety net to prevent students from getting stuck.
 */
class Rule14_PersistentVisualSafetyNet extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_14';
    protected string $ruleName = 'Persistent Visual Safety Net';
    protected string $actionCode = 'H01';
    protected int $priority = 5; // Highest priority (persistent failure)

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, ['G01', 'G02'])
            && $this->hasFact($facts, 'G22')
            && $this->hasFact($facts, 'G07');
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Bantuan Komprehensif';
        $state['next_action'] = 'STUDY_MATERIAL';
        $state['message'] = 'Anda mengalami kesulitan signifikan. Mari kita ulas materi secara menyeluruh.';
        $state['intervention_type'] = 'persistent_visual_safety';
        $state['force_material_review'] = true;

        return $state;
    }
}
