<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 8: Module Graduation
 * IF (G04 AND G05 AND G11 AND G17 AND (G13..G25)) THEN H08
 * 
 * Triggers when student completes advanced question with mastery,
 * fast, no hints, in any of the 5 modules.
 */
class Rule08_ModuleGraduation extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_08';
    protected string $ruleName = 'Module Graduation';
    protected string $actionCode = 'H08';
    protected int $priority = 30; // Medium priority

    public function evaluate(array $facts): bool
    {
        $modulesFacts = ['G13', 'G14', 'G23', 'G24', 'G25'];

        return $this->hasAllFacts($facts, ['G04', 'G05', 'G11', 'G17'])
            && $this->hasAnyFact($facts, $modulesFacts);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'FINISH_MATERIAL';
        $state['message'] = 'Selamat! Anda telah menguasai seluruh materi modul ini dengan sempurna.';
        $state['achievement'] = 'module_completed';

        // Update module progress
        if (isset($context['module_id'])) {
            $moduleProgress = $state['adaptive_state']['module_progress'] ?? [];
            $moduleProgress[$context['module_id']] = 100;
            $state['adaptive_state']['module_progress'] = $moduleProgress;
        }

        return $state;
    }
}
