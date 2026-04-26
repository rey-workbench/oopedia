<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use App\Rules\Adaptive\DynamicAdaptiveRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Mesin Inferensi Forward Chaining – Detective Model.
 *
 * Alur kerja:
 *  1. Mulai dengan fakta observasi awal (dari FactGatheringService).
 *  2. Loop: cari SEMUA aturan yang cocok (Conflict Set).
 *  3. Pilih aturan dengan prioritas tertinggi (Conflict Resolution).
 *  4. Terapkan aksi, lalu tambahkan deduced_facts ke working memory.
 *  5. Ulangi sampai tidak ada aturan baru yang terpicu (saturation).
 *
 * Tidak ada forbidden_facts. Pure Positive Logic Only.
 */
final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    private const MAX_INFERENCE_CYCLES = 10; // Circuit breaker anti-infinite-loop

    public function evaluate(array $facts, array $currentState, array $context): array
    {
        $user = Auth::user();
        $previousState = $currentState;

        [$triggeredRule, $matchedRules, $newState, $finalFacts] = $this->runInferenceCycles($facts, $currentState, $context);

        if (! $triggeredRule) {
            $newState = $this->createDefaultNextQuestionState($newState, (bool) ($context['is_correct'] ?? false));
        }

        // Hanya log jika ada rule non-silent yang dieksekusi
        if ($user && $triggeredRule) {
            $flatKeys = ['target_difficulty', 'current_material_id', 'learning_style', 'xp', 'level'];
            $flatBefore = array_intersect_key($previousState, array_flip($flatKeys));
            $flatAfter = array_intersect_key($newState, array_flip($flatKeys));
            $delta = array_diff_assoc($flatAfter, $flatBefore);

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

        Log::info('Adaptive Inference Complete', [
            'user_id' => $user?->id,
            'initial_facts' => $facts,
            'final_facts' => $finalFacts,
            'triggered_rule' => $triggeredRule?->getRuleId(),
            'action_code' => $triggeredRule?->getActionCode(),
            'matched_count' => count($matchedRules),
        ]);

        return [
            'triggered_rule' => $triggeredRule ? $this->mapRule($triggeredRule) : null,
            'triggered_rules' => array_map(fn(AdaptiveRuleInterface $r) => $this->mapRule($r), $matchedRules),
            'new_state' => $newState,
            'facts' => $finalFacts,
            'engine_metadata' => [
                'rule_count' => Cache::remember('adaptive_rules_count', now()->addHours(24), fn() => AdaptiveRule::where('is_active', true)->count()),
                'engine_version' => '5.0.0-DETECTIVE',
                'fact_labels' => Cache::remember('adaptive_fact_labels', now()->addHours(24), fn() => AdaptiveFact::all()->pluck('name', 'code')->toArray()),
                'fact_categories' => Cache::remember('adaptive_fact_categories', now()->addHours(24), fn() => AdaptiveFact::all()->pluck('category', 'code')->toArray()),
            ],
        ];
    }

    /**
     * Jalankan inference loop hingga saturasi (tidak ada aturan baru yang aktif).
     * Ini adalah inti dari model "Detektif" / Forward Chaining Multi-Tahap.
     *
     * @return array{0: AdaptiveRuleInterface|null, 1: AdaptiveRuleInterface[], 2: array, 3: array}
     */
    private function runInferenceCycles(array $initialFacts, array $currentState, array $context): array
    {
        /** @var Collection<AdaptiveRule> $allRules */
        $allRules = Cache::remember('adaptive_rules_all', now()->addHours(24), function () {
            return AdaptiveRule::with('action')
                ->where('is_active', true)
                ->ordered()
                ->get();
        });

        $workingMemory = $initialFacts;     // Akumulasi semua fakta (awal + deduced)
        $firedRuleCodes = [];                // Aturan yang sudah pernah aktif (anti-loop)
        $allTriggered = [];
        $finalState = $currentState;
        $firstTrigger = null;

        for ($cycle = 0; $cycle < self::MAX_INFERENCE_CYCLES; $cycle++) {
            // 1. Bangun Conflict Set: semua aturan yang cocok dan belum pernah dijalankan
            $conflictSet = $allRules
                ->filter(function (AdaptiveRule $model) use ($workingMemory, $firedRuleCodes) {
                    if (in_array($model->rule_code, $firedRuleCodes, true)) {
                        return false;
                    }
                    $rule = new DynamicAdaptiveRule($model);

                    return $rule->evaluate($workingMemory);
                })
                ->values();

            if ($conflictSet->isEmpty()) {
                break; // Saturasi: tidak ada aturan baru
            }

            // 2. Conflict Resolution: pilih aturan dengan prioritas tertinggi
            /** @var AdaptiveRule $bestModel */
            $bestModel = $conflictSet->first(); // Sudah diurutkan by priority ASC
            $bestRule = new DynamicAdaptiveRule($bestModel);

            if ($this->isRuleBlockedByConstraints($bestRule, $finalState, $context)) {
                $firedRuleCodes[] = $bestModel->rule_code;
                continue;
            }

            // 3. Fire: terapkan aksi (kecuali silent – deduksi saja menambah fakta)
            $actionCode = $bestRule->getActionCode();
            $isSilent = $actionCode === AC::ACTION_SILENT || $actionCode === AC::ACTION_DEDUCTION;

            if (!$isSilent) {
                $proposed = $bestRule->apply($finalState, $context);
                $finalState = $this->mergeStateDeltas($finalState, $proposed, $currentState);
                $allTriggered[] = $bestRule;

                if (!$firstTrigger) {
                    $firstTrigger = $bestRule;
                }
            }

            // 4. Tambahkan deduced_facts ke working memory (chaining mechanism)
            $deduced = $bestRule->getDeducedFacts();
            foreach ($deduced as $deducedFact) {
                if (!in_array($deducedFact, $workingMemory, true)) {
                    $workingMemory[] = $deducedFact;
                }
            }

            $firedRuleCodes[] = $bestModel->rule_code;

            // Jika rule bukan silent dan aksinya terminal, hentikan loop
            if (!$isSilent && $this->isTerminalAction($this->getSemanticNextAction($bestRule))) {
                break;
            }
        }

        return [$firstTrigger, $allTriggered, $finalState, $workingMemory];
    }

    /**
     * Helper to dynamically extract the semantic next_action from instructions if available.
     */
    private function getSemanticNextAction(AdaptiveRuleInterface $rule): string
    {
        $actionCode = $rule->getActionCode();
        if ($rule instanceof \App\Rules\Adaptive\DynamicAdaptiveRule) {
            $model = $rule->getModel();
            if ($model->relationLoaded('action') && $model->action) {
                return $model->action->instructions['next_action'] ?? $actionCode;
            }
        }
        return $actionCode;
    }

    /**
     * Apakah aksi ini mengakhiri rantai inferensi?
     * Aksi non-terminal (misal: study_*) tidak menghentikan loop –
     * hasilnya bisa memicu aturan deduksi lainnya.
     */
    private function isTerminalAction(string $nextAction): bool
    {
        return in_array($nextAction, [
            AC::ACTION_NEXT_QUESTION,
            AC::ACTION_FINISH_MATERIAL,
            AC::ACTION_NEXT_MATERIAL,
            AC::ACTION_INCREASE_DIFFICULTY,
            AC::ACTION_REDUCE_DIFFICULTY,
        ], true);
    }

    private function isRuleBlockedByConstraints(AdaptiveRuleInterface $rule, array $currentState, array $context): bool
    {
        $currentMaterialId = (string) ($context['material_id'] ?? '');
        $stateMaterialId = (string) ($currentState['current_material_id'] ?? '');

        if ($currentMaterialId === '' || $stateMaterialId === '' || $currentMaterialId !== $stateMaterialId) {
            return false;
        }

        $nextAction = $this->getSemanticNextAction($rule);
        
        if ($nextAction === AC::ACTION_INCREASE_DIFFICULTY) {
            return $this->isAtMaxDifficulty($currentState);
        }

        $actionCode = $rule->getActionCode();
        $lastAction = AdaptiveExecutionLog::where('user_id', Auth::id())->latest()->value('action_code');

        $nonRepeatableActions = [
            AC::ACTION_STUDY_VISUAL_MAT,
            AC::ACTION_STUDY_TEXTUAL_MAT,
            AC::ACTION_LOGIC_GUIDE,
            AC::ACTION_SYNTAX_GUIDE,
            AC::ACTION_STUDY_MIXED_MAT,
            AC::ACTION_REDUCE_DIFFICULTY,
        ];

        if (in_array($actionCode, $nonRepeatableActions, true)) {
            return $lastAction === $actionCode;
        }

        return false;
    }

    private function isAtMaxDifficulty(array $state): bool
    {
        return ($state['target_difficulty'] ?? null) === AC::DIFFICULTY_HARD;
    }

    private function mergeStateDeltas(array $combined, array $proposed, array $original): array
    {
        foreach ($proposed as $key => $value) {
            $alreadySet = array_key_exists($key, $combined)
                && (!array_key_exists($key, $original) || $combined[$key] !== $original[$key]);

            if (!$alreadySet) {
                $combined[$key] = $value;
            }
        }

        return $combined;
    }

    private function createDefaultNextQuestionState(array $state, bool $isCorrect): array
    {
        $state['next_action'] = AC::ACTION_NEXT_QUESTION;
        $state['_feedback_message'] = $isCorrect
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
            ->map(fn(AdaptiveRule $m) => new DynamicAdaptiveRule($m))
            ->toArray();
    }

    public function getRuleById(string $ruleId): ?AdaptiveRuleInterface
    {
        $model = AdaptiveRule::with('action')->where('rule_code', $ruleId)->first();

        return $model ? new DynamicAdaptiveRule($model) : null;
    }

    private function mapRule(AdaptiveRuleInterface $rule): array
    {
        $action = $rule instanceof DynamicAdaptiveRule ? $rule->getModel()->action : null;
        $instructions = $action ? ($action->instructions ?? []) : [];

        return [
            'id' => $rule->getRuleId(),
            'name' => $rule->getRuleName(),
            'action' => $instructions['next_action'] ?? $rule->getActionCode(),
            'action_code' => $rule->getActionCode(),
            'priority' => $rule->getPriority(),
            'variant' => $action?->variant ?? 'result',
            'label' => $instructions['label'] ?? null,
            'message' => $instructions['message'] ?? null,
            'title' => $instructions['title'] ?? null,
        ];
    }
}
