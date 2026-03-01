<?php

namespace App\Services\Gamification;

/**
 * LevelingService
 *
 * Handles level calculation and leveling logic.
 */
class LevelingService
{
    /**
     * Determine level based on XP
     */
    public function determineLevel(int $xp): string
    {
        if ($xp >= 500) {
            return 'Ahli';
        }
        if ($xp >= 200) {
            return 'Mahir';
        }
        if ($xp >= 50) {
            return 'Menengah';
        }

        return 'Pemula';
    }

    /**
     * Get level progress (percentage to next level)
     */
    public function getLevelProgress(int $xp): array
    {
        $levels = [
            ['name' => 'Pemula', 'min' => 0],
            ['name' => 'Menengah', 'min' => 50],
            ['name' => 'Mahir', 'min' => 200],
            ['name' => 'Ahli', 'min' => 500],
        ];

        $currentLevel = $this->determineLevel($xp);
        $currentIndex = 0;
        foreach ($levels as $index => $level) {
            if ($level['name'] === $currentLevel) {
                $currentIndex = $index;
                break;
            }
        }

        $nextLevel = $levels[$currentIndex + 1] ?? null;

        if (! $nextLevel) {
            return [
                'current_level' => $currentLevel,
                'next_level'    => null,
                'percentage'    => 100,
                'xp_needed'     => 0,
            ];
        }

        $currentMin    = $levels[$currentIndex]['min'];
        $nextMin       = $nextLevel['min'];
        $progressXp    = $xp      - $currentMin;
        $totalXpNeeded = $nextMin - $currentMin;

        return [
            'current_level' => $currentLevel,
            'next_level'    => $nextLevel['name'],
            'percentage'    => round(($progressXp / $totalXpNeeded) * 100),
            'xp_needed'     => $nextMin - $xp,
        ];
    }
}
