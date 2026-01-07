<?php

namespace App\Repositories\Interfaces;

interface ProgressRepositoryInterface
{
    public function getUserProgressStats($userId);
    public function getUserMaterialProgress($userId);
    public function getDetailedUserProgress($userId);
    public function getRecentActivities($userId, $limit = 5);
    public function getCorrectAnswersWithAttempts($roleId = 3);
    public function getLeaderboardStats($roleId = 3);
}
