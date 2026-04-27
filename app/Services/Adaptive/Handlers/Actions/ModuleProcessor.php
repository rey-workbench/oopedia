<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Models\Material;
use App\Rules\Adaptive\Constants\ActionConstants;

final class ModuleProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        $nextAction = $instructions[ActionConstants::KEY_NEXT_ACTION] ?? null;

        if ($nextAction !== ActionConstants::FINISH_MATERIAL) {
            return $state;
        }

        $materialId = $context['material_id'] ?? null;
        if (!$materialId) {
            return $state;
        }

        $material = Material::find($materialId);
        $nextMaterial = $material?->getNextMaterial();

        if ($nextMaterial && $nextMaterial->module_id) {
            $unlocked = $state['unlocked_modules'] ?? [];
            if (!in_array((string) $nextMaterial->module_id, $unlocked, true)) {
                $unlocked[] = (string) $nextMaterial->module_id;
                $state['unlocked_modules'] = array_values(array_unique($unlocked));
            }
        }

        return $state;
    }
}
