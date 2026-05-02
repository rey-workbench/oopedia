<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\DTOs\Adaptive\AdaptiveActionDTO;
use App\DTOs\Adaptive\AdaptiveRuleDTO;
use App\Http\Resources\AdaptiveActionResource;
use App\Http\Resources\AdaptiveRuleResource;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use Illuminate\Support\Facades\DB;

final class AdaptiveManagementService implements AdaptiveManagementServiceInterface
{
    public function createRule(AdaptiveRuleDTO $adaptiveRuleDTO): array
    {
        return DB::transaction(function () use ($adaptiveRuleDTO) {
            $this->syncFacts($adaptiveRuleDTO->facts, $adaptiveRuleDTO->deduced_facts);
            $rule = AdaptiveRule::create($adaptiveRuleDTO->toArray());

            return new AdaptiveRuleResource($rule)->resolve();
        });
    }

    public function updateRule(string $id, AdaptiveRuleDTO $adaptiveRuleDTO): array
    {
        $rule = AdaptiveRule::findOrFail($id);

        return DB::transaction(function () use ($rule, $adaptiveRuleDTO) {
            $this->syncFacts($adaptiveRuleDTO->facts, $adaptiveRuleDTO->deduced_facts);
            $rule->update($adaptiveRuleDTO->toArray());

            return new AdaptiveRuleResource($rule->fresh())->resolve();
        });
    }

    public function deleteRule(string $id): void
    {
        AdaptiveRule::findOrFail($id)->delete();
    }

    public function createAction(AdaptiveActionDTO $adaptiveActionDTO): array
    {
        $action = AdaptiveAction::create($adaptiveActionDTO->toArray());

        return new AdaptiveActionResource($action)->resolve();
    }

    public function updateAction(string $id, AdaptiveActionDTO $adaptiveActionDTO): array
    {
        $action = AdaptiveAction::findOrFail($id);
        $action->update($adaptiveActionDTO->toArray());

        return new AdaptiveActionResource($action->fresh())->resolve();
    }

    public function deleteAction(string $id): void
    {
        $action = AdaptiveAction::findOrFail($id);

        if ($action->rules()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus aksi yang masih digunakan oleh aturan.');
        }

        $action->delete();
    }

    public function syncFacts(array $facts, array $deducedFacts = []): void
    {
        $keys = AdaptiveConditionKeys::class;

        // 1. Sync from WHEN blocks (facts array)
        foreach ($facts as $fact) {
            $id = $fact['id'] ?? $fact['key'] ?? null;
            if (! $id) {
                continue;
            }

            $name  = $fact['name'] ?? 'Fakta: ' . $id;
            $logic = json_encode([
                $keys::OP  => $fact['operator'] ?? $keys::OP_EQ,
                $keys::VAL => $fact['value']    ?? 1,
                $keys::KEY => $fact['key']      ?? $id,
            ]);

            AdaptiveFact::updateOrCreate(
                ['id' => $id],
                [
                    'name'     => $name,
                    'category' => 'primary',
                    'logic'    => $logic,
                ],
            );
        }

        // 2. Sync from DEDUCE blocks (Virtual Facts / Diagnoses)
        foreach ($deducedFacts as $deducedFact) {
            $id = $deducedFact['id'] ?? $deducedFact['key'] ?? null;
            if (! empty($id)) {
                AdaptiveFact::updateOrCreate(
                    ['id' => $id],
                    [
                        'name'     => $deducedFact['name'] ?? 'Diagnosa: ' . $id,
                        'category' => 'virtual',
                        'logic'    => null,
                    ],
                );
            }
        }
    }
}
