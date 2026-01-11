<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 7: Critical Backtracking
 * IF ((G01 AND G16) OR (G01 AND G17)) THEN H07
 * 
 * Triggers when student has critical score on medium or advanced level.
 * Forces student to go back to easier material.
 */
class Rule07_CriticalBacktracking extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_07';
    protected string $ruleName = 'Critical Backtracking';
    protected string $actionCode = 'H07';
    protected int $priority = 25; // Medium-high priority
    
    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, 'G01')
            && $this->hasAnyFact($facts, ['G16', 'G17']);
    }
    
    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Review Dasar';
        $state['next_action'] = 'REDUCE_DIFFICULTY';
        $state['message'] = 'Soal ini sepertinya terlalu sulit sekarang. Mari turunkan tingkat kesulitan dan perkuat fondasi Anda.';
        
        return $state;
    }
}
