<?php

namespace App\Contracts\Services;

interface LeaderboardServiceInterface
{
    public function getLeaderboardData(string $currentUserId): array;
}
