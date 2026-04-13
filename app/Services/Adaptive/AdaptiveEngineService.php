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
        $triggeredRule = null;
        $matchedRules  = [];
        $newState      = $currentState;

        foreach ($this->ruleRegistry->getAllRules() as $rule) {
            if ($this->shouldSkipRule($rule, $currentState, $context)) {
                continue;
            }

            if ($rule->evaluate($facts)) {
                $matchedRules[] = $rule;

                if (! $triggeredRule) {
                    $triggeredRule    = $rule;
                    $context['facts'] = $facts;
                    $newState         = $rule->apply($newState, $context);
                }
            }
        }

        if (! $triggeredRule) {
            $newState['next_action'] = 'NEXT_QUESTION';
            $newState['message']     = $context['is_correct']
                ? 'Jawaban benar! Silakan lanjut ke soal berikutnya.'
                : 'Jawaban kurang tepat. Mari coba lagi.';
        }

        Log::info('Adaptive Rule Evaluation', [
            'facts_gathered' => $facts,
            'is_correct'     => $context['is_correct'],
            'matched_rules'  => array_map(
                fn (AdaptiveRuleInterface $rule): string => $rule->getRuleId(),
                $matchedRules,
            ),
        ]);

        return [
            'triggered_rule' => $triggeredRule ? $this->mapRuleMetadata($triggeredRule) : null,
            'matched_rules'  => array_map(
                fn (AdaptiveRuleInterface $rule): array => $this->mapRuleMetadata($rule),
                $matchedRules,
            ),
            'new_state' => $newState,
            'facts'     => $facts,
        ];
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

        if ($actionCode === AdaptiveConstants::ACTION_ACCELERATED_MATERIAL_PROMOTION) {
            return $this->isLastActionMaterialAcceleration($adaptiveState);
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

        return $lastAction === AdaptiveConstants::ACTION_ACCELERATED_MATERIAL_PROMOTION;
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
        ];
    }
}
