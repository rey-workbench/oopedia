<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 7: Critical Backtracking
 * IF (G01 AND (G16 OR G17) AND NOT G22) THEN H07
 *
 * Triggers when student has critical score on medium or advanced level,
 * and has NOT reached persistent failure (G22 — stuck).
 * When G22 is present, Safety Net rules (R14/R15) take over at priority 5.
 */
class CriticalBacktracking extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_07';

    protected string $ruleName = 'Critical Backtracking';

    protected string $actionCode = AdaptiveConstants::ACTION_CRITICAL_BACKTRACKING;

    protected int $priority = 27;

    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_CRITICAL)
            && $this->hasAnyFact($facts, [AdaptiveConstants::FACT_DIFF_MEDIUM, AdaptiveConstants::FACT_DIFF_HARD])
            && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Review Dasar';
        $state['next_action']    = 'REDUCE_DIFFICULTY';
        $state['message']        = 'Soal ini sepertinya terlalu sulit sekarang. Mari turunkan tingkat kesulitan dan perkuat fondasi Anda.';

        return $state;
    }
}
