<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface AdminDashboardServiceInterface
{
    public function getDashboardStats(): array;

    public function getRecentProgress(int $limit = 10): Collection;

    public function getStudentProgressOverview(int $limit = 5): array;

    public function getMaterialStatistics(): SupportCollection;

    public function getPopularMaterials(int $limit = 5): Collection;

    public function getStudentAnalytics(): array;
}
