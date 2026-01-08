<?php

namespace App\Services;

use App\Rules\Personalization\FastTrackRule;
use App\Rules\Personalization\FatigueDetectionRule;

/**
 * PersonalizationRulesService
 * 
 * Simple service to evaluate personalization rules
 */
class PersonalizationRulesService
{
    protected $rules = [];

    public function __construct()
    {
        // Register all personalization rules
        $this->rules = [
            new FastTrackRule(),
            new FatigueDetectionRule(),
        ];
    }

    /**
     * Evaluate all rules using FORWARD CHAINING
     * Rules are re-evaluated iteratively until no new rules fire (convergence)
     */
    public function evaluate(array $state, bool $isCorrect, bool $usedHint): array
    {
        $state['is_correct'] = $isCorrect;
        $state['used_hint'] = $usedHint;

        $triggeredRules = [];
        $allActions = [];
        $iteration = 0;
        $maxIterations = 10; // Prevent infinite loops
        $hasChanges = true;

        // FORWARD CHAINING: Keep evaluating until no new rules fire
        while ($hasChanges && $iteration < $maxIterations) {
            $iteration++;
            $hasChanges = false;

            foreach ($this->rules as $rule) {
                $ruleName = $rule->getName();
                
                // Skip if rule already triggered
                if (in_array($ruleName, $triggeredRules)) {
                    continue;
                }
                
                // Evaluate rule
                $result = $rule->evaluate($state);
                
                if ($result !== null) {
                    // Rule fired!
                    $triggeredRules[] = $ruleName;
                    $hasChanges = true;
                    
                    // Apply actions to state
                    foreach ($result['actions'] as $action) {
                        $key = $action['key'];
                        $operator = $action['operator'];
                        $value = $action['value'];
                        
                        $oldValue = $state[$key] ?? 0;
                        
                        switch ($operator) {
                            case '=':
                                $state[$key] = $value;
                                break;
                            case '+':
                                $state[$key] = ($state[$key] ?? 0) + $value;
                                break;
                            case '-':
                                $state[$key] = ($state[$key] ?? 0) - $value;
                                break;
                        }
                        
                        $allActions[] = [
                            'iteration' => $iteration,
                            'rule' => $ruleName,
                            'key' => $key,
                            'old_value' => $oldValue,
                            'new_value' => $state[$key],
                            'message' => $action['message'] ?? null
                        ];
                    }
                    
                    // Break to re-evaluate all rules with new state (forward chaining essence)
                    break;
                }
            }
        }

        return [
            'triggered_rules' => $triggeredRules,
            'new_state' => $state,
            'actions_report' => $allActions,
            'iterations' => $iteration,
            'converged' => !$hasChanges,
        ];
    }
}
