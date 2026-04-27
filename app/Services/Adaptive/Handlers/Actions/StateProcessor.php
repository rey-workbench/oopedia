<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Enums\Lms\StudentLevel;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class StateProcessor implements ActionProcessorInterface
{
    private const METADATA_KEYS = [
        ActionConstants::KEY_FLOW,
        ActionConstants::KEY_BADGES,
        ActionConstants::KEY_MESSAGE,
        ActionConstants::KEY_TITLE,
        ActionConstants::KEY_CERTIFICATION
    ];

    public function process(array $instructions, array $state, array $context): array
    {
        $xpChanged = false;

        foreach ($instructions as $key => $value) {
            if (in_array($key, self::METADATA_KEYS, true)) {
                continue;
            }

            // Support increments like "+10" or "-5"
            if (is_string($value) && (str_starts_with($value, '+') || str_starts_with($value, '-'))) {
                $currentValue = data_get($state, $key, 0);
                data_set($state, $key, (int) $currentValue + (int) $value);
            } else {
                data_set($state, $key, $value);
            }

            if ($key === StudentStateSchema::GLOBAL_XP) {
                $xpChanged = true;
            }
        }

        // Auto-update Level if XP changed
        if ($xpChanged) {
            $state[StudentStateSchema::CURRENT_LEVEL] = StudentLevel::fromXp((int) ($state[StudentStateSchema::GLOBAL_XP] ?? 0))->value;
        }

        return $state;
    }
}
