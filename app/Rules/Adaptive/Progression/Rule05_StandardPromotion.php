<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 5: Standard Promotion
 * IF (G03 AND G11 AND (G15 OR G16)) THEN H05
 * 
 * Triggers when student passes with standard score,
 * didn't use hints, on easy or medium level.
 */
class Rule05_StandardPromotion extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_05';
    protected string $ruleName = 'Standard Promotion';
    protected string $actionCode = 'H05';
    protected int $priority = 50; // Normal priority
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G03', 'G11'])
            && $this->hasAnyFact($facts, ['G15', 'G16']);
    }
    
    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'NEXT_QUESTION';
        $state['message'] = $context['is_correct']
            ? 'Jawaban tepat! Mari lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi atau ulas kembali materi jika kesulitan.';
        
        return $state;
    }
}
