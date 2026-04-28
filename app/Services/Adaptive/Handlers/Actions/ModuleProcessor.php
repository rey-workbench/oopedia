<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Models\Material;
use App\Rules\Adaptive\Constants\ActionConstants;

final class ModuleProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        $flow = $instructions[ActionConstants::KEY_FLOW] ?? null;

        if ($flow !== ActionConstants::FLOW_FINISH) {
            return $state;
        }

        $materialId = $context['material_id'] ?? null;
        if (! $materialId) {
            return $state;
        }

        $material     = Material::find($materialId);
        $nextMaterial = $material?->getNextMaterial();

        if ($nextMaterial && $nextMaterial->module_id) {
            $unlocked = $state['unlocked_modules'] ?? [];
            if (! in_array((string) $nextMaterial->module_id, $unlocked, true)) {
                $unlocked[]                = (string) $nextMaterial->module_id;
                $state['unlocked_modules'] = array_values(array_unique($unlocked));
            }
        }

        return $state;
    }
}
