<?php

namespace App\Contracts\Services;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;

interface MaterialViewServiceInterface
{
    /** @return Collection<int, Material> */
    public function getMaterialsList(?string $userId, bool $isGuest): Collection;

    /** @return array<string, mixed> */
    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest): array;

    /** @return array<string, mixed> */
    public function getSubMaterialDetail(string $materialId, string $subMaterialId, bool $isGuest): array;

    public function resetMaterialProgress(string $userId, string $materialId): void;
}
