<?php

namespace App\Contracts\Services;

interface LeaderboardServiceInterface
{
    public function getLeaderboardData($currentUserId);
}
