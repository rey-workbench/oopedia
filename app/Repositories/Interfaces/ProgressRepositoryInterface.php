<?php

namespace App\Repositories\Interfaces;

interface ProgressRepositoryInterface
{
    public function getUserProgressStats($userId);
    public function getUserMaterialProgress($userId);
    public function getDetailedUserProgress($userId);
    public function getRecentActivities($userId, $limit = 5);
}
