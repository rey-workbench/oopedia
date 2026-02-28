<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 6: Accelerated Jump
 * IF (G04 AND G05 AND G11 AND G15) THEN H06
 *
 * Triggers when student has mastery score, answered fast,
 * didn't use hints, on easy level.
 * Allows student to skip to harder difficulty or finish early.
 */
class Rule06_AcceleratedJump extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_06';
    protected string $ruleName = 'Accelerated Jump';
    protected string $actionCode = AdaptiveConstants::ACTION_ACCELERATED_JUMP;
    protected int $priority = 40; // Medium priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_HINT_NONE,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
            AdaptiveConstants::FACT_NEXT_LOCKED,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['fast_track_active'] = true;
        $state['global_xp'] = ($state['global_xp'] ?? 0) + 50; // Bonus XP

        // Check if there are actually harder questions available
        $hasHarder = \App\Models\Question::where('material_id', '=', $context['material_id'])
            ->where('difficulty', '=', 'hard')
            ->exists();

        if ($hasHarder && ($context['difficulty'] ?? '') !== 'hard') {
            $state['next_action'] = 'INCREASE_DIFFICULTY';
            $state['message'] = 'Luar biasa! Akurasi dan kecepatan Anda tinggi. Kami memberikan tantangan yang lebih menantang untuk mempercepat progres Anda.';
        } else {
            // If already at hard or no harder questions, just move to finish to graduate
            $state['next_action'] = 'FINISH_MATERIAL';
            $state['message'] = 'Performa Anda sempurna! Modul ini telah selesai lebih awal karena penguasaan materi Anda yang sangat baik.';
        }

        return $state;
    }
}
