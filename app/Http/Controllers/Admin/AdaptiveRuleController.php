<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Models\StudentState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

final class AdaptiveRuleController extends Controller
{
    public function create(): Response
    {
        return $this->render('Admin/AdaptiveRules/Create/', [
            'allFacts'   => AdaptiveFact::all(),
            'allActions' => AdaptiveAction::all(),
        ]);
    }

    public function edit(AdaptiveRule $adaptive_rule): Response
    {
        return $this->render('Admin/AdaptiveRules/Edit/Index', [
            'rule' => [
                'real_id'         => $adaptive_rule->id,
                'id'              => $adaptive_rule->code,
                'code'            => $adaptive_rule->code,
                'name'            => $adaptive_rule->name,
                'domain'          => $adaptive_rule->domain,
                'priority'        => $adaptive_rule->priority,
                'action_id'       => $adaptive_rule->action_id,
                'required_facts'  => $adaptive_rule->required_facts,
                'forbidden_facts' => $adaptive_rule->forbidden_facts,
                'deduced_facts'   => $adaptive_rule->deduced_facts,
                'is_active'       => $adaptive_rule->is_active,
            ],
            'allFacts'   => AdaptiveFact::all(),
            'allActions' => AdaptiveAction::all(),
        ]);
    }

    public function index(): Response
    {
        return $this->render('Admin/AdaptiveRules/Index', [
            'totalRules'                => AdaptiveRule::count(),
            'totalFacts'                => AdaptiveFact::count(),
            'totalActions'              => AdaptiveAction::count(),
            'rulesByDomain'             => $this->getRulesByDomain(),
            'adaptiveStateDistribution' => $this->getAdaptiveStateDistribution(),
            'recentTriggers'            => $this->getRecentTriggers(),
            'ruleTriggersStats'         => $this->getRuleTriggersStats(),
            'decisionTree'              => $this->getDecisionTree(),
            'allFacts'                  => AdaptiveFact::all(),
            'allActions'                => AdaptiveAction::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'            => 'required|string|unique:adaptive_rules,code',
            'name'            => 'required|string|max:255',
            'domain'          => 'required|string',
            'priority'        => 'required|integer',
            'action_id'       => 'required|exists:adaptive_actions,id',
            'required_facts'  => 'nullable|array',
            'forbidden_facts' => 'nullable|array',
            'is_active'       => 'boolean',
        ]);

        AdaptiveRule::create($validated);

        return back()->with('success', 'Aturan berhasil dibuat.');
    }

    public function update(Request $request, AdaptiveRule $adaptive_rule): RedirectResponse
    {
        $validated = $request->validate([
            'code'            => 'required|string|unique:adaptive_rules,code,' . $adaptive_rule->id,
            'name'            => 'required|string|max:255',
            'domain'          => 'required|string',
            'priority'        => 'required|integer',
            'action_id'       => 'required|exists:adaptive_actions,id',
            'required_facts'  => 'nullable|array',
            'forbidden_facts' => 'nullable|array',
            'is_active'       => 'boolean',
        ]);

        $adaptive_rule->update($validated);

        return back()->with('success', 'Aturan berhasil diperbarui.');
    }

    public function destroy(AdaptiveRule $adaptive_rule): RedirectResponse
    {
        $adaptive_rule->delete();

        return back()->with('success', 'Aturan berhasil dihapus.');
    }

    private function getRulesByDomain(): array
    {
        $rules = AdaptiveRule::with('action')->ordered()->get();

        // Group by domain from DB
        $grouped = $rules->groupBy('domain');

        $result = [];
        foreach ($grouped as $domainName => $ruleList) {
            $result[] = [
                'domain' => $domainName ?? 'Uncategorized',
                'count'  => $ruleList->count(),
                'rules'  => $ruleList->map(fn ($rule) => [
                    'id'              => $rule->code,
                    'real_id'         => $rule->id,
                    'code'            => $rule->code,
                    'name'            => $rule->name,
                    'domain'          => $rule->domain,
                    'priority'        => $rule->priority,
                    'action'          => $rule->action?->code ?? 'H00',
                    'action_id'       => $rule->action_id,
                    'required_facts'  => $rule->required_facts,
                    'forbidden_facts' => $rule->forbidden_facts,
                    'deduced_facts'   => $rule->deduced_facts,
                    'is_active'       => $rule->is_active,
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
            ->map(fn ($log) => [
                'id'             => $log->id,
                'rule_id'        => $log->code,
                'rule_name'      => AdaptiveRule::where('code', $log->code)->value('name') ?? $log->code,
                'action'         => $log->action_code,
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

        return AdaptiveExecutionLog::select('code', \DB::raw('count(*) as count'))
            ->groupBy('code')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) use ($totalLogs) {
                $rule = AdaptiveRule::where('code', $stat->code)->first();

                return [
                    'rule_id'       => $stat->code,
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
        //  - Domain Groups (Branches)
        //    - Rules (Leafs)

        $domains = $this->getRulesByDomain();

        $rootChildren = [];
        foreach ($domains as $idx => $domain) {
            $ruleChildren = [];
            foreach ($domain['rules'] as $rule) {
                $ruleChildren[] = [
                    'id'          => $rule['id'],
                    'name'        => $rule['name'],
                    'type'        => 'rule',
                    'is_terminal' => true,
                    'action_code' => $rule['action'],
                    'priority'    => $rule['priority'],
                    'children'    => [],
                ];
            }

            $rootChildren[] = [
                'id'        => 'domain_' . $idx,
                'name'      => $domain['domain'],
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
