<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface MaterialViewServiceInterface
{
    /** @return Collection<int, \App\Models\Material> */
    public function getMaterialsList(int|string|null $userId, bool $isGuest): Collection;

    /** @return array<string, mixed> */
    public function getMaterialDetail(int $materialId, int|string|null $userId, bool $isGuest): array;

    /** @return array<string, mixed> */
    public function getSubMaterialDetail(int $materialId, int $subMaterialId, bool $isGuest): array;

    public function resetMaterialProgress(int|string $userId, int $materialId): void;
}
