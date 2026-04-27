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
use Illuminate\Support\Facades\Log;

/**
 * Forward Chaining Inference Engine – Modular Version.
 */
final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    private const int MAX_INFERENCE_CYCLES = 10;

    public function __construct(
        private readonly Handlers\PrimaryFactHandler $primaryFactHandler,
        private readonly Handlers\VirtualFactHandler $virtualFactHandler,
        private readonly Handlers\ActionHandler $actionHandler,
    ) {}

    public function evaluate(array $facts, array $currentState, array $context): array
    {
        $user = Auth::user();
        $previousState = $currentState;

        // 1. Enrich facts (Virtualization layer)
        $enrichedFacts = array_values(array_unique(array_merge(
            $facts,
            $this->virtualFactHandler->derive($facts)
        )));

        // 2. Main Inference Engine Loop
        [$triggeredRule, $matchedRules, $newState, $finalFacts] = $this->runInferenceCycles($enrichedFacts, $currentState, $context);

        // 3. Default Action if no rule triggered
        if (! $triggeredRule) {
            $newState = $this->createDefaultNextQuestionState($newState, (bool) ($context['is_correct'] ?? false));
        }

        // 4. Persistence & Logging
        if ($user && $triggeredRule) {
            $this->logExecution($user, $triggeredRule, $previousState, $newState, $enrichedFacts, $context);
        }

        return [
            'triggered_rule' => $triggeredRule ? $this->mapRule($triggeredRule) : null,
            'triggered_rules' => array_map(fn(AdaptiveRuleInterface $r) => $this->mapRule($r), $matchedRules),
            'new_state' => $newState,
            'facts' => $finalFacts,
            'engine_metadata' => $this->getEngineMetadata(),
        ];
    }

    private function runInferenceCycles(array $initialFacts, array $currentState, array $context): array
    {
        $allRules = Cache::remember('adaptive_rules_all', now()->addHours(24), function () {
            return AdaptiveRule::with('action')->where('is_active', true)->ordered()->get();
        });

        $workingMemory = $initialFacts;
        $firedRuleCodes = [];
        $allTriggered = [];
        $finalState = $currentState;
        $firstTrigger = null;

        for ($cycle = 0; $cycle < self::MAX_INFERENCE_CYCLES; $cycle++) {
            $conflictSet = $allRules->filter(function (AdaptiveRule $model) use ($workingMemory, $firedRuleCodes) {
                if (in_array($model->rule_code, $firedRuleCodes, true)) return false;
                return $model->evaluate($workingMemory);
            })->values();

            if ($conflictSet->isEmpty()) break;

            $bestRule = $conflictSet->first();

            if ($this->isRuleBlockedByConstraints($bestRule, $finalState)) {
                $firedRuleCodes[] = $bestRule->rule_code;
                continue;
            }

            // Fire Action via ActionHandler
            $actionCode = $bestRule->getActionCode();
            $isSilent = $actionCode === ActionConstants::SILENT || $actionCode === ActionConstants::DEDUCTION;

            if (!$isSilent) {
                $instructions = $bestRule->action?->instructions ?? [];
                $finalState = $this->actionHandler->apply($instructions, $finalState, $context);
                $allTriggered[] = $bestRule;
                if (!$firstTrigger) $firstTrigger = $bestRule;
            }

            // Fact Propagation (Chaining)
            $deduced = $bestRule->getDeducedFacts();
            foreach ($deduced as $f) {
                if (!in_array($f, $workingMemory, true)) $workingMemory[] = $f;
            }

            $firedRuleCodes[] = $bestRule->rule_code;

            if (!$isSilent && $this->isTerminalAction($this->getSemanticNextAction($bestRule))) break;
        }

        return [$firstTrigger, $allTriggered, $finalState, $workingMemory];
    }

    private function isRuleBlockedByConstraints(AdaptiveRuleInterface $rule, array $currentState): bool
    {
        $nextAction = $this->getSemanticNextAction($rule);
        
        if ($nextAction === ActionConstants::LABEL_INCREASE_DIFFICULTY) {
            return ($currentState[StudentStateSchema::TARGET_DIFFICULTY] ?? null) === QuestionDifficulty::HARD->value;
        }

        if ($nextAction === ActionConstants::LABEL_REDUCE_DIFFICULTY) {
            return ($currentState[StudentStateSchema::TARGET_DIFFICULTY] ?? null) === QuestionDifficulty::BEGINNER->value;
        }

        return false;
    }

    private function isTerminalAction(string $nextAction): bool
    {
        return in_array($nextAction, [
            ActionConstants::LABEL_NEXT_QUESTION,
            ActionConstants::LABEL_INCREASE_DIFFICULTY,
            ActionConstants::LABEL_REDUCE_DIFFICULTY,
            ActionConstants::LABEL_STUDY_MATERIAL,
        ], true);
    }

    private function getSemanticNextAction(AdaptiveRuleInterface $rule): string
    {
        if ($rule instanceof AdaptiveRule) {
            return $rule->action?->instructions[ActionConstants::KEY_NEXT_ACTION] ?? $rule->getActionCode();
        }
        return $rule->getActionCode();
    }

    private function createDefaultNextQuestionState(array $state, bool $isCorrect): array
    {
        $state['next_action'] = ActionConstants::LABEL_NEXT_QUESTION;
        $state['_feedback_message'] = $isCorrect
            ? 'Jawaban benar! Silakan lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi.';
        return $state;
    }

    private function logExecution($user, $triggeredRule, $previousState, $newState, $facts, $context): void
    {
        $flatKeys = [
            StudentStateSchema::TARGET_DIFFICULTY,
            StudentStateSchema::CURRENT_MATERIAL_ID,
            StudentStateSchema::LEARNING_STYLE,
            StudentStateSchema::GLOBAL_XP,
            StudentStateSchema::CURRENT_LEVEL
        ];
        
        $delta = array_diff_assoc(
            array_intersect_key($newState, array_flip($flatKeys)),
            array_intersect_key($previousState, array_flip($flatKeys))
        );

        AdaptiveExecutionLog::create([
            'user_id' => $user->id,
            'rule_code' => $triggeredRule->getRuleId(),
            'action_code' => $triggeredRule->getActionCode(),
            'trigger_facts' => $facts,
            'state_deltas' => $delta ?: [],
            'new_state' => [],
            'execution_context' => $context,
        ]);
    }

    private function getEngineMetadata(): array
    {
        return [
            'rule_count' => Cache::remember('adaptive_rules_count', now()->addHours(24), fn() => AdaptiveRule::where('is_active', true)->count()),
            'engine_version' => '6.1.0-SPECIALIZED',
            'fact_labels' => Cache::remember('adaptive_fact_labels', now()->addHours(24), fn() => AdaptiveFact::all()->pluck('name', 'code')->toArray()),
        ];
    }

    public function getAllRules(): array
    {
        return AdaptiveRule::with('action')->where('is_active', true)->ordered()->get()->toArray();
    }

    public function getRuleById(string $ruleId): ?AdaptiveRuleInterface
    {
        return AdaptiveRule::with('action')->where('rule_code', $ruleId)->first();
    }

    private function mapRule(AdaptiveRuleInterface $rule): array
    {
        $action = $rule instanceof AdaptiveRule ? $rule->action : null;
        $instr = $action->instructions ?? [];

        return [
            'id' => $rule->getRuleId(),
            'name' => $rule->getRuleName(),
            'action' => $instr[ActionConstants::KEY_NEXT_ACTION] ?? $rule->getActionCode(),
            'action_code' => $rule->getActionCode(),
            'priority' => $rule->getPriority(),
            'variant' => $action?->variant ?? 'result',
            'label' => $instr[ActionConstants::KEY_LABEL] ?? null,
            'message' => $instr[ActionConstants::KEY_MESSAGE] ?? null,
            'title' => $instr[ActionConstants::KEY_TITLE] ?? null,
        ];
    }
}
