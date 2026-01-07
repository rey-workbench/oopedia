<?php

namespace App\Services;

use App\Models\AdaptiveRule;
use App\Models\AttributeDefinition;
use App\Models\Question;
use App\Models\Progress;
use Illuminate\Support\Facades\Log;

class AdaptiveQuizService
{
    protected $formulaEngine;
    protected $attrManager;

    public function __construct(FormulaEngine $formulaEngine, AttributeManager $attrManager)
    {
        $this->formulaEngine = $formulaEngine;
        $this->attrManager = $attrManager;
    }

    public function processAttempt($userId, $materialId, Question $question, $isCorrect, $usedHint = false)
    {
        // 1. Fetch Previous State
        $latestProgress = Progress::where('user_id', $userId)
            ->where('material_id', $materialId)
            ->latest()
            ->first();
        
        // Load attributes or initialize defaults
        $currentAttributes = $latestProgress ? ($latestProgress->attributes ?? []) : [];
        $state = $this->attrManager->mergeWithDefaults($currentAttributes);

        // 2. Set context flags for rule evaluation
        $state['is_correct'] = $isCorrect;
        $state['used_hint'] = $usedHint;

        // 3. Calculate Computed Attributes using Formulas
        $computedAttrs = AttributeDefinition::where('is_computed', true)
            ->where('is_active', true)
            ->with('formula')
            ->get();
        
        foreach ($computedAttrs as $attr) {
            if ($attr->formula) {
                $state[$attr->key] = $this->formulaEngine->evaluate(
                    $attr->formula,
                    $state,
                    $userId,
                    $materialId
                );
            }
        }

        // 4. Evaluate Dynamic Rules (ALL logic from database)
        $rules = AdaptiveRule::where('is_active', true)
            ->where(function($q) use ($materialId) {
                $q->whereNull('material_id')->orWhere('material_id', $materialId);
            })
            ->orderBy('priority', 'desc')
            ->get();

        $actionsReport = [];
        $xpEarned = 0;
        $pointsEarned = 0;

        foreach ($rules as $rule) {
            // Check Conditions
            if ($this->evaluateConditions($rule->conditions, $state)) {
                // Apply Actions
                $actions = $rule->getFormattedActions();
                $report = $this->applyActions($actions, $state, $xpEarned, $pointsEarned);
                $actionsReport = array_merge($actionsReport, $report);
                
                Log::info("Rule applied: {$rule->name}", [
                    'user_id' => $userId,
                    'material_id' => $materialId,
                    'rule_id' => $rule->id,
                    'actions' => $report
                ]);
            }
        }

        // 5. Return results
        return [
            'status' => 'success',
            'is_correct' => $isCorrect,
            'xp_earned' => $xpEarned,
            'points_earned' => $pointsEarned,
            'new_state' => $state,
            'actions_applied' => $actionsReport,
            'level_changed' => in_array('current_level', array_column($actionsReport, 'key'))
        ];
    }

    /**
     * Evaluate conditions (AND logic)
     */
    private function evaluateConditions($conditions, $state)
    {
        if (empty($conditions)) return false;
        
        foreach ($conditions as $cond) {
            // Support both 'key' and 'type' for backward compatibility
            $key = $cond['key'] ?? $cond['type'] ?? null;
            if (!$key) continue;
            
            $operator = $cond['operator'];
            $expectedValue = $cond['value'];
            
            $currentValue = $state[$key] ?? null;
            
            if (!$this->compare($currentValue, $operator, $expectedValue)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Compare values with operator
     */
    private function compare($a, $op, $b)
    {
        // Handle boolean string comparisons
        if ($b === 'true') $b = true;
        if ($b === 'false') $b = false;
        if ($a === 'true') $a = true;
        if ($a === 'false') $a = false;
        
        // Handle string comparisons
        if (is_string($a) || is_string($b)) {
            return match($op) {
                '==' => $a == $b,
                '!=' => $a != $b,
                default => false
            };
        }
        
        // Numeric comparisons
        return match($op) {
            '>' => $a > $b,
            '>=' => $a >= $b,
            '<' => $a < $b,
            '<=' => $a <= $b,
            '==' => $a == $b,
            '!=' => $a != $b,
            default => false
        };
    }

    /**
     * Apply actions to state
     */
    private function applyActions($actions, &$state, &$xpEarned, &$pointsEarned)
    {
        $report = [];
        
        foreach ($actions as $action) {
            if ($action['type'] === 'update_attribute') {
                $key = $action['key'];
                $operator = $action['operator'];
                $value = $action['value'];
                
                $oldValue = $state[$key] ?? 0;
                
                switch ($operator) {
                    case '+':
                        $state[$key] = $oldValue + $value;
                        break;
                    case '-':
                        $state[$key] = max(0, $oldValue - $value); // Prevent negative values
                        break;
                    case '*':
                        $state[$key] = $oldValue * $value;
                        break;
                    case '=':
                        $state[$key] = $value;
                        break;
                }
                
                // Track XP and points changes
                if ($key === 'xp') {
                    $diff = $state[$key] - $oldValue;
                    $xpEarned += $diff;
                } elseif ($key === 'points' || $key === 'coins') {
                    $diff = $state[$key] - $oldValue;
                    $pointsEarned += $diff;
                }
                
                $report[] = [
                    'key' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $state[$key],
                    'message' => "{$key}: {$oldValue} → {$state[$key]}"
                ];
            }
        }
        
        return $report;
    }
}
