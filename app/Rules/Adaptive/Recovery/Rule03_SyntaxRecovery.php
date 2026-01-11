<?php

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 3: Syntax Recovery
 * IF (G02 AND G09 AND G16 AND G12) THEN H03
 * 
 * Triggers when student has remedial score on medium level,
 * made syntax errors, and used hints.
 */
class Rule03_SyntaxRecovery extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_03';
    protected string $ruleName = 'Syntax Recovery';
    protected string $actionCode = 'H03';
    protected int $priority = 20; // Medium-high priority
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G02', 'G09', 'G16', 'G12']);
    }
    
    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Latihan Sintaksis';
        $state['next_action'] = 'REDUCE_DIFFICULTY';
        $state['message'] = 'Sepertinya Anda butuh penguatan sintaks dasar. Mari coba soal yang lebih mudah dengan fokus pada penulisan kode.';
        $state['recovery_type'] = 'syntax';
        
        return $state;
    }
}
