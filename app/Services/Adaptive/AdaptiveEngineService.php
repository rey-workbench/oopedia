<?php

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Rules\Adaptive\RuleRegistry;
use Illuminate\Support\Facades\Log;

/**
 * AdaptiveEngineService
 *
 * Main orchestrator for the adaptive learning system.
 * Evaluates rules using forward chaining and returns appropriate actions.
 */
class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    protected RuleRegistry $ruleRegistry;

    public function __construct()
    {
        $this->ruleRegistry = new RuleRegistry;
    }

    /**
     * Main entry point: Evaluate adaptive rules and return action.
     *
     * @param array $facts Gathered facts (G01-G25)
     * @param array $currentState Current state of the student
     * @param array $context Context information (is_correct, question_id, etc.)
     * @return array Result containing triggered rule, new state, and facts
     */
    public function evaluate(
        array $facts,
        array $currentState,
        array $context,
    ): array {
        // 2. Forward Chaining: Find first matching rule
        $triggeredRule = null;
        $newState      = $currentState;

        foreach ($this->ruleRegistry->getAllRules() as $rule) {
            if ($rule->evaluate($facts)) {
                $triggeredRule = $rule;
                // Pass facts into context so the rule's apply() can use them
                $context['facts'] = $facts;
                $newState         = $rule->apply($newState, $context);
                break; // First match wins (priority-based)
            }
        }

        // 3. Fallback if no rule matched
        if (! $triggeredRule) {
            $newState['next_action'] = 'NEXT_QUESTION';
            $newState['message']     = $context['is_correct']
                ? 'Jawaban benar! Silakan lanjut ke soal berikutnya.'
                : 'Jawaban kurang tepat. Mari coba lagi.';
        }

        // 4. Log for debugging
        Log::info('Adaptive Rule Evaluation', [
            'facts_gathered' => $facts,
            'is_correct'     => $context['is_correct'],
        ]);

        return [
            'triggered_rule' => $triggeredRule ? [
                'id'       => $triggeredRule->getRuleId(),
                'name'     => $triggeredRule->getRuleName(),
                'action'   => $triggeredRule->getActionCode(),
                'priority' => $triggeredRule->getPriority(),
            ] : null,
            'new_state' => $newState,
            'facts'     => $facts,
        ];
    }

    /**
     * Get all registered rules (for debugging/testing).
     */
    public function getAllRules(): array
    {
        return $this->ruleRegistry->getAllRules();
    }

    /**
     * Get a specific rule by ID (for debugging/testing).
     */
    public function getRuleById(string $ruleId): mixed
    {
        return $this->ruleRegistry->getRuleById($ruleId);
    }
}
