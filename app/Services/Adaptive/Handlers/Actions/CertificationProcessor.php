<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Rules\Adaptive\Constants\ActionConstants;

final class CertificationProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        $cert = $instructions[ActionConstants::KEY_CERTIFICATION] ?? null;

        if (!$cert) {
            return $state;
        }

        // If certification is just a boolean true, we tie it to the current module
        $certificateId = ($cert === true) ? ($context['module_id'] ?? null) : $cert;

        if (!$certificateId) {
            return $state;
        }

        $certs = $state['certifications'] ?? [];
        if (!in_array($certificateId, $certs, true)) {
            $certs[] = $certificateId;
            $state['certifications'] = array_values(array_unique($certs));
        }

        return $state;
    }
}
