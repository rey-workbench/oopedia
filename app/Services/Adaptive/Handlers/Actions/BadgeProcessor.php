<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class BadgeProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        $badges = $instructions[ActionConstants::KEY_BADGES] ?? null;

        if (!is_array($badges)) {
            return $state;
        }

        $current = $state[StudentStateSchema::BADGES] ?? [];
        $state[StudentStateSchema::BADGES] = array_values(array_unique(array_merge($current, $badges)));

        return $state;
    }
}
