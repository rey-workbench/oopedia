<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\DTOs\Adaptive\AdaptiveActionDTO;
use App\DTOs\Adaptive\AdaptiveRuleDTO;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use Illuminate\Support\Facades\DB;

final class AdaptiveManagementService implements AdaptiveManagementServiceInterface
{
    public function createRule(AdaptiveRuleDTO $dto): AdaptiveRule
    {
        return DB::transaction(function () use ($dto) {
            $this->syncFacts($dto->facts, $dto->deduced_facts);

            return AdaptiveRule::create($dto->toArray());
        });
    }

    public function updateRule(string $id, AdaptiveRuleDTO $dto): AdaptiveRule
    {
        $rule = AdaptiveRule::findOrFail($id);

        return DB::transaction(function () use ($rule, $dto) {
            $this->syncFacts($dto->facts, $dto->deduced_facts);
            $rule->update($dto->toArray());

            return $rule->fresh();
        });
    }

    public function deleteRule(string $id): void
    {
        AdaptiveRule::findOrFail($id)->delete();
    }

    public function createAction(AdaptiveActionDTO $dto): AdaptiveAction
    {
        return AdaptiveAction::create($dto->toArray());
    }

    public function updateAction(string $id, AdaptiveActionDTO $dto): AdaptiveAction
    {
        $action = AdaptiveAction::findOrFail($id);
        $action->update($dto->toArray());

        return $action->fresh();
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
        foreach ($deducedFacts as $fact) {
            $id = $fact['id'] ?? $fact['key'] ?? null;
            if (! empty($id)) {
                AdaptiveFact::updateOrCreate(
                    ['id' => $id],
                    [
                        'name'     => $fact['name'] ?? 'Diagnosa: ' . $id,
                        'category' => 'virtual',
                        'logic'    => null,
                    ],
                );
            }
        }
    }
}
