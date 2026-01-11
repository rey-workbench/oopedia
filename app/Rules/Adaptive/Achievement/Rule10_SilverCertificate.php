<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 10: Silver Certificate
 * IF (G18 AND G03 AND G11) THEN H10
 * 
 * Triggers when student completes final project with standard score
 * and didn't use hints.
 */
class Rule10_SilverCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_10';
    protected string $ruleName = 'Silver Certificate';
    protected string $actionCode = 'H10';
    protected int $priority = 20; // High priority (achievement)
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G18', 'G03', 'G11']);
    }
    
    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'ISSUE_CERTIFICATE';
        $state['message'] = 'Selamat! Anda layak mendapatkan Sertifikat PERAK sebagai Object-Oriented Developer.';
        $state['certification'] = 'silver';
        $state['achievement'] = 'silver_certificate';
        
        // Add badge
        $badges = $state['gamification_data']['badges'] ?? [];
        $badges[] = 'silver_developer';
        $state['gamification_data']['badges'] = $badges;
        
        return $state;
    }
}
