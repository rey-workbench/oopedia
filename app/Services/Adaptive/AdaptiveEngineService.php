<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use App\Rules\Adaptive\RuleRegistry;
use Illuminate\Support\Facades\Log;

final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    protected RuleRegistry $ruleRegistry;

    public function __construct()
    {
        $this->ruleRegistry = new RuleRegistry;
    }

    public function evaluate(
        array $facts,
        array $currentState,
        array $context,
    ): array {
        [$triggeredRule, $matchedRules, $newState] = $this->evaluateRegisteredRules(
            facts: $facts,
            currentState: $currentState,
            context: $context,
        );

        if (! $triggeredRule) {
            $newState = $this->applyDefaultFallback(
                state: $newState,
                isCorrect: (bool) ($context['is_correct'] ?? false),
            );
        }

        Log::info('Adaptive Rule Evaluation', [
            'facts_gathered' => $facts,
            'is_correct'     => $context['is_correct'] ?? null,
            'triggered_rule' => $triggeredRule?->getRuleId(),
            'matched_count'  => count($matchedRules),
            'matched_rules'  => array_map(
                fn (AdaptiveRuleInterface $rule): string => $rule->getRuleId(),
                $matchedRules,
            ),
        ]);

        return [
            'triggered_rule'  => $triggeredRule ? $this->mapRuleMetadata($triggeredRule) : null,
            'triggered_rules' => array_map(
                fn (AdaptiveRuleInterface $rule): array => $this->mapRuleMetadata($rule),
                $matchedRules,
            ),
            'new_state' => $newState,
            'facts'     => $facts,
        ];
    }

    /**
     * @return array{0: AdaptiveRuleInterface|null, 1: array<int, AdaptiveRuleInterface>, 2: array<string, mixed>}
     */
    private function evaluateRegisteredRules(array $facts, array $currentState, array $context): array
    {
        $triggeredRules   = [];
        $finalState       = $currentState;
        $context['facts'] = $facts;

        foreach ($this->ruleRegistry->getAllRules() as $rule) {
            if ($this->shouldSkipRule($rule, $currentState, $context)) {
                continue;
            }

            if (! $rule->evaluate($facts)) {
                continue;
            }

            $triggeredRules[] = $rule;

            // Apply rule to get its proposed state changes
            $ruleAppliedState = $rule->apply($currentState, $context);

            // Merge changes into final state, preserving keys set by higher priority rules
            $finalState = $this->mergeRuleOutputs($finalState, $ruleAppliedState, $currentState);
        }

        $mainRule = $triggeredRules[0] ?? null;

        return [$mainRule, $triggeredRules, $finalState];
    }

    /**
     * Merge proposed state from a rule into the combined state.
     * Implements "First-Priority Wins" for each key.
     *
     * @param array<string, mixed> $combinedState The state being built
     * @param array<string, mixed> $proposedState The output of rule->apply()
     * @param array<string, mixed> $originalState The state before any rules fired
     */
    private function mergeRuleOutputs(array $combinedState, array $proposedState, array $originalState): array
    {
        foreach ($proposedState as $key => $value) {
            // A key is considered "set" by a previous higher-priority rule if its value in combinedState
            // is effectively different from the originalState (including if it was newly added).
            $existsInCombined = array_key_exists($key, $combinedState);
            $existsInOriginal = array_key_exists($key, $originalState);

            $isAlreadySet = $existsInCombined && (
                ! $existsInOriginal || $combinedState[$key] !== $originalState[$key]
            );

            if (! $isAlreadySet) {
                $combinedState[$key] = $value;
            }
        }

        return $combinedState;
    }

    /**
     * @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function applyDefaultFallback(array $state, bool $isCorrect): array
    {
        $state['next_action'] = AdaptiveConstants::ACTION_NEXT_QUESTION;
        $state['message']     = $isCorrect
            ? 'Jawaban benar! Silakan lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi.';

        return $state;
    }

    public function getAllRules(): array
    {
        return $this->ruleRegistry->getAllRules();
    }

    public function getRuleById(string $ruleId): mixed
    {
        return $this->ruleRegistry->getRuleById($ruleId);
    }

    private function shouldSkipRule(AdaptiveRuleInterface $rule, array $currentState, array $context): bool
    {
        $adaptiveState = $this->normalizeAdaptiveState($currentState);

        $currentMaterialId = (string) ($context['material_id'] ?? '');
        $stateMaterialId   = (string) ($adaptiveState['current_material_id'] ?? '');

        if ($currentMaterialId === '' || $stateMaterialId === '' || $currentMaterialId !== $stateMaterialId) {
            return false;
        }

        $actionCode = $rule->getActionCode();

        if ($actionCode === AdaptiveConstants::ACTION_ACCELERATED_JUMP) {
            return $this->hasReachedFastTrackTarget($adaptiveState);
        }

        if ($actionCode === AdaptiveConstants::ACTION_ACCELERATED_MATERIAL) {
            return $this->isLastActionMaterialAcceleration($adaptiveState);
        }

        if ($actionCode === AdaptiveConstants::ACTION_SYNTAX_RECOVERY || $actionCode === AdaptiveConstants::ACTION_LOGIC_RECOVERY) {
            return $this->isRecoveryLoopPrevention($adaptiveState, $actionCode);
        }

        return false;
    }

    private function normalizeAdaptiveState(array $currentState): array
    {
        $adaptiveState = $currentState['adaptive_state'] ?? [];

        if (is_string($adaptiveState)) {
            $adaptiveState = json_decode($adaptiveState, true) ?? [];
        }

        return is_array($adaptiveState) ? $adaptiveState : [];
    }

    private function hasReachedFastTrackTarget(array $adaptiveState): bool
    {
        $targetDifficulty = $adaptiveState['target_difficulty'] ?? null;

        return in_array($targetDifficulty, [
            AdaptiveConstants::DIFFICULTY_MEDIUM,
            AdaptiveConstants::DIFFICULTY_HARD,
        ], true);
    }

    private function isLastActionMaterialAcceleration(array $adaptiveState): bool
    {
        $lastAction = $adaptiveState['last_rule']['action'] ?? null;

        return $lastAction === AdaptiveConstants::ACTION_ACCELERATED_MATERIAL;
    }

    private function isRecoveryLoopPrevention(array $adaptiveState, string $actionCode): bool
    {
        $recoveryCount = $adaptiveState['consecutive_recovery_count'] ?? 0;
        $lastAction    = $adaptiveState['last_rule']['action']        ?? null;

        if ($lastAction === $actionCode && $recoveryCount >= 2) {
            return true;
        }

        return false;
    }

    /**
     * @return array{id: string, name: string, action: string, priority: int}
     */
    private function mapRuleMetadata(AdaptiveRuleInterface $rule): array
    {
        return [
            'id'       => $rule->getRuleId(),
            'name'     => $rule->getRuleName(),
            'action'   => $rule->getActionCode(),
            'priority' => $rule->getPriority(),
            'variant'  => $rule->getVariant(),
        ];
    }
}
