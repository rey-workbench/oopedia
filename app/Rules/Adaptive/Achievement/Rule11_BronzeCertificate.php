<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 11: Bronze Certificate
 * IF (G18 AND (G03 OR G04) AND G12) THEN H11
 * 
 * Triggers when student completes final project with passing score
 * but used hints.
 */
class Rule11_BronzeCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_11';
    protected string $ruleName = 'Bronze Certificate';
    protected string $actionCode = 'H11';
    protected int $priority = 20; // High priority (achievement)
    
    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, 'G18')
            && $this->hasAnyFact($facts, ['G03', 'G04'])
            && $this->hasFact($facts, 'G12');
    }
    
    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'ISSUE_CERTIFICATE';
        $state['message'] = 'Bagus! Anda layak mendapatkan Sertifikat PERUNGGU sebagai Junior Object-Oriented Programmer.';
        $state['certification'] = 'bronze';
        $state['achievement'] = 'bronze_certificate';
        
        // Add badge
        $badges = $state['gamification_data']['badges'] ?? [];
        $badges[] = 'bronze_junior';
        $state['gamification_data']['badges'] = $badges;
        
        return $state;
    }
}
