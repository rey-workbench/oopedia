<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Support\Facades\Log;

use App\Traits\Adaptive\EvaluatesAdaptiveConditions;

final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    use EvaluatesAdaptiveConditions;

    /**
     * Core Engine: Evaluates student state against DB-driven rules and facts.
     */
    public function evaluate(array $state): array
    {
        try {
            // 1. Generate Active Facts dynamically from DB
            $activeFacts = $this->generateActiveFacts($state);

            // 2. Fetch Active Rules from DB (ordered by priority)
            $rules = AdaptiveRule::where('is_active', true)
                ->ordered()
                ->get();

            // 3. Pattern Matching Logic
            foreach ($rules as $rule) {
                if (!$rule instanceof AdaptiveRule) {
                    continue;
                }

                $required = $rule->required_fact_ids ?? [];

                // IF (All required facts from DB are present in active student facts)
                if ($this->isRuleSatisfied($required, $activeFacts)) {
                    return $this->formatResponse($rule, $activeFacts);
                }
            }

            // 4. Ultimate Fallback (R14) if no rule matched
            $fallback = AdaptiveRule::where('id', 'R14')->first();
            
            if ($fallback instanceof AdaptiveRule) {
                return $this->formatResponse($fallback, $activeFacts);
            }

            return $this->hardcodedFallback($activeFacts);

        } catch (\Exception $e) {
            Log::error("Adaptive Engine Error: " . $e->getMessage(), [
                'state' => $state,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->hardcodedFallback([]);
        }
    }

    /**
     * Evaluator: Maps raw state values to Fact G-Codes based on DB logic.
     */
    private function generateActiveFacts(array $state): array
    {
        $activeFacts = [];
        
        // Fetch primary fact definitions that have logic
        $factDefinitions = AdaptiveFact::where('category', 'primary')->get();

        foreach ($factDefinitions as $fact) {
            $formula = json_decode($fact->description ?? '', true);

            // If no formula (like for manual/complex facts), handle separately if needed
            if (!$formula || !isset($formula[AdaptiveConditionKeys::KEY])) {
                continue;
            }

            // Use data_get to support dot notation (e.g., performance_metrics.trend)
            $currentValue = data_get($state, $formula[AdaptiveConditionKeys::KEY]);
            $threshold = $formula[AdaptiveConditionKeys::VAL];
            $operator = $formula[AdaptiveConditionKeys::OP];

            if ($this->evaluateCondition($currentValue, $operator, $threshold)) {
                $activeFacts[] = $fact->id; // G01, G02, etc.
            }
        }

        return array_unique($activeFacts);
    }

    /**
     * Checks if all required facts are present in active facts (AND logic).
     */
    private function isRuleSatisfied(array $required, array $active): bool
    {
        if (empty($required)) {
            return true; // Fallback rule usually has empty requirements
        }

        // Check if every required fact exists in the active facts array
        return count(array_intersect($required, $active)) === count($required);
    }

    /**
     * Standardized response format from DB record.
     */
    private function formatResponse(AdaptiveRule $rule, array $activeFacts): array
    {
        return [
            'id'              => $rule->id,
            'diagnosis'       => $rule->name,
            'recommendation'  => $rule->recommendation, // Renamed from domain
            'recommendations' => $rule->action_ids,
            'facts'           => $activeFacts,
            'deduced_facts'   => $rule->deduced_fact_ids,
            'timestamp'       => now()->toIso8601String(),
            'engine_metadata' => [
                'engine_version' => '3.1.0-full-db',
                'priority'       => $rule->priority,
            ]
        ];
    }

    /**
     * Emergency fallback if DB is empty or unreachable.
     */
    private function hardcodedFallback(array $activeFacts): array
    {
        return [
            'id'              => 'ERR-FALLBACK',
            'diagnosis'       => 'Normal Learning',
            'recommendation'  => 'Tetap konsisten dalam belajar!',
            'recommendations' => ['FEEDBACK'],
            'facts'           => $activeFacts,
            'deduced_facts'   => [],
            'timestamp'       => now()->toIso8601String(),
        ];
    }
}
