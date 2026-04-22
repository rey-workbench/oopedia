<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveExecutionLog;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Models\StudentState;
use Inertia\Response;

final class AdaptiveAnalyticsController extends Controller
{
    public function index(): Response
    {
        $totalRules   = AdaptiveRule::count();
        $totalFacts   = AdaptiveFact::count();
        $totalActions = AdaptiveAction::count();

        $rulesByDomain = $this->getRulesByDomainFromDb();

        $adaptiveStateDistribution = $this->getAdaptiveStateDistribution();

        $recentTriggers = $this->getRecentTriggers();

        $ruleTriggersStats = $this->getRuleTriggersStats();

        return $this->render('Admin/AdaptiveAnalytics/Index', [
            'totalRules'                => $totalRules,
            'totalFacts'                => $totalFacts,
            'totalActions'              => $totalActions,
            'rulesByDomain'             => $rulesByDomain,
            'adaptiveStateDistribution' => $adaptiveStateDistribution,
            'recentTriggers'            => $recentTriggers,
            'ruleTriggersStats'         => $ruleTriggersStats,
            'decisionTree'              => $this->getDecisionTreeData(),
        ]);
    }

    private function countFacts(): int
    {
        return AdaptiveFact::count();
    }

    private function countActions(): int
    {
        return AdaptiveAction::count();
    }

    private function getRulesByDomainFromDb(): array
    {
        $rules = AdaptiveRule::with('action')->ordered()->get();

        $domains = [
            'Safety'      => [],
            'Project'     => [],
            'Achievement' => [],
            'Recovery'    => [],
            'Progression' => [],
        ];

        foreach ($rules as $rule) {
            $domain             = $this->categorizeRule($rule->rule_code);
            $domains[$domain][] = [
                'id'       => $rule->rule_code,
                'name'     => $rule->name,
                'priority' => $rule->priority,
                'action'   => $rule->action?->code ?? 'H00',
            ];
        }

        $result = [];
        foreach ($domains as $name => $ruleList) {
            if (empty($ruleList)) {
                continue;
            }

            $result[] = [
                'domain' => $name,
                'count'  => count($ruleList),
                'rules'  => $ruleList,
            ];
        }

        return $result;
    }

    private function categorizeRule(string $code): string
    {
        if (in_array($code, ['R03', 'R04', 'R05', 'R06', 'R14'])) {
            return 'Safety';
        }
        if (in_array($code, ['R01', 'R02', 'R07', 'R08'])) {
            return 'Project';
        }
        if (in_array($code, ['R09', 'R10', 'R11'])) {
            return 'Achievement';
        }
        if (in_array($code, ['R12', 'R13'])) {
            return 'Recovery';
        }

        return 'Progression';
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
                'rule_id'        => $log->rule_code,
                'rule_name'      => AdaptiveRule::where('rule_code', $log->rule_code)->value('name') ?? $log->rule_code,
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

        return AdaptiveExecutionLog::select('rule_code', \DB::raw('count(*) as count'))
            ->groupBy('rule_code')
            ->orderByDesc('count')
            ->get()
            ->map(function ($stat) use ($totalLogs) {
                $rule = AdaptiveRule::where('rule_code', $stat->rule_code)->first();

                return [
                    'rule_id'       => $stat->rule_code,
                    'rule_name'     => $rule?->name ?? 'Legacy Rule',
                    'trigger_count' => $stat->count,
                    'percentage'    => round(($stat->count / $totalLogs) * 100, 1),
                ];
            })
            ->toArray();
    }

    public function getDecisionTreeData(): array
    {
        // Construct a virtual tree for visualization
        // Root: Fact Gathering
        //  - Domain Groups (Branches)
        //    - Rules (Leafs)

        $domains = $this->getRulesByDomainFromDb();

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
