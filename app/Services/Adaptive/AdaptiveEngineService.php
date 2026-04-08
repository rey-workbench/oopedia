<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Rules\Adaptive\RuleRegistry;
use Illuminate\Support\Facades\Log;

final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    protected RuleRegistry $ruleRegistry;

    public function __construct()
    {
        $this->ruleRegistry = new RuleRegistry();
    }

    public function evaluate(
        array $facts,
        array $currentState,
        array $context,
    ): array {
        $triggeredRule = null;
        $newState      = $currentState;

        foreach ($this->ruleRegistry->getAllRules() as $rule) {
            if ($rule->evaluate($facts)) {
                $triggeredRule    = $rule;
                $context['facts'] = $facts;
                $newState         = $rule->apply($newState, $context);
                break;
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

    public function getAllRules(): array
    {
        return $this->ruleRegistry->getAllRules();
    }

    public function getRuleById(string $ruleId): mixed
    {
        return $this->ruleRegistry->getRuleById($ruleId);
    }
}
