<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\AdaptiveExecutionLogRepositoryInterface;
use App\Contracts\Repositories\AdaptiveRuleRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\AdaptiveAnalyticsServiceInterface;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Models\StudentState;
use Illuminate\Support\Collection;

final readonly class AdaptiveAnalyticsService implements AdaptiveAnalyticsServiceInterface
{
    public function __construct(
        private AdaptiveRuleRepositoryInterface $adaptiveRuleRepository,
        private AdaptiveExecutionLogRepositoryInterface $adaptiveExecutionLogRepository,
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'total_rules'   => $this->adaptiveRuleRepository->count(),
            'total_facts'   => AdaptiveFact::count(),
            'total_actions' => AdaptiveAction::count(),
        ];
    }

    public function getRecentTriggers(int $limit = 10): array
    {
        return $this->adaptiveExecutionLogRepository->getRecent($limit)
            ->map(fn ($log): array => [
                'id'             => $log->id,
                'rule_id'        => $log->rule_id,
                'rule_name'      => AdaptiveRule::where('id', $log->rule_id)->value('name') ?? $log->rule_id,
                'action'         => $log->action_id,
                'user_name'      => $log->user->name                          ?? 'System',
                'material_title' => $log->execution_context['material_title'] ?? 'General',
                'created_at'     => $log->created_at->diffForHumans(),
            ])
            ->all();
    }

    public function getRuleTriggerStats(): array
    {
        $totalLogs = $this->adaptiveExecutionLogRepository->count();
        if ($totalLogs === 0) {
            return [];
        }

        return $this->adaptiveRuleRepository->getWithExecutionStats()
            ->filter(fn ($rule): bool => $rule->execution_logs_count > 0)
            ->map(fn ($rule): array => [
                'rule_id'       => (string) $rule->id,
                'rule_name'     => $rule->name ?? 'Legacy Rule',
                'trigger_count' => (int) $rule->execution_logs_count,
                'percentage'    => round(($rule->execution_logs_count / $totalLogs) * 100, 1),
            ])
            ->toArray();
    }

    public function getAdaptiveStateDistribution(): array
    {
        return StudentState::whereNotNull('target_difficulty')
            ->selectRaw('target_difficulty as difficulty, count(*) as count')
            ->groupBy('target_difficulty')
            ->get()
            ->map(fn ($item): array => [
                'difficulty' => (string) $item->difficulty,
                'count'      => (int) $item->count,
            ])
            ->toArray();
    }

    public function getDecisionTree(): array
    {
        $rules = $this->adaptiveRuleRepository->getOrdered();

        $nodes = [];
        $edges = [];

        foreach ($rules as $rule) {
            $ruleNodeId = 'rule_' . $rule->id;
            $nodes[]    = [
                'id'    => $ruleNodeId,
                'type'  => 'rule',
                'label' => $rule->name,
                'data'  => $rule,
            ];

            foreach ($rule->required_fact_ids ?? [] as $factId) {
                $factNodeId = 'fact_' . $factId;
                $edges[]    = [
                    'id'     => sprintf('edge_%s_%s', $factId, $rule->id),
                    'source' => $factNodeId,
                    'target' => $ruleNodeId,
                ];
            }
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }

    public function getRulesByDiagnosis(): array
    {
        $rules = $this->adaptiveRuleRepository->getOrdered();

        // Group by Name (Diagnosis) from DB
        $grouped = $rules->groupBy('name');

        $result = [];
        foreach ($grouped as $diagnosisName => $ruleList) {
            $result[] = [
                'diagnosis_name' => $diagnosisName ?? 'Uncategorized',
                'count'          => $ruleList->count(),
                'rules'          => $ruleList->map(fn ($rule): array => [
                    'id'                => $rule->id,
                    'name'              => $rule->name,
                    'recommendation'    => $rule->recommendation,
                    'priority'          => $rule->priority,
                    'actions'           => $rule->getAttribute('actions'),
                    'required_fact_ids' => $rule->required_fact_ids,
                    'deduced_fact_ids'  => $rule->deduced_fact_ids,
                    'is_active'         => $rule->is_active,
                ]),
            ];
        }

        return $result;
    }

    public function getAllFacts(): Collection
    {
        return AdaptiveFact::select(['id', 'name', 'category'])->get();
    }

    public function getAllActions(): Collection
    {
        return AdaptiveAction::select(['id', 'name', 'description', 'variant'])->get();
    }
}
