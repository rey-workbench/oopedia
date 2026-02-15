<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 6: Accelerated Jump
 * IF (G04 AND G05 AND G11 AND G15 AND G19) THEN H06
 * 
 * Triggers when student has mastery score, answered fast,
 * didn't use hints, on easy level, and next material is locked.
 * Allows student to skip ahead.
 */
class Rule06_AcceleratedJump extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_06';
    protected string $ruleName = 'Accelerated Jump';
    protected string $actionCode = 'H06';
    protected int $priority = 40; // Medium priority
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G04', 'G05', 'G11', 'G15', 'G19']);
    }
    
    public function apply(array $state, array $context): array
    {
        $state['fast_track_active'] = true;
        $state['global_xp'] = ($state['global_xp'] ?? 0) + 50; // Bonus XP
        $state['next_action'] = 'NEXT_MATERIAL';
        $state['message'] = 'Luar biasa! Akurasi dan kecepatan Anda tinggi. Anda siap melanjutkan ke materi berikutnya.';
        
        return $state;
    }
}
