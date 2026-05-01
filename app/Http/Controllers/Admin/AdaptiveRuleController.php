<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\AdaptiveAnalyticsServiceInterface;
use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\DTOs\Adaptive\AdaptiveRuleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdaptiveRule\StoreAdaptiveRuleRequest;
use App\Http\Requests\Admin\AdaptiveRule\UpdateAdaptiveRuleRequest;
use App\Models\AdaptiveRule;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class AdaptiveRuleController extends Controller
{
    public function __construct(
        private readonly AdaptiveManagementServiceInterface $adaptiveManagementService,
        private readonly AdaptiveAnalyticsServiceInterface $adaptiveAnalyticsService,
    ) {}

    public function create(): Response
    {
        return $this->render('Admin/AdaptiveRules/Create/Index', [
            'all_facts'   => $this->adaptiveAnalyticsService->getAllFacts(),
            'all_actions' => $this->adaptiveAnalyticsService->getAllActions(),
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
            'all_facts'   => $this->adaptiveAnalyticsService->getAllFacts(),
            'all_actions' => $this->adaptiveAnalyticsService->getAllActions(),
        ]);
    }

    public function index(): Response
    {
        $stats = $this->adaptiveAnalyticsService->getDashboardStats();

        return $this->render('Admin/AdaptiveRules/Index', [
            'total_rules'                 => $stats['total_rules'],
            'total_facts'                 => $stats['total_facts'],
            'total_actions'               => $stats['total_actions'],
            'rules_by_diagnosis'          => $this->adaptiveAnalyticsService->getRulesByDiagnosis(),
            'adaptive_state_distribution' => $this->adaptiveAnalyticsService->getAdaptiveStateDistribution(),
            'recent_triggers'             => $this->adaptiveAnalyticsService->getRecentTriggers(),
            'rule_triggers_stats'         => $this->adaptiveAnalyticsService->getRuleTriggerStats(),
            'decision_tree'               => $this->adaptiveAnalyticsService->getDecisionTree(),
            'all_facts'                   => $this->adaptiveAnalyticsService->getAllFacts(),
            'all_actions'                 => $this->adaptiveAnalyticsService->getAllActions(),
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
}
