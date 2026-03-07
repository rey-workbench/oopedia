<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 20: Accelerated Material Promotion (Gating)
 * IF (G04 AND G05 AND (NOT G12) AND G20) THEN H16
 *
 * Triggers when student has mastery score, answers fast,
 * used no hints, and the NEXT material is already unlocked.
 * Higher priority than difficulty jump (AcceleratedJump).
 */
class AcceleratedMaterialPromotion extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_20';

    protected string $ruleName = 'Accelerated Material Promotion';

    protected string $actionCode = AdaptiveConstants::ACTION_ACCELERATED_MATERIAL_PROMOTION;

    protected int $priority = 35; // Higher priority than RULE_06 (40)

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_NEXT_UNLOCKED,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['fast_track_active'] = true;
        $state['next_action'] = 'NEXT_MATERIAL';
        $state['message'] = 'Luar biasa! Penguasaan materi Anda sangat baik. Mari langsung lanjut ke materi berikutnya!';

        return $state;
    }
}
