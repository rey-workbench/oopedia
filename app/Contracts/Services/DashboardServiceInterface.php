<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for the student-facing dashboard service.
 */
interface DashboardServiceInterface
{
    /**
     * Get all available materials.
     *
     * @return Collection<int, \App\Models\Material>
     */
    public function getAllMaterials(): Collection;

    /**
     * Get data for the dashboard index page.
     *
     * @return array<string, mixed>
     */
    public function getDashboardIndexData(int|string $userId, bool $isGuest): array;

    /**
     * Get materials that are currently in-progress for the user.
     *
     * @return array<string, mixed>
     */
    public function getInProgressData(int|string $userId, bool $isGuest): array;

    /**
     * Get materials completed by the user.
     *
     * @return array<string, mixed>
     */
    public function getCompletedData(int|string $userId, bool $isGuest): array;
}
