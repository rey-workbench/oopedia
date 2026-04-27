<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Rules\Adaptive\Constants\ActionConstants;

final class CertificationProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        $tier = $instructions[ActionConstants::KEY_CERTIFICATION] ?? null;

        if (!$tier) {
            return $state;
        }

        $materialId = $context['material_id'] ?? null;
        if (!$materialId) {
            return $state;
        }

        $certs = $state['certifications'] ?? [];
        $certs[(string) $materialId] = strtolower($tier);
        $state['certifications'] = $certs;

        return $state;
    }
}
