<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 9: Gold Certificate
 * IF (G18 AND G04 AND G11) THEN H09
 * 
 * Triggers when student completes final project with mastery
 * and didn't use hints.
 */
class Rule09_GoldCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_09';
    protected string $ruleName = 'Gold Certificate';
    protected string $actionCode = 'H09';
    protected int $priority = 20; // High priority (achievement)
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G18', 'G04', 'G11']);
    }
    
    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'ISSUE_CERTIFICATE';
        $state['message'] = 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS sebagai Object-Oriented Architect.';
        $state['certification'] = 'gold';
        $state['achievement'] = 'gold_certificate';
        
        // Add badge
        $badges = $state['gamification_data']['badges'] ?? [];
        $badges[] = 'gold_architect';
        $state['gamification_data']['badges'] = $badges;
        
        return $state;
    }
}
