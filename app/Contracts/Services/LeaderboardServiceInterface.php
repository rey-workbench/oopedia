<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface LeaderboardServiceInterface
{
    public function getLeaderboardData(string $currentUserId): array;
}
