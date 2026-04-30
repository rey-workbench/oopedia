<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\DTOs\Adaptive\AdaptiveRuleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdaptiveRule\StoreAdaptiveRuleRequest;
use App\Http\Requests\Admin\AdaptiveRule\UpdateAdaptiveRuleRequest;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Models\StudentState;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class AdaptiveRuleController extends Controller
{
    public function __construct(
        private readonly AdaptiveManagementServiceInterface $adaptiveManagementService,
    ) {}

    public function create(): Response
    {
        return $this->render('Admin/AdaptiveRules/Create/Index', [
            'all_facts'   => AdaptiveFact::all(),
            'all_actions' => AdaptiveAction::all(),
        ]);
    }

    public function edit(AdaptiveRule $adaptiveRule): Response
    {
        return $this->render('Admin/AdaptiveRules/Edit/Index', [
            'rule' => [
                'id'                => $adaptiveRule->id,
                'name'              => $adaptiveRule->name,
                'recommendation'    => $adaptiveRule->recommendation,
                'priority'          => $adaptiveRule->priority,
                'actions'           => $adaptiveRule->getAttribute('actions'),
                'required_fact_ids' => $adaptiveRule->required_fact_ids,
                'deduced_fact_ids'  => $adaptiveRule->deduced_fact_ids,
                'is_active'         => $adaptiveRule->is_active,
            ],
            'all_facts'   => AdaptiveFact::all(),
            'all_actions' => AdaptiveAction::all(),
        ]);
    }

    public function index(): Response
    {
        return $this->render('Admin/AdaptiveRules/Index', [
            'total_rules'                 => AdaptiveRule::count(),
            'total_facts'                 => AdaptiveFact::count(),
            'total_actions'               => AdaptiveAction::count(),
            'rules_by_diagnosis'          => $this->getRulesByDiagnosis(),
            'adaptive_state_distribution' => $this->getAdaptiveStateDistribution(),
            'recent_triggers'             => $this->getRecentTriggers(),
            'rule_triggers_stats'         => $this->getRuleTriggersStats(),
            'decision_tree'               => $this->getDecisionTree(),
            'all_facts'                   => AdaptiveFact::all(),
            'all_actions'                 => AdaptiveAction::all(),
        ]);
    }

    public function store(StoreAdaptiveRuleRequest $storeAdaptiveRuleRequest): RedirectResponse
    {
        $adaptiveRuleDTO = AdaptiveRuleDTO::fromRequest($storeAdaptiveRuleRequest);
        $this->adaptiveManagementService->createRule($adaptiveRuleDTO);

        return back()->with('success', 'Aturan berhasil dibuat.');
    }

    public function update(UpdateAdaptiveRuleRequest $updateAdaptiveRuleRequest, AdaptiveRule $adaptiveRule): RedirectResponse
    {
        $adaptiveRuleDTO = AdaptiveRuleDTO::fromRequest($updateAdaptiveRuleRequest);
        $this->adaptiveManagementService->updateRule($adaptiveRule->id, $adaptiveRuleDTO);

        return back()->with('success', 'Aturan berhasil diperbarui.');
    }

    public function destroy(AdaptiveRule $adaptiveRule): RedirectResponse
    {
        $this->adaptiveManagementService->deleteRule($adaptiveRule->id);

        return back()->with('success', 'Aturan berhasil dihapus.');
    }

    private function getRulesByDiagnosis(): array
    {
        $rules = AdaptiveRule::ordered()->get();

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

    /**
     * @return array<int, array{difficulty: string, count: int}>
     */
    private function getAdaptiveStateDistribution(): array
    {
        $studentStates = StudentState::all();

        $distribution = [
            'beginner' => 0,
            'medium'   => 0,
            'hard'     => 0,
        ];

        foreach ($studentStates as $studentState) {
            $difficulty = $studentState->target_difficulty;
            if ($difficulty && isset($distribution[$difficulty])) {
                $distribution[$difficulty]++;
            }
        }

        $result = [];
        foreach ($distribution as $difficulty => $count) {
            $result[] = [
                'difficulty' => $difficulty,
                'count'      => $count,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array{id: int, rule_id: string, rule_name: string, action: string, user_name: string, material_title: string, created_at: string}>
     */
    private function getRecentTriggers(): array
    {
        return AdaptiveExecutionLog::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($log): array => [
                'id'             => $log->id,
                'rule_id'        => $log->rule_id,
                'rule_name'      => AdaptiveRule::where('id', $log->rule_id)->value('name') ?? $log->rule_id,
                'action'         => $log->action_id,
                'user_name'      => $log->user->name                          ?? 'System',
                'material_title' => $log->execution_context['material_title'] ?? 'General',
                'created_at'     => $log->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    /**
     * @return array<int, array{rule_id: string, rule_name: string, trigger_count: int, percentage: float}>
     */
    private function getRuleTriggersStats(): array
    {
        $totalLogs = AdaptiveExecutionLog::count();
        if ($totalLogs === 0) {
            return [];
        }

        return AdaptiveExecutionLog::select('rule_id', \DB::raw('count(*) as count'))
            ->groupBy('rule_id')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) use ($totalLogs): array {
                $rule = AdaptiveRule::where('id', $stat->rule_id)->first();

                return [
                    'rule_id'       => $stat->rule_id,
                    'rule_name'     => $rule?->name ?? 'Legacy Rule',
                    'trigger_count' => $stat->count,
                    'percentage'    => round(($stat->count / $totalLogs) * 100, 1),
                ];
            })
            ->toArray();
    }

    public function getDecisionTree(): array
    {
        // Construct a virtual tree for visualization
        // Root: Fact Gathering
        //  - Diagnosis Groups (Branches)
        //    - Rules (Leafs)

        $diagnoses = $this->getRulesByDiagnosis();

        $rootChildren = [];
        foreach ($diagnoses as $idx => $diagnosis) {
            $ruleChildren = [];
            foreach ($diagnosis['rules'] as $rule) {
                $ruleChildren[] = [
                    'id'             => $rule['id'],
                    'name'           => $rule['name'],
                    'type'           => 'rule',
                    'is_terminal'    => true,
                    'action_id'      => $rule['actions'][0]['id'] ?? 'H00',
                    'priority'       => $rule['priority'],
                    'recommendation' => $rule['recommendation'],
                    'children'       => [],
                ];
            }

            $rootChildren[] = [
                'id'        => 'diag_' . $idx,
                'name'      => $diagnosis['diagnosis_name'],
                'type'      => 'decision',
                'is_active' => true,
                'children'  => $ruleChildren,
            ];
        }

        return [
            'id'       => 'root',
            'name'     => 'Adaptive Strategy',
            'type'     => 'root',
            'children' => $rootChildren,
        ];
    }
}
