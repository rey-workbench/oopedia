<?php

namespace App\Contracts\Services;

/**
 * Contract for the leaderboard data service.
 */
interface LeaderboardServiceInterface
{
    /**
     * Get leaderboard data with the current user's position highlighted.
     *
     * @return array<string, mixed>
     */
    public function getLeaderboardData(int $currentUserId): array;
}
