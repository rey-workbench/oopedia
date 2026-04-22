<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use App\Rules\Adaptive\DynamicAdaptiveRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    public function evaluate(array $facts, array $currentState, array $context): array
    {
        $user          = auth()->user();
        $previousState = $currentState;

        [$triggeredRule, $matchedRules, $newState] = $this->evaluateRules($facts, $currentState, $context);

        if (! $triggeredRule) {
            $newState = $this->applyDefaultFallback(
                state: $newState,
                isCorrect: (bool) ($context['is_correct'] ?? false),
            );
        }

        if ($user) {
            $flatKeys   = ['target_difficulty', 'current_material_id', 'learning_style', 'xp', 'level'];
            $flatBefore = array_intersect_key($previousState, array_flip($flatKeys));
            $flatAfter  = array_intersect_key($newState, array_flip($flatKeys));
            $delta      = array_diff_assoc($flatAfter, $flatBefore);

            AdaptiveExecutionLog::create([
                'user_id'           => $user->id,
                'rule_code'         => $triggeredRule?->getRuleId(),
                'action_code'       => $triggeredRule?->getActionCode(),
                'trigger_facts'     => $facts,
                'state_deltas'      => $delta,
                'new_state'         => [],
                'execution_context' => $context,
            ]);
        }

        Log::info('Adaptive Rule Evaluation', [
            'user_id'        => $user?->id,
            'facts'          => $facts,
            'triggered_rule' => $triggeredRule?->getRuleId(),
            'action_code'    => $triggeredRule?->getActionCode(),
            'matched_count'  => count($matchedRules),
        ]);

        return [
            'triggered_rule'  => $triggeredRule ? $this->mapRule($triggeredRule) : null,
            'triggered_rules' => array_map(fn (AdaptiveRuleInterface $r) => $this->mapRule($r), $matchedRules),
            'new_state'       => $newState,
            'facts'           => $facts,
        ];
    }

    /**
     * Flat forward chaining:
     * 1. Load all active rules ordered by priority (cached 24h).
     * 2. Evaluate each rule against the gathered facts.
     * 3. Apply the first matching rule. Continue if non-exclusive (configurable).
     *
     * @return array{0: AdaptiveRuleInterface|null, 1: AdaptiveRuleInterface[], 2: array<string, mixed>}
     */
    private function evaluateRules(array $facts, array $currentState, array $context): array
    {
        $lastAction = AdaptiveExecutionLog::where('user_id', auth()->id())
            ->latest()
            ->value('action_code');

        /** @var Collection<AdaptiveRule> $rules */
        $rules = Cache::remember('adaptive_rules_all', now()->addHours(24), function () {
            return AdaptiveRule::with('action')
                ->where('is_active', true)
                ->ordered()
                ->get();
        });

        $triggeredRules = [];
        $finalState     = $currentState;

        foreach ($rules as $model) {
            $rule = new DynamicAdaptiveRule($model);

            if (! $rule->evaluate($facts)) {
                continue;
            }

            if ($this->shouldSkipRule($rule, $currentState, $context, $lastAction)) {
                continue;
            }

            $triggeredRules[] = $rule;

            $proposed   = $rule->apply($currentState, $context);
            $finalState = $this->mergeOutputs($finalState, $proposed, $currentState);

            // First-rule-wins: stop after first match
            break;
        }

        return [$triggeredRules[0] ?? null, $triggeredRules, $finalState];
    }

    private function shouldSkipRule(
        AdaptiveRuleInterface $rule,
        array $currentState,
        array $context,
        ?string $lastActionCode,
    ): bool {
        $currentMaterialId = (string) ($context['material_id'] ?? '');
        $stateMaterialId   = (string) ($currentState['current_material_id'] ?? '');

        if ($currentMaterialId === '' || $stateMaterialId === '' || $currentMaterialId !== $stateMaterialId) {
            return false;
        }

        $actionCode = $rule->getActionCode();

        if ($actionCode === AdaptiveConstants::ACTION_ACCELERATED_JUMP) {
            return $this->hasReachedFastTrackTarget($currentState);
        }

        if ($actionCode === AdaptiveConstants::ACTION_ACCELERATED_MATERIAL) {
            return $lastActionCode === AdaptiveConstants::ACTION_ACCELERATED_MATERIAL;
        }

        if (in_array($actionCode, [AdaptiveConstants::ACTION_SYNTAX_RECOVERY, AdaptiveConstants::ACTION_LOGIC_RECOVERY], true)) {
            return $lastActionCode === $actionCode;
        }

        return false;
    }

    private function hasReachedFastTrackTarget(array $state): bool
    {
        return in_array($state['target_difficulty'] ?? null, [
            AdaptiveConstants::DIFFICULTY_MEDIUM,
            AdaptiveConstants::DIFFICULTY_HARD,
        ], true);
    }

    private function mergeOutputs(array $combined, array $proposed, array $original): array
    {
        foreach ($proposed as $key => $value) {
            $alreadySet = array_key_exists($key, $combined)
                && (! array_key_exists($key, $original) || $combined[$key] !== $original[$key]);

            if (! $alreadySet) {
                $combined[$key] = $value;
            }
        }

        return $combined;
    }

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
        return AdaptiveRule::with('action')
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->map(fn (AdaptiveRule $m) => new DynamicAdaptiveRule($m))
            ->toArray();
    }

    public function getRuleById(string $ruleId): ?AdaptiveRuleInterface
    {
        $model = AdaptiveRule::with('action')->where('rule_code', $ruleId)->first();

        return $model ? new DynamicAdaptiveRule($model) : null;
    }

    private function mapRule(AdaptiveRuleInterface $rule): array
    {
        return [
            'id'       => $rule->getRuleId(),
            'name'     => $rule->getRuleName(),
            'action'   => $rule->getActionCode(),
            'priority' => $rule->getPriority(),
        ];
    }
}
