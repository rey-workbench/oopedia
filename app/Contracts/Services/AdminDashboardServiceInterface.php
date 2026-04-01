<?php

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface AdminDashboardServiceInterface
{
    /** @return array<string, mixed> */
    public function getDashboardStats(): array;

    /** @return Collection<int, QuizAttempt> */
    public function getRecentProgress(int $limit = 10): Collection;

    /** @return array<int, array<string, mixed>> */
    public function getStudentProgressOverview(int $limit = 5): array;

    /** @return SupportCollection<int, array<string, mixed>> */
    public function getMaterialStatistics(): SupportCollection;

    /** @return Collection<int, Material> */
    public function getPopularMaterials(int $limit = 5): Collection;

    /** @return array<string, mixed> */
    public function getStudentAnalytics(): array;
}
