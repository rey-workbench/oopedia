<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface DashboardServiceInterface
{
    public function getAllMaterials(): Collection;

    public function getDashboardIndexData(string $userId, bool $isGuest): array;

    public function getInProgressData(string $userId, bool $isGuest): array;

    public function getCompletedData(string $userId, bool $isGuest): array;
}
