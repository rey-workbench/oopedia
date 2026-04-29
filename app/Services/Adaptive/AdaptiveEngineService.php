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
     * Core Engine: True Forward Chaining with Iterative Evaluation.
     */
    public function evaluate(array $state): array
    {
        try {
            // 1. Initial Fact Generation from State
            $activeFacts = $this->generateActiveFacts($state);
            
            $appliedRules = [];
            $appliedRuleIds = [];
            $maxIterations = 10;
            $iteration = 0;

            // Fetch all active rules ordered by priority
            $rules = AdaptiveRule::where('is_active', true)->ordered()->get();

            // 2. Forward Chaining Loop
            while ($iteration < $maxIterations) {
                $iteration++;
                $newFactAdded = false;

                foreach ($rules as $rule) {
                    // Skip if already applied to prevent infinite loops
                    if (in_array($rule->id, $appliedRuleIds)) {
                        continue;
                    }

                    $required = $rule->required_fact_ids ?? [];
                    
                    // Skip fallback rules (empty requirements) during chaining
                    // They will be handled if the chain is empty
                    if (empty($required)) {
                        continue;
                    }

                    // IF (All required facts are present) -> FIRE RULE
                    if ($this->isRuleSatisfied($required, $activeFacts)) {
                        $appliedRules[] = $rule;
                        $appliedRuleIds[] = $rule->id;

                        // Inject Deduced Facts back into the pool
                        if (!empty($rule->deduced_fact_ids)) {
                            foreach ($rule->deduced_fact_ids as $factId) {
                                if (!in_array($factId, $activeFacts)) {
                                    $activeFacts[] = $factId;
                                    $newFactAdded = true;
                                }
                            }
                        }
                    }
                }

                // If no new facts were added in this pass, the engine has stabilized
                if (!$newFactAdded) {
                    break;
                }
            }

            // 3. Fallback Handling (if no rules fired)
            if (empty($appliedRules)) {
                $fallback = AdaptiveRule::where('id', 'R14')->first();
                if ($fallback instanceof AdaptiveRule) {
                    return $this->formatResponse([$fallback], $activeFacts, $iteration);
                }
                return $this->hardcodedFallback($activeFacts);
            }

            // 4. Return formatted response with the entire inference chain
            return $this->formatResponse($appliedRules, $activeFacts, $iteration);

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

            if (!$formula || !isset($formula[AdaptiveConditionKeys::KEY])) {
                continue;
            }

            $currentValue = data_get($state, $formula[AdaptiveConditionKeys::KEY]);
            $threshold = $formula[AdaptiveConditionKeys::VAL];
            $operator = $formula[AdaptiveConditionKeys::OP];

            if ($this->evaluateCondition($currentValue, $operator, $threshold)) {
                $activeFacts[] = $fact->id; 
            }
        }

        return array_unique($activeFacts);
    }

    private function isRuleSatisfied(array $required, array $active): bool
    {
        if (empty($required)) {
            return false; 
        }
        return count(array_intersect($required, $active)) === count($required);
    }

    /**
     * Standardized response format aggregating the chain of decisions.
     */
    private function formatResponse(array $appliedRules, array $activeFacts, int $iterations): array
    {
        // The last rule in the chain is usually the most specific diagnosis
        $finalRule = end($appliedRules);
        
        // Aggregate all actions from the chain
        $allActionIds = [];
        foreach ($appliedRules as $rule) {
            $allActionIds = array_merge($allActionIds, $rule->action_ids ?? []);
        }

        return [
            'id'              => $finalRule->id,
            'diagnosis'       => $finalRule->name,
            'recommendation'  => $finalRule->recommendation,
            'recommendations' => array_values(array_unique($allActionIds)),
            'facts'           => $activeFacts,
            'deduced_facts'   => $finalRule->deduced_fact_ids,
            'timestamp'       => now()->toIso8601String(),
            'engine_metadata' => [
                'engine_version' => '4.0.0-forward-chaining',
                'iterations'     => $iterations,
                'rule_chain'     => array_column($appliedRules, 'id'),
                'priority'       => $finalRule->priority,
            ]
        ];
    }

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
