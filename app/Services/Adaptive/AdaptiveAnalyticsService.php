<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\AdaptiveActionRepositoryInterface;
use App\Contracts\Repositories\AdaptiveExecutionLogRepositoryInterface;
use App\Contracts\Repositories\AdaptiveFactRepositoryInterface;
use App\Contracts\Repositories\AdaptiveRuleRepositoryInterface;
use App\Contracts\Services\AdaptiveAnalyticsServiceInterface;
use App\Http\Resources\AdaptiveActionResource;
use App\Http\Resources\AdaptiveExecutionLogResource;
use App\Http\Resources\AdaptiveFactResource;
use App\Http\Resources\AdaptiveRuleResource;
use App\Models\StudentState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

final readonly class AdaptiveAnalyticsService implements AdaptiveAnalyticsServiceInterface
{
    public function __construct(
        private AdaptiveRuleRepositoryInterface $adaptiveRuleRepository,
        private AdaptiveExecutionLogRepositoryInterface $adaptiveExecutionLogRepository,
        private AdaptiveFactRepositoryInterface $adaptiveFactRepository,
        private AdaptiveActionRepositoryInterface $adaptiveActionRepository,
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'total_rules'   => $this->adaptiveRuleRepository->count(),
            'total_facts'   => $this->adaptiveFactRepository->count(),
            'total_actions' => $this->adaptiveActionRepository->count(),
        ];
    }

    public function getRecentTriggers(int $limit = 10): array
    {
        $actions = $this->adaptiveActionRepository->allKeyedById();
        $logs    = $this->adaptiveExecutionLogRepository->getRecent($limit);

        return AdaptiveExecutionLogResource::collection($logs->map(function ($log) use ($actions): Model {
            $log->rule_name   = $this->adaptiveRuleRepository->findNameById($log->rule_id) ?? $log->rule_id;
            $log->action_name = $actions->get($log->action_id)?->name                      ?? $log->action_id;

            return $log;
        }))->resolve();
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
            ->all();
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
        $rules     = $this->adaptiveRuleRepository->getOrdered();
        $grouped   = $rules->groupBy('name');

        $result = [];
        foreach ($grouped as $diagnosisName => $ruleList) {
            $result[] = [
                'diagnosis_name' => $diagnosisName ?? 'Uncategorized',
                'count'          => $ruleList->count(),
                'rules'          => AdaptiveRuleResource::collection($ruleList)->resolve(),
            ];
        }

        return $result;
    }

    public function getAllFacts(): SupportCollection
    {
        return collect(AdaptiveFactResource::collection($this->adaptiveFactRepository->getAllForResources())->resolve());
    }

    public function getAllActions(): SupportCollection
    {
        return collect(AdaptiveActionResource::collection($this->adaptiveActionRepository->getAllForResources())->resolve());
    }
}
