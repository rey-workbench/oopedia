<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Repositories\AdaptiveActionRepositoryInterface;
use App\Contracts\Repositories\AdaptiveFactRepositoryInterface;
use App\Contracts\Repositories\AdaptiveRuleRepositoryInterface;
use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\DTOs\Adaptive\AdaptiveActionDTO;
use App\DTOs\Adaptive\AdaptiveRuleDTO;
use App\Http\Resources\AdaptiveActionResource;
use App\Http\Resources\AdaptiveRuleResource;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use Illuminate\Support\Facades\DB;

final readonly class AdaptiveManagementService implements AdaptiveManagementServiceInterface
{
    public function __construct(
        private AdaptiveRuleRepositoryInterface $adaptiveRuleRepository,
        private AdaptiveActionRepositoryInterface $adaptiveActionRepository,
        private AdaptiveFactRepositoryInterface $adaptiveFactRepository,
    ) {}

    public function createRule(AdaptiveRuleDTO $adaptiveRuleDTO): array
    {
        return DB::transaction(function () use ($adaptiveRuleDTO) {
            $this->syncFacts($adaptiveRuleDTO->facts, $adaptiveRuleDTO->deduced_facts);
            $rule = $this->adaptiveRuleRepository->create($adaptiveRuleDTO->toArray());

            return new AdaptiveRuleResource($rule)->resolve();
        });
    }

    public function updateRule(string $id, AdaptiveRuleDTO $adaptiveRuleDTO): array
    {
        $rule = $this->adaptiveRuleRepository->find($id);

        if (! $rule instanceof AdaptiveRule) {
            abort(404, 'Aturan tidak ditemukan.');
        }

        return DB::transaction(function () use ($rule, $adaptiveRuleDTO) {
            $this->syncFacts($adaptiveRuleDTO->facts, $adaptiveRuleDTO->deduced_facts);
            $rule->update($adaptiveRuleDTO->toArray());

            return new AdaptiveRuleResource($rule->fresh())->resolve();
        });
    }

    public function deleteRule(string $id): void
    {
        $rule = $this->adaptiveRuleRepository->find($id);

        if (! $rule instanceof AdaptiveRule) {
            abort(404, 'Aturan tidak ditemukan.');
        }

        $this->adaptiveRuleRepository->delete($id);
    }

    public function createAction(AdaptiveActionDTO $adaptiveActionDTO): array
    {
        $action = $this->adaptiveActionRepository->create($adaptiveActionDTO->toArray());

        return new AdaptiveActionResource($action)->resolve();
    }

    public function updateAction(string $id, AdaptiveActionDTO $adaptiveActionDTO): array
    {
        $action = $this->adaptiveActionRepository->update($id, $adaptiveActionDTO->toArray());

        if (! $action instanceof AdaptiveAction) {
            abort(404, 'Aksi tidak ditemukan.');
        }

        return new AdaptiveActionResource($action)->resolve();
    }

    public function deleteAction(string $id): void
    {
        $action = $this->adaptiveActionRepository->find($id);

        if (! $action instanceof AdaptiveAction) {
            abort(404, 'Aksi tidak ditemukan.');
        }

        if ($action->rules()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus aksi yang masih digunakan oleh aturan.');
        }

        $this->adaptiveActionRepository->delete($id);
    }

    public function syncFacts(array $facts, array $deducedFacts = []): void
    {
        $keys = AdaptiveConditionKeys::class;

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

            $this->adaptiveFactRepository->updateOrCreate(
                ['id' => $id],
                [
                    'name'     => $name,
                    'category' => 'primary',
                    'logic'    => $logic,
                ],
            );
        }

        foreach ($deducedFacts as $deducedFact) {
            $id = $deducedFact['id'] ?? $deducedFact['key'] ?? null;
            if (! empty($id)) {
                $this->adaptiveFactRepository->updateOrCreate(
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
