<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface MaterialViewServiceInterface
{
    public function getMaterialsList(?string $userId, bool $isGuest): Collection;

    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest, array $guestProgress = []): array;

    public function getSubMaterialDetail(string $materialId, string $subMaterialId, bool $isGuest): array;

    public function resetMaterialProgress(string $userId, string $materialId): void;
}
