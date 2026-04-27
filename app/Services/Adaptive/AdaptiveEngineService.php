<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Enums\Lms\QuestionDifficulty;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Adaptive Engine Service.
 * A forward-chaining inference engine that evaluates pedagogical rules.
 */
final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    private const int MAX_CYCLES = 10;

    public function __construct(
        private readonly Handlers\PrimaryFactHandler $primaryFactHandler,
        private readonly Handlers\VirtualFactHandler $virtualFactHandler,
        private readonly Handlers\ActionHandler $actionHandler,
    ) {}

    /**
     * The main orchestrator. Reads like a story.
     */
    public function evaluate(array $facts, array $state, array $context): array
    {
        // 1. Preparation
        $enrichedFacts = $this->getEnrichedFacts($facts, $state);
        
        // 2. Inference (The Core Logic)
        $result = $this->runInference($enrichedFacts, $state, $context);

        // 3. Post-processing
        if ($result['is_empty']) {
            $result['state'] = $this->applyFallbackNavigation($result['state'], (bool)($context['is_correct'] ?? false));
        }

        // 4. Persistence
        $this->logActivity($result['primary_rule'], $state, $result['state'], $enrichedFacts, $context);

        // 5. Delivery
        return $this->buildResponse($result, $enrichedFacts);
    }

    /**
     * Run the Forward Chaining inference loop.
     */
    private function runInference(array $facts, array $state, array $context): array
    {
        $allRules = $this->fetchActiveRules();
        $memory = ['facts' => $facts, 'state' => $state, 'fired' => []];
        $triggered = [];

        for ($i = 0; $i < self::MAX_CYCLES; $i++) {
            $rule = $this->selectBestRule($allRules, $memory);
            if (!$rule) break;

            $this->fireRule($rule, $memory, $context, $triggered);

            if ($this->shouldStop($rule)) break;
        }

        return [
            'primary_rule' => $triggered[0] ?? null,
            'all_triggered' => $triggered,
            'state' => $memory['state'],
            'facts' => $memory['facts'],
            'is_empty' => empty($triggered),
        ];
    }

    private function selectBestRule(Collection $rules, array $memory): ?AdaptiveRule
    {
        return $rules->first(function (AdaptiveRule $rule) use ($memory) {
            $isNotFired = !in_array($rule->rule_code, $memory['fired'], true);
            $isMatched = $rule->evaluate($memory['facts']);
            $isAllowed = !$this->isBlocked($rule, $memory['state']);

            return $isNotFired && $isMatched && $isAllowed;
        });
    }

    private function fireRule(AdaptiveRule $rule, array &$memory, array $context, array &$triggered): void
    {
        $actionCode = $rule->getActionCode();
        $isDeductionOnly = ($actionCode === ActionConstants::DEDUCTION);

        // Apply visual action
        if (!$isDeductionOnly) {
            $memory['state'] = $this->actionHandler->apply($rule->action?->instructions ?? [], $memory['state'], $context);
            $triggered[] = $rule;
        }

        // Propagate facts
        $memory['facts'] = array_values(array_unique(array_merge($memory['facts'], $rule->getDeducedFacts())));
        $memory['fired'][] = $rule->rule_code;
    }

    private function isBlocked(AdaptiveRuleInterface $rule, array $state): bool
    {
        $flow = $this->getFlow($rule);
        $difficulty = $state[StudentStateSchema::TARGET_DIFFICULTY] ?? null;

        $isAtMax = ($flow === ActionConstants::FLOW_UP && $difficulty === QuestionDifficulty::HARD->value);
        $isAtMin = ($flow === ActionConstants::FLOW_DOWN && $difficulty === QuestionDifficulty::BEGINNER->value);

        return $isAtMax || $isAtMin;
    }

    private function shouldStop(AdaptiveRuleInterface $rule): bool
    {
        return in_array($this->getFlow($rule), [
            ActionConstants::FLOW_NEXT, ActionConstants::FLOW_UP, 
            ActionConstants::FLOW_DOWN, ActionConstants::FLOW_REVIEW
        ], true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    private function getEnrichedFacts(array $facts, array $state): array
    {
        return array_values(array_unique(array_merge($facts, $this->virtualFactHandler->derive($facts, $state))));
    }

    private function getFlow(AdaptiveRuleInterface $rule): string
    {
        return ($rule instanceof AdaptiveRule) 
            ? ($rule->action?->instructions[ActionConstants::KEY_FLOW] ?? ActionConstants::FLOW_NEXT)
            : ActionConstants::FLOW_NEXT;
    }

    private function applyFallbackNavigation(array $state, bool $isCorrect): array
    {
        $state['next_action'] = ActionConstants::FLOW_NEXT;
        $state['_feedback_message'] = $isCorrect ? 'Jawaban benar! Lanjut.' : 'Jawaban kurang tepat. Coba lagi.';
        return $state;
    }

    private function fetchActiveRules(): Collection
    {
        return Cache::remember('adaptive_rules_v7', now()->addDay(), fn() => 
            AdaptiveRule::with('action')->where('is_active', true)->ordered()->get()
        );
    }

    private function logActivity(?AdaptiveRuleInterface $rule, array $old, array $new, array $facts, array $ctx): void
    {
        $user = Auth::user();
        if (!$user || !$rule) return;

        $keys = [StudentStateSchema::TARGET_DIFFICULTY, StudentStateSchema::GLOBAL_XP, StudentStateSchema::CURRENT_LEVEL];
        $delta = array_diff_assoc(array_intersect_key($new, array_flip($keys)), array_intersect_key($old, array_flip($keys)));

        AdaptiveExecutionLog::create([
            'user_id' => $user->id,
            'rule_code' => $rule->getRuleId(),
            'action_code' => $rule->getActionCode(),
            'trigger_facts' => $facts,
            'state_deltas' => $delta ?: [],
            'execution_context' => $ctx,
        ]);
    }

    private function buildResponse(array $result, array $initialFacts): array
    {
        return [
            'triggered_rule' => $result['primary_rule'] ? $this->mapRule($result['primary_rule']) : null,
            'triggered_rules' => array_map(fn($r) => $this->mapRule($r), $result['all_triggered']),
            'new_state' => $result['state'],
            'facts' => $result['facts'],
            'engine_metadata' => $this->getMetadata(),
        ];
    }

    private function getMetadata(): array
    {
        return [
            'rule_count' => Cache::remember('rules_count', now()->addDay(), fn() => AdaptiveRule::where('is_active', true)->count()),
            'engine_version' => '7.1.0-ULTRA-CLEAN',
            'fact_labels' => Cache::remember('fact_labels', now()->addDay(), fn() => AdaptiveFact::all()->pluck('name', 'code')->toArray()),
        ];
    }

    private function mapRule(AdaptiveRuleInterface $rule): array
    {
        $instr = ($rule instanceof AdaptiveRule) ? ($rule->action?->instructions ?? []) : [];
        return [
            'id' => $rule->getRuleId(),
            'name' => $rule->getRuleName(),
            'action' => $instr[ActionConstants::KEY_FLOW] ?? $rule->getActionCode(),
            'action_code' => $rule->getActionCode(),
            'priority' => $rule->getPriority(),
            'variant' => ($rule instanceof AdaptiveRule) ? ($rule->action?->variant ?? 'result') : 'result',
            'message' => $instr[ActionConstants::KEY_MESSAGE] ?? null,
            'title' => $instr[ActionConstants::KEY_TITLE] ?? null,
        ];
    }

    // Public API for metadata/admin
    public function getAllRules(): array { return AdaptiveRule::with('action')->where('is_active', true)->ordered()->get()->toArray(); }
    public function getRuleById(string $id): ?AdaptiveRuleInterface { return AdaptiveRule::with('action')->where('rule_code', $id)->first(); }
}
