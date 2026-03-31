<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_PersistentTextualSafetyNet extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

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
        return $this->applyPersistentTextualSafety(
            $state,
            'Anda mengalami kesulitan signifikan. Mari kita ulas materi secara menyeluruh.',
        );
    }
}
