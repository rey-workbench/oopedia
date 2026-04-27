<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Rules\Adaptive\Constants\ActionConstants;

final class BadgeProcessor implements ActionProcessorInterface
{
    public function process(array $instructions, array $state, array $context): array
    {
        $badges = $instructions[ActionConstants::KEY_BADGES] ?? null;

        if (!$badges) {
            return $state;
        }

        $newBadges = is_array($badges) ? $badges : [$badges];
        $currentBadges = $state['gamification_data']['badges'] ?? [];

        $updatedBadges = array_values(array_unique(array_merge($currentBadges, $newBadges)));
        $state['gamification_data']['badges'] = $updatedBadges;

        return $state;
    }
}
