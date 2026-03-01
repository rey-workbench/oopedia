<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 8: Module Graduation
 * IF ((G03 OR G04) AND G11 AND (G16 OR G17) AND (G26 OR G18) AND (G13..G25)) THEN H08
 *
 * Triggers when student completes a medium/hard question with standard or mastery score,
 * no hints, in any of the 5 modules, and has satisfactory progress.
 */
class Rule08_ModuleGraduation extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_08';

    protected string $ruleName = 'Module Graduation';

    protected string $actionCode = AdaptiveConstants::ACTION_MODULE_GRADUATION;

    protected int $priority = 30; // Medium priority

    public function evaluate(array $facts): bool
    {
        $modulesFacts = [
            AdaptiveConstants::FACT_MODULE_FOUNDATION,
            AdaptiveConstants::FACT_MODULE_ENCAPSULATION,
            AdaptiveConstants::FACT_MODULE_INHERITANCE,
            AdaptiveConstants::FACT_MODULE_POLYMORPHISM,
            AdaptiveConstants::FACT_MODULE_ABSTRACTION,
        ];

        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_HINT_NONE,
            AdaptiveConstants::FACT_DIFF_HARD,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ]) && ($this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_SCORE_STANDARD,
            AdaptiveConstants::FACT_SCORE_MASTERY,
        ])) && $this->hasAnyFact($facts, $modulesFacts);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'FINISH_MATERIAL';
        $state['message']     = 'Selamat! Anda telah menguasai seluruh materi modul ini dengan sempurna.';
        $state['achievement'] = 'module_completed';

        // Update module progress
        if (isset($context['module_id'])) {
            $moduleProgress                             = $state['adaptive_state']['module_progress'] ?? [];
            $moduleProgress[$context['module_id']]      = 100;
            $state['adaptive_state']['module_progress'] = $moduleProgress;
        }

        return $state;
    }
}
