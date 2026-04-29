<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use App\Rules\Adaptive\Constants\FactConstants;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

final class AdaptiveRuleController extends Controller
{
    public function create(): Response
    {
        return $this->render('Admin/AdaptiveRules/Create/', [
            'allFacts' => AdaptiveFact::all(),
            'allActions' => AdaptiveAction::all(),
        ]);
    }

    public function edit(AdaptiveRule $adaptive_rule): Response
    {
        return $this->render('Admin/AdaptiveRules/Edit/Index', [
            'rule' => [
                'id' => $adaptive_rule->id,
                'name' => $adaptive_rule->name,
                'recommendation' => $adaptive_rule->recommendation,
                'priority' => $adaptive_rule->priority,
                'action_ids' => $adaptive_rule->action_ids,
                'required_fact_ids' => $adaptive_rule->required_fact_ids,
                'deduced_fact_ids' => $adaptive_rule->deduced_fact_ids,
                'is_active' => $adaptive_rule->is_active,
            ],
            'allFacts' => AdaptiveFact::all(),
            'allActions' => AdaptiveAction::all(),
        ]);
    }

    public function index(): Response
    {
        return $this->render('Admin/AdaptiveRules/Index', [
            'totalRules' => AdaptiveRule::count(),
            'totalFacts' => AdaptiveFact::count(),
            'totalActions' => AdaptiveAction::count(),
            'rulesByDiagnosis' => $this->getRulesByDiagnosis(),
            'adaptiveStateDistribution' => $this->getAdaptiveStateDistribution(),
            'recentTriggers' => $this->getRecentTriggers(),
            'ruleTriggersStats' => $this->getRuleTriggersStats(),
            'decisionTree' => $this->getDecisionTree(),
            'allFacts' => AdaptiveFact::all(),
            'allActions' => AdaptiveAction::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->getValidationRules());

        $this->syncFacts($validated['facts'] ?? [], $validated['deduced_facts'] ?? []);
        AdaptiveRule::create($validated);

        return back()->with('success', 'Aturan berhasil dibuat.');
    }

    public function update(Request $request, AdaptiveRule $adaptive_rule): RedirectResponse
    {
        $validated = $request->validate($this->getValidationRules($adaptive_rule));

        $this->syncFacts($validated['facts'] ?? [], $validated['deduced_facts'] ?? []);
        $adaptive_rule->update($validated);

        return back()->with('success', 'Aturan berhasil diperbarui.');
    }

    private function getValidationRules(?AdaptiveRule $rule = null): array
    {
        $idRule = $rule
            ? 'required|string|unique:adaptive_rules,id,' . $rule->id
            : 'required|string|unique:adaptive_rules,id';

        return [
            'id' => $idRule,
            'name' => 'required|string|max:255',
            'recommendation' => 'nullable|string',
            'priority' => 'required|integer',
            'action_ids' => 'nullable|array',
            'action_ids.*' => 'exists:adaptive_actions,id',
            'required_fact_ids' => 'nullable|array',
            'deduced_fact_ids' => 'nullable|array',
            'facts' => 'nullable|array',
            'deduced_facts' => 'nullable|array',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Auto-insert facts that don't exist in the database yet.
     */
    private function syncFacts(array $facts, array $deducedFacts = []): void
    {
        $keys = AdaptiveConditionKeys::class;
        $factConst = FactConstants::class;

        // 1. Sync from WHEN blocks (facts array)
        foreach ($facts as $fact) {
            $id = $fact['id'] ?? $fact['key'] ?? null;
            if (!$id)
                continue;

            $standardLogic = $factConst::getLogic($id);

            if ($standardLogic) {
                // If it matches a G-code, enforce standard name and logic
                $name = $factConst::NAMES[$id] ?? $fact['name'];
                $description = json_encode([
                        $keys::OP => $standardLogic[$keys::OP],
                        $keys::VAL => $standardLogic[$keys::VAL],
                        $keys::KEY => $standardLogic[$keys::KEY],
                ]);
            } else {
                // Custom fact
                $name = $fact['name'] ?? 'Discover: ' . $id;
                $description = json_encode([
                        $keys::OP => $fact['operator'] ?? $keys::OP_EQ,
                        $keys::VAL => $fact['value'] ?? 1,
                        $keys::KEY => $fact['key'] ?? $id,
                ]);
            }

            AdaptiveFact::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $name,
                    'category' => 'primary',
                    'description' => $description,
                ]
            );
        }

        // 2. Sync from DEDUCE blocks (Virtual Facts / Diagnoses)
        foreach ($deducedFacts as $fact) {
            $id = $fact['id'] ?? $fact['key'] ?? null;
            if (!empty($id)) {
                AdaptiveFact::updateOrCreate(
                    ['id' => $id],
                    [
                        'name' => $fact['name'] ?? ($factConst::VIRTUAL_NAMES[$id] ?? 'Diagnosa: ' . $id),
                        'category' => 'virtual',
                        'description' => null,
                    ]
                );
            }
        }
    }

    public function destroy(AdaptiveRule $adaptive_rule): RedirectResponse
    {
        $adaptive_rule->delete();

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
                'diagnosis' => $diagnosisName ?? 'Uncategorized',
                'count' => $ruleList->count(),
                'rules' => $ruleList->map(fn($rule) => [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'recommendation' => $rule->recommendation,
                    'priority' => $rule->priority,
                    'action_ids' => $rule->action_ids,
                    'required_fact_ids' => $rule->required_fact_ids,
                    'deduced_fact_ids' => $rule->deduced_fact_ids,
                    'is_active' => $rule->is_active,
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
            'medium' => 0,
            'hard' => 0,
        ];

        foreach ($studentStates as $state) {
            $difficulty = $state->target_difficulty;
            if ($difficulty && isset($distribution[$difficulty])) {
                $distribution[$difficulty]++;
            }
        }

        $result = [];
        foreach ($distribution as $difficulty => $count) {
            $result[] = [
                'difficulty' => $difficulty,
                'count' => $count,
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
            ->map(fn($log) => [
                'id' => $log->id,
                'rule_id' => $log->rule_id,
                'rule_name' => AdaptiveRule::where('id', $log->rule_id)->value('name') ?? $log->rule_id,
                'action' => $log->action_id,
                'user_name' => $log->user->name ?? 'System',
                'material_title' => $log->execution_context['material_title'] ?? 'General',
                'created_at' => $log->created_at->diffForHumans(),
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
            ->map(function ($stat) use ($totalLogs) {
                $rule = AdaptiveRule::where('id', $stat->rule_id)->first();

                return [
                    'rule_id' => $stat->rule_id,
                    'rule_name' => $rule?->name ?? 'Legacy Rule',
                    'trigger_count' => $stat->count,
                    'percentage' => round(($stat->count / $totalLogs) * 100, 1),
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
                    'id' => $rule['id'],
                    'name' => $rule['name'],
                    'type' => 'rule',
                    'is_terminal' => true,
                    'action_id' => $rule['action_ids'][0] ?? 'H00',
                    'priority' => $rule['priority'],
                    'recommendation' => $rule['recommendation'],
                    'children' => [],
                ];
            }

            $rootChildren[] = [
                'id' => 'diag_' . $idx,
                'name' => $diagnosis['diagnosis'],
                'type' => 'decision',
                'is_active' => true,
                'children' => $ruleChildren,
            ];
        }

        return [
            'id' => 'root',
            'name' => 'Adaptive Strategy',
            'type' => 'root',
            'children' => $rootChildren,
        ];
    }
}
